<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteApplication;

use Nexendrie\Translation\LocaleResolver;
use Nette\Application\Application;
use Nette\Application\Request;

/**
 * AppRequestAwareLocaleResolver
 *
 * @author Jakub Konečný
 */
interface AppRequestAwareLocaleResolver extends LocaleResolver
{
    public function onRequest(Application $application, Request $request): void;
}
