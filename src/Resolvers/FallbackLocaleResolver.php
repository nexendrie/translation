<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

/**
 * FallbackLocaleResolver
 * Fallback resolver when nothing else can be used
 * Uses just default language (specified by loader)
 *
 * @author Jakub Konečný
 */
class FallbackLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  /**
   * Resolve language
   *
   * @return NULL
   */
  public function resolve(): ?string {
    return NULL;
  }
}
?>