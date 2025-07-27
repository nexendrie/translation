<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteDI;

/**
 * TranslationProvider
 *
 * @author Jakub Konečný
 */
interface TranslationProvider {
  /**
   * Return list of folders that contains translations
   *
   * @return string[]
   */
  public function getTranslationResources(): array;
}
?>