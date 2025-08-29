<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteDI;

if (false) { // @phpstan-ignore if.alwaysFalse
    /** @deprecated Use TranslationProvider */
    interface ITranslationProvider extends TranslationProvider
    {
    }
} elseif (!interface_exists(ITranslationProvider::class)) {
    class_alias(TranslationProvider::class, ITranslationProvider::class);
}
