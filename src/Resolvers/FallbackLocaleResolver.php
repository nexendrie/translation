<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nexendrie\Translation\LocaleResolver;

/**
 * FallbackLocaleResolver
 * Fallback resolver when nothing else can be used
 * Uses just default language (specified by loader)
 *
 * @author Jakub Konečný
 */
final class FallbackLocaleResolver implements LocaleResolver
{
    /**
     * Resolve language
     *
     * @return null
     */
    public function resolve(): ?string
    {
        return null;
    }
}
