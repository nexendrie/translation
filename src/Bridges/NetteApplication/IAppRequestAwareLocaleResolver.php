<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteApplication;

if(false) { // @phpstan-ignore if.alwaysFalse
  /** @deprecated Use AppRequestAwareLocaleResolver */
  interface IAppRequestAwareLocaleResolver extends AppRequestAwareLocaleResolver {
  }
} elseif(!interface_exists(IAppRequestAwareLocaleResolver::class)) {
  class_alias(AppRequestAwareLocaleResolver::class, IAppRequestAwareLocaleResolver::class);
}
?>