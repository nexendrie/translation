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
   */
  function resolve(): ?string;
}
?>