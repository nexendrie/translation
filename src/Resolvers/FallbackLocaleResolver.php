<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nexendrie\Translation\ILocaleResolver;

/**
 * FallbackLocaleResolver
 * Fallback resolver when nothing else can be used
 * Uses just default language (specified by loader)
 *
 * @author Jakub Konečný
 */
final class FallbackLocaleResolver implements ILocaleResolver {
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