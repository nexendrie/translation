<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

/**
 * LoaderAwareLocaleResolver
 *
 * @author Jakub Konečný
 */
interface LoaderAwareLocaleResolver extends LocaleResolver
{
    public function setLoader(Loader $loader): void;
}
