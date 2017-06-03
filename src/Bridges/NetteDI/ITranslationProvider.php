<?php
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
  function getTranslationResources(): array;
}
?>