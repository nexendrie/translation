<?php
namespace Nexendrie\Translation\Resolvers;

use Nette\Application\Application,
    Nette\Application\Request;

/**
 * IAppRequestAwareLocaleResolver
 *
 * @author Jakub Konečný
 */
interface IAppRequestAwareLocaleResolver extends ILocaleResolver {
  function onRequest(Application $application, Request $request);
}
?>