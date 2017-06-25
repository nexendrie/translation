<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nexendrie\Utils\Collection;

/**
 * ChainResolver
 *
 * @author Jakub Konečný
 */
class ChainLocaleResolver extends Collection implements ILocaleResolver {
  use \Nette\SmartObject;
  
  /** @var ILocaleResolver[] */
  protected $items = [];
  protected $class = ILocaleResolver::class;
  
  function addResolver(ILocaleResolver $resolver): void {
    $this->offsetSet(NULL, $resolver);
  }
  
  /**
   * Resolve language
   *
   * @return string|NULL
   */
  function resolve(): ?string {
    foreach($this->items as $resolver) {
      $lang = $resolver->resolve();
      if(!is_null($lang)) {
        return $lang;
      }
    }
    return NULL;
  }
}
?>