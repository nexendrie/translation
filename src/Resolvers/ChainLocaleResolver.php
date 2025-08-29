<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nexendrie\Utils\Collection;
use Nexendrie\Translation\LocaleResolver;

/**
 * ChainResolver
 *
 * @author Jakub Konečný
 */
final class ChainLocaleResolver extends Collection implements LocaleResolver
{
    protected string $class = LocaleResolver::class;

    public function resolve(): ?string
    {
        /** @var LocaleResolver $resolver */
        foreach ($this->items as $resolver) {
            $lang = $resolver->resolve();
            if ($lang !== null) {
                return $lang;
            }
        }
        return null;
    }
}
