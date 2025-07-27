<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

if(false) { // @phpstan-ignore if.alwaysFalse
  /** @deprecated Use LoaderAwareLocaleResolver */
  interface ILoaderAwareLocaleResolver extends LoaderAwareLocaleResolver {
  }
} elseif(!interface_exists(ILoaderAwareLocaleResolver::class)) {
  class_alias(LoaderAwareLocaleResolver::class, ILoaderAwareLocaleResolver::class);
}
?>