<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Events;

use Nexendrie\Translation\Loaders\FileLoader;

final readonly class LanguageChanged
{
    public function __construct(
        public FileLoader $loader,
        public string $oldLanguage,
        public string $newLanguage
    ) {
    }
}
