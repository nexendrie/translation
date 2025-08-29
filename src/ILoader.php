<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

if (false) { // @phpstan-ignore if.alwaysFalse
    /** @deprecated Use Loader */
    interface ILoader extends Loader
    {
    }
} elseif (!interface_exists(ILoader::class)) {
    class_alias(Loader::class, ILoader::class);
}
