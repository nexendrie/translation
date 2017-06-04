<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteHttp;

use Nexendrie\Translation\Resolvers\ILocaleResolver,
    Nette\Http\Session,
    Nette\Http\SessionSection;

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
  
  function __construct(Session $session) {
    $this->session = $session;
    $this->section = $session->getSection(get_class($this));
    $this->section->lang = NULL;
  }
  
  /**
   * @return string|NULL
   */
  function getLang(): ?string {
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