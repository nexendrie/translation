<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

/**
 * ILoader
 *
 * @author Jakub Konečný
 */
interface ILoader {
  /**
   * @return string
   */
  function getLang();
  
  /**
   * @param string $lang
   */
  function setLang($lang);
  
  /**
   * @return string
   */
  function getDefaultLang();
  
  /**
   * @param string $defaultLang
   */
  function setDefaultLang($defaultLang);
  
  /**
   * @return array
   */
  function getResources();
  
  /**
   * @return array
   */
  function getTexts();
  
  /**
   * @return string
   */
  function getResolverName();
  
  /**
   * @return string[]
   */
  function getAvailableLanguages();
}
?>