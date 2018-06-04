<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteApplication;

use Nexendrie\Translation\Resolvers\ILocaleResolver;
use Nette\Application\Application;
use Nette\Application\Request;

/**
 * IAppRequestAwareLocaleResolver
 *
 * @author Jakub Konečný
 */
interface IAppRequestAwareLocaleResolver extends ILocaleResolver {
  public function onRequest(Application $application, Request $request);
}
?>