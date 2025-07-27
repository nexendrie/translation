<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

/**
 * LocaleResolver
 *
 * @author Jakub Konečný
 */
final class LocaleResolver implements \Nexendrie\Translation\LocaleResolver {
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