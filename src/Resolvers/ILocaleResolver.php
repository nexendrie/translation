<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

/**
 * ILocaleResolver
 *
 * @author Jakub Konečný
 */
interface ILocaleResolver {
  /**
   * Resolve language
   *
   * @return string|NULL
   */
  function resolve(): ?string;
}
?>