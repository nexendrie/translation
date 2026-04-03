<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Events;

use Nexendrie\Translation\Loaders\FileLoader;

final readonly class LanguageLoaded
{
    public function __construct(public FileLoader $loader, public string $language)
    {
    }
}
