<?php
namespace Nexendrie\Translation\Resolvers;

use Nexendrie\Translation\Loaders\ILoader;

/**
 * ILoaderAwareLocaleResolver
 *
 * @author Jakub Konečný
 */
interface ILoaderAwareLocaleResolver extends ILocaleResolver {
  /**
   * Inject loader
   *
   * @param ILoader $loader
   */
  function setLoader(ILoader $loader);
}
?>