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
 */
class SessionLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  /** @var Session */
  protected $session;
  /** @var SessionSection */
  protected $section;
  
  function __construct(Session $session = NULL) {
    if(is_null($session)) {
      $request = (new RequestFactory)->createHttpRequest();
      $response = new Response;
      $session = new Session($request, $response);
    }
    $this->session = $session;
    $this->section = $session->getSection(get_class($this));
  }
  
  /**
   * @return string|NULL
   */
  function getLang(): ?string {
    if(empty($this->section->lang)) {
      return NULL;
    }
    return $this->section->lang;
  }
  
  function setLang(string $lang) {
    $this->section->lang = $lang;
  }
  
  /**
   * Resolve language
   *
   * @return string|NULL
   */
  function resolve(): ?string {
    return $this->getLang();
  }
}
?>