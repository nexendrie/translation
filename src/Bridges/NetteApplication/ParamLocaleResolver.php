<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteApplication;

use Nexendrie\Translation\Resolvers\IAppRequestAwareLocaleResolver,
    Nette\Application\Application,
    Nette\Application\Request;

/**
 * ParamLocaleResolver
 *
 * @author Jakub Konečný
 */
class ParamLocaleResolver implements IAppRequestAwareLocaleResolver {
  use \Nette\SmartObject;
  
  /** @var Request */
  protected $request;
  
  /**
   * @param Application $application
   * @param Request $request
   * @return void
   */
  function onRequest(Application $application, Request $request) {
    $locale = $request->getParameter("locale");
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
      return $this->request->getParameter("locale");
    }
  }
}
?>