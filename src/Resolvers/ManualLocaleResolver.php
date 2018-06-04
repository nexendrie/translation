<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

/**
 * ManualLocaleResolver
 * Allows you to manually specify current language
 *
 * @author Jakub Konečný
 * @property string|null $lang
 */
class ManualLocaleResolver implements ISettableLocaleResolver {
  use \Nette\SmartObject;
  
  /** @var string|null */
  protected $lang = null;
  
  public function getLang(): ?string {
    return $this->lang;
  }
  
  public function setLang(string $lang): void {
    $this->lang = $lang;
  }
  
  public function resolve(): ?string {
    return $this->getLang();
  }
}
?>