<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nexendrie\Translation\ISettableLocaleResolver;

/**
 * ManualLocaleResolver
 * Allows you to manually specify current language
 *
 * @author Jakub Konečný
 */
final class ManualLocaleResolver implements ISettableLocaleResolver {
  use \Nette\SmartObject;

  public ?string $lang = null;

  /**
   * @deprecated Access the property directly
   */
  public function getLang(): ?string {
    return $this->lang;
  }

  /**
   * @deprecated Access the property directly
   */
  public function setLang(?string $lang): void {
    $this->lang = $lang;
  }
  
  public function resolve(): ?string {
    return $this->lang;
  }
}
?>