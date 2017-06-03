<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteDI;

/**
 * ProviderExtension
 *
 * @author Jakub Konečný
 */
class ProviderExtension extends \Nette\DI\CompilerExtension implements ITranslationProvider {
  /**
   * @return string[]
   */
  function getTranslationResources(): array {
    return [__DIR__ . "/../../../../_temp"];
  }
}
?>