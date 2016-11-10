<?php
namespace Nexendrie\Translation\Resolvers;

/**
 * FallbackLocaleResolver
 *
 * @author Jakub Konečný
 */
class FallbackLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  /**
   * Resolve language
   *
   * @return NULL
   */
  function resolve() {
    return NULL;
  }
}
?>