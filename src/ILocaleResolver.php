<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

if(false) { // @phpstan-ignore if.alwaysFalse
  /** @deprecated Use LocaleResolver */
  interface ILocaleResolver extends LocaleResolver {
  }
} elseif(!interface_exists(ILocaleResolver::class)) {
  class_alias(LocaleResolver::class, ILocaleResolver::class);
}
?>