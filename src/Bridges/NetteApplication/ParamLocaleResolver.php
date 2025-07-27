<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteApplication;

use Nette\Application\Application;
use Nette\Application\Request;

/**
 * ParamLocaleResolver
 *
 * @author Jakub Konečný
 */
final class ParamLocaleResolver implements AppRequestAwareLocaleResolver {
  use \Nette\SmartObject;

  private ?Request $request = null;
  /** @var string */
  public string $param = "locale";

  /**
   * @deprecated Access the property directly
   */
  public function getParam(): string {
    return $this->param;
  }

  /**
   * @deprecated Access the property directly
   */
  public function setParam(string $param): void {
    $this->param = $param;
  }
  
  public function onRequest(Application $application, Request $request): void {
    $locale = $request->getParameter($this->param);
    if($request->method === Request::FORWARD && $locale === null) {
      return;
    }
    $this->request = $request;
  }
  
  public function resolve(): ?string {
    return $this->request?->getParameter($this->param);
  }
}
?>