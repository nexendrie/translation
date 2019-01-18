<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

/**
 * ILocaleResolver
 *
 * @author Jakub Konečný
 */
interface ILocaleResolver {
  /**
   * Resolve language
   */
  public function resolve(): ?string;
}
?>