<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteApplication;

use Nette\Application\Application;
use Nette\Application\Request;

/**
 * ParamLocaleResolver
 *
 * @author Jakub Konečný
 * @property string $param
 */
final class ParamLocaleResolver implements IAppRequestAwareLocaleResolver {
  use \Nette\SmartObject;
  
  /** @var Request|null */
  protected $request = null;
  /** @var string */
  protected $param = "locale";
  
  public function getParam(): string {
    return $this->param;
  }
  
  public function setParam(string $param): void {
    $this->param = $param;
  }
  
  public function onRequest(Application $application, Request $request): void {
    $locale = $request->getParameter($this->param);
    if($request->method === Request::FORWARD AND is_null($locale)) {
      return;
    }
    $this->request = $request;
  }
  
  public function resolve(): ?string {
    if(!is_null($this->request)) {
      return $this->request->getParameter($this->param);
    }
    return null;
  }
}
?>