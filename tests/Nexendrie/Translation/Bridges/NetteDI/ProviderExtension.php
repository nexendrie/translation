<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\DI\CompilerExtension;

/**
 * ProviderExtension
 *
 * @author Jakub Konečný
 */
class ProviderExtension extends CompilerExtension implements ITranslationProvider {
  /**
   * @return string[]
   */
  public function getTranslationResources(): array {
    return [__DIR__ . "/../../../../_temp"];
  }
}
?>