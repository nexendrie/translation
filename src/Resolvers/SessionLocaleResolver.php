<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nette\Http\Session,
    Nette\Http\SessionSection,
    Nette\Http\RequestFactory,
    Nette\Http\Response;

/**
 * SessionLocaleResolver
 *
 * @author Jakub Konečný
 * @property string|NULL $lang
 * @property string $varName
 */
class SessionLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  /** @var Session */
  protected $session;
  /** @var SessionSection */
  protected $section;
  /** @var string */
  protected $varName = "lang";
  
  function __construct(Session $session = NULL) {
    if(is_null($session)) {
      $request = (new RequestFactory)->createHttpRequest();
      $response = new Response;
      $session = new Session($request, $response);
    }
    $this->session = $session;
    $this->section = $session->getSection(get_class($this));
  }
  
  function getLang(): ?string {
    if(empty($this->section->{$this->varName})) {
      return NULL;
    }
    return $this->section->{$this->varName};
  }
  
  function setLang(string $lang) {
    $this->section->{$this->varName} = $lang;
  }
  
  function getVarName(): string {
    return $this->varName;
  }
  
  function setVarName(string $varName) {
    $this->varName = $varName;
  }
  
  /**
   * Resolve language
   */
  function resolve(): ?string {
    return $this->getLang();
  }
}
?>