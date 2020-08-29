<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Http\RequestFactory;
use Nette\Http\Response;
use Nexendrie\Translation\ISettableLocaleResolver;

/**
 * SessionLocaleResolver
 *
 * @author Jakub Konečný
 * @property string|null $lang
 * @property string $varName
 */
final class SessionLocaleResolver implements ISettableLocaleResolver {
  use \Nette\SmartObject;
  
  /** @var Session */
  protected $session;
  /** @var SessionSection */
  protected $section;
  /** @var string */
  protected $varName = "lang";
  
  public function __construct(Session $session = null) {
    if($session === null) {
      $request = (new RequestFactory())->fromGlobals();
      $response = new Response();
      $session = new Session($request, $response);
    }
    $this->session = $session;
    $this->section = $session->getSection(get_class($this));
  }
  
  public function getLang(): ?string {
    $lang = $this->section->{$this->varName};
    if(empty($lang)) {
      return null;
    }
    return $lang;
  }
  
  public function setLang(?string $lang): void {
    $this->section->{$this->varName} = $lang;
  }
  
  public function getVarName(): string {
    return $this->varName;
  }
  
  public function setVarName(string $varName): void {
    $this->varName = $varName;
  }
  
  public function resolve(): ?string {
    return $this->getLang();
  }
}
?>