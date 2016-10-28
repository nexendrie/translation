<?php
namespace Nexendrie\Translation\Resolvers;

/**
 * ILocaleResolver
 *
 * @author Jakub Konečný
 * @property string $defaultLang
 */
interface ILocaleResolver {
  /**
   * Resolve language
   *
   * @return string
   */
  function resolve();
  
  /**
   * @return string
   */
  function getDefaultLang();
  
  /**
   * Set default language
   *
   * @param string $default
   */
  function setDefaultLang($default);
}
?>