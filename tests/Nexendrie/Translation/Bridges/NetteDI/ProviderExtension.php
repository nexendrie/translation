<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\DI\CompilerExtension;

/**
 * ProviderExtension
 *
 * @author Jakub Konečný
 */
final class ProviderExtension extends CompilerExtension implements TranslationProvider
{
    /**
     * @return string[]
     */
    public function getTranslationResources(): array
    {
        return [__DIR__ . "/../../../../_temp"];
    }
}
