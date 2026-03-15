<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Events;

use Nexendrie\Translation\Loaders\FileLoader;

final class LanguageChanged
{
    public function __construct(
        public readonly FileLoader $loader,
        public readonly string $oldLanguage,
        public readonly string $newLanguage
    ) {
    }
}
