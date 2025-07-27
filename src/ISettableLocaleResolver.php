<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

if(false) { // @phpstan-ignore if.alwaysFalse
  /** @deprecated Use SettableLocaleResolver */
  interface ISettableLocaleResolver extends SettableLocaleResolver {
  }
} elseif(!interface_exists(ISettableLocaleResolver::class)) {
  class_alias(SettableLocaleResolver::class, ISettableLocaleResolver::class);
}
?>