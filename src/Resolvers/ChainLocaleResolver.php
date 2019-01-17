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

  /** @var string */
  protected $class = ILocaleResolver::class;

  /**
   * @deprecated
   */
  public function addResolver(ILocaleResolver $resolver): void {
    $this[] = $resolver;
  }
  
  public function resolve(): ?string {
    foreach($this as $resolver) {
      $lang = $resolver->resolve();
      if(!is_null($lang)) {
        return $lang;
      }
    }
    return null;
  }
}
?>