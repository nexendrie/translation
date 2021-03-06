<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

/**
 * ILoader
 *
 * @author Jakub Konečný
 */
interface ILoader {
  public function getLang(): string;
  public function setLang(string $lang): void;
  public function getDefaultLang(): string;
  public function setDefaultLang(string $defaultLang): void;
  public function getResources(): array;
  public function getTexts(): array;
  public function getResolverName(): string;

  /**
   * @return string[]
   */
  public function getAvailableLanguages(): array;
}
?>