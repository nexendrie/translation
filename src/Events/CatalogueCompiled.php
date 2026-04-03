<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Events;

use Nexendrie\Translation\CatalogueCompiler;

final readonly class CatalogueCompiled
{
    public function __construct(public CatalogueCompiler $catalogueCompiler, public string $language)
    {
    }
}
