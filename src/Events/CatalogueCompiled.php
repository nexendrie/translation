<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Events;

use Nexendrie\Translation\CatalogueCompiler;

final class CatalogueCompiled
{
    public function __construct(public readonly CatalogueCompiler $catalogueCompiler, public readonly string $language)
    {
    }
}
