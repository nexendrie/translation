<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

if (false) { // @phpstan-ignore if.alwaysFalse
    /** @deprecated Use FileLoader */
    interface IFileLoader extends FileLoader
    {
    }
} elseif (!interface_exists(IFileLoader::class)) {
    class_alias(FileLoader::class, IFileLoader::class);
}
