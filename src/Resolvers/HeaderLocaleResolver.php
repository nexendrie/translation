<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nexendrie\Translation\Loader;
use Nette\Http\IRequest;
use Nexendrie\Translation\LoaderNotSetException;
use Nette\Http\RequestFactory;
use Nexendrie\Translation\LoaderAwareLocaleResolver;

/**
 * HeaderLocaleResolver
 *
 * @author Jakub Konečný
 */
final class HeaderLocaleResolver implements LoaderAwareLocaleResolver {
  use \Nette\SmartObject;

  private ?Loader $loader = null;
  private IRequest $request;
  
  public function __construct(IRequest $request = null) {
    if($request === null) {
      $request = (new RequestFactory())->fromGlobals();
    }
    $this->request = $request;
  }
  
  public function setLoader(Loader $loader): void {
    $this->loader = $loader;
  }
  
  /**
   * Resolve language
   *
   * Taken from Nette\Http\Request::detectLanguage()
   * @author David Grudl
   */
  public function resolve(): ?string {
    if($this->loader === null) {
      throw new LoaderNotSetException("Loader is not available, cannot detect possible languages.");
    }
    $header = $this->request->getHeader("Accept-Language");
    $langs = $this->loader->getAvailableLanguages();
    if($header === null) {
      return null;
    }
    $s = strtolower($header);  // case insensitive
    $s = strtr($s, '_', '-');  // cs_CZ means cs-CZ
    rsort($langs);             // first more specific
    $pattern = ')(?:-[^\s,;=]+)?\s*(?:;\s*q=([0-9.]+))?#';
    preg_match_all('#(' . implode('|', $langs) . $pattern, $s, $matches);
    if(!isset($matches[0])) {
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