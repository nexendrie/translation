<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteApplication;

use Nette\Application\Application,
    Nette\Application\Request;

/**
 * ParamLocaleResolver
 *
 * @author Jakub Konečný
 * @property string $param
 */
class ParamLocaleResolver implements IAppRequestAwareLocaleResolver {
  use \Nette\SmartObject;
  
  /** @var Request */
  protected $request;
  /** @var string */
  protected $param = "locale";
  
  function getParam(): string {
    return $this->param;
  }
  
  function setParam(string $param) {
    $this->param = $param;
  }
  
  function onRequest(Application $application, Request $request): void {
    $locale = $request->getParameter($this->param);
    if($request->method === Request::FORWARD AND is_null($locale)) {
      return;
    }
    $this->request = $request;
  }
  
  /**
   * Resolve language
   */
  function resolve(): ?string {
    if(!is_null($this->request)) {
      return $this->request->getParameter($this->param);
    }
    return NULL;
  }
}
?>