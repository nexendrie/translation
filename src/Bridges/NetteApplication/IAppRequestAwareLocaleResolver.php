<?php
namespace Nexendrie\Translation\Bridges\NetteApplication;

use Nexendrie\Translation\Resolvers\ILocaleResolver,
    Nette\Application\Application,
    Nette\Application\Request;

/**
 * IAppRequestAwareLocaleResolver
 *
 * @author Jakub Konečný
 */
interface IAppRequestAwareLocaleResolver extends ILocaleResolver {
  public function onRequest(Application $application, Request $request);
}
?>