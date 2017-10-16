<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nexendrie\Translation\Loaders\ILoader;

/**
 * ILoaderAwareLocaleResolver
 *
 * @author Jakub Konečný
 */
interface ILoaderAwareLocaleResolver extends ILocaleResolver {
  public function setLoader(ILoader $loader): void;
}
?>