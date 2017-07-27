<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

/**
 * ILoader
 *
 * @author Jakub Konečný
 */
interface ILoader {
  public function getLang(): string;
  public function setLang(string $lang);
  public function getDefaultLang(): string;
  public function setDefaultLang(string $defaultLang);
  public function getResources(): array;
  public function getTexts(): array;
  public function getResolverName(): string;
  
  /**
   * @return string[]
   */
  public function getAvailableLanguages(): array;
}
?>