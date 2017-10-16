<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteDI;

/**
 * ITranslationProvider
 *
 * @author Jakub Konečný
 */
interface ITranslationProvider {
  /**
   * Return list of folders that contains translations
   *
   * @return string[]
   */
  public function getTranslationResources(): array;
}
?>