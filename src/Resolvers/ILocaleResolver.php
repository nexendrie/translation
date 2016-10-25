<?php
namespace Nexendrie\Translation\Resolvers;

/**
 * ILocaleResolver
 *
 * @author Jakub Konečný
 */
interface ILocaleResolver {
  /**
   * Resolve language
   *
   * @return string
   */
  function resolve();
}
?>