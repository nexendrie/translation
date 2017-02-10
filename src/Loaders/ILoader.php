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
  function getLang(): string;
  
  /**
   * @param string $lang
   */
  function setLang(string $lang);
  
  /**
   * @return string
   */
  function getDefaultLang(): string;
  
  /**
   * @param string $defaultLang
   */
  function setDefaultLang(string $defaultLang);
  
  /**
   * @return array
   */
  function getResources(): array;
  
  /**
   * @return array
   */
  function getTexts(): array;
  
  /**
   * @return string
   */
  function getResolverName(): string;
  
  /**
   * @return string[]
   */
  function getAvailableLanguages(): array;
}
?>