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
 */
final class SessionLocaleResolver implements ISettableLocaleResolver {
  use \Nette\SmartObject;

  private Session $session;
  private SessionSection $section;
  public string $varName = "lang";
  
  public function __construct(Session $session = null) {
    if($session === null) {
      $request = (new RequestFactory())->fromGlobals();
      $response = new Response();
      $session = new Session($request, $response);
    }
    $this->session = $session;
    $this->section = $session->getSection(get_class($this));
  }

  /**
   * @deprecated Access the property directly
   */
  public function getLang(): ?string {
    $lang = (string) $this->section->{$this->varName};
    if($lang === '') {
      return null;
    }
    return $lang;
  }

  /**
   * @deprecated Access the property directly
   */
  public function setLang(?string $lang): void {
    $this->section->{$this->varName} = $lang;
  }

  /**
   * @deprecated Access the property directly
   */
  public function getVarName(): string {
    return $this->varName;
  }

  /**
   * @deprecated Access the property directly
   */
  public function setVarName(string $varName): void {
    $this->varName = $varName;
  }
  
  public function resolve(): ?string {
    return $this->getLang();
  }
}
?>