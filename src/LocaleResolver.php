<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

/**
 * LocaleResolver
 *
 * @author Jakub Konečný
 */
interface LocaleResolver {
  /**
   * Resolve language
   */
  public function resolve(): ?string;
}
?>