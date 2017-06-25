<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

/**
 * ILoader
 *
 * @author Jakub Konečný
 */
interface ILoader {
  function getLang(): string;
  function setLang(string $lang);
  function getDefaultLang(): string;
  function setDefaultLang(string $defaultLang);
  function getResources(): array;
  function getTexts(): array;
  function getResolverName(): string;
  
  /**
   * @return string[]
   */
  function getAvailableLanguages(): array;
}
?>