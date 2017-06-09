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
  
  /**
   * @return string
   */
  function getParam(): string {
    return $this->param;
  }
  
  /**
   * @param string $param
   */
  function setParam(string $param) {
    $this->param = $param;
  }
  
  /**
   * @param Application $application
   * @param Request $request
   * @return void
   */
  function onRequest(Application $application, Request $request) {
    $locale = $request->getParameter($this->param);
    if($request->method === Request::FORWARD and is_null($locale)) {
      return;
    }
    $this->request = $request;
  }
  
  /**
   * Resolve language
   *
   * @return string|NULL
   */
  function resolve(): ?string {
    if(is_null($this->request)) {
      return NULL;
    } else {
      return $this->request->getParameter($this->param);
    }
  }
}
?>