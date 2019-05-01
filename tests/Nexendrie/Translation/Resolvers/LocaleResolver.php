<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nexendrie\Translation\ILocaleResolver;

/**
 * LocaleResolver
 *
 * @author Jakub Konečný
 */
final class LocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;

  /**
   * Resolve language
   *
   * @return null
   */
  public function resolve(): ?string {
    return null;
  }
}
?>