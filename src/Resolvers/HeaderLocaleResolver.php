<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nexendrie\Translation\Loaders\ILoader;
use Nette\Http\IRequest;
use Nexendrie\Translation\LoaderNotSetException;
use Nette\Http\RequestFactory;

/**
 * HeaderLocaleResolver
 *
 * @author Jakub Konečný
 */
class HeaderLocaleResolver implements ILoaderAwareLocaleResolver {
  use \Nette\SmartObject;
  
  /** @var ILoader|null */
  protected $loader = null;
  /** @var IRequest */
  protected $request;
  
  public function __construct(IRequest $request = null) {
    if(is_null($request)) {
      $request = (new RequestFactory())->createHttpRequest();
    }
    $this->request = $request;
  }
  
  public function setLoader(ILoader $loader): void {
    $this->loader = $loader;
  }
  
  /**
   * Resolve language
   *
   * Taken from Nette\Http\Request::detectLanguage()
   * @author David Grudl
   */
  public function resolve(): ?string {
    if(is_null($this->loader)) {
      throw new LoaderNotSetException("Loader is not available, cannot detect possible languages.");
    }
    $header = $this->request->getHeader("Accept-Language");
    $langs = $this->loader->getAvailableLanguages();
    if(is_null($header)) {
      return null;
    }
    $s = strtolower($header);  // case insensitive
    $s = strtr($s, '_', '-');  // cs_CZ means cs-CZ
    rsort($langs);             // first more specific
    $pattern = ')(?:-[^\s,;=]+)?\s*(?:;\s*q=([0-9.]+))?#';
    preg_match_all('#(' . implode('|', $langs) . $pattern, $s, $matches);
    if(!$matches[0]) {
      return null;
    }
    $max = 0;
    $lang = null;
    foreach($matches[1] as $key => $value) {
      $q = ($matches[2][$key] === '') ? 1.0 : (float) $matches[2][$key];
      if($q > $max) {
        $max = $q;
        $lang = $value;
      }
    }
    return $lang;
  }
}
?>