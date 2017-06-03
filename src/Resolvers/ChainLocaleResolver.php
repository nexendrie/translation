<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

/**
 * ChainResolver
 *
 * @author Jakub Konečný
 */
class ChainLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  /** @var ILocaleResolver[] */
  protected $resolvers = [];
  
  function addResolver(ILocaleResolver $resolver): void {
    $this->resolvers[] = $resolver;
  }
  
  /**
   * Resolve language
   *
   * @return string|NULL
   */
  function resolve(): ?string {
    foreach($this->resolvers as $resolver) {
      $lang = $resolver->resolve();
      if(!is_null($lang)) {
        return $lang;
      }
    }
    return NULL;
  }
}
?>