<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

/**
 * ManualLocaleResolver
 * Allows you to manually specify current language
 *
 * @author Jakub Konečný
 * @property string|NULL $lang
 */
class ManualLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  /** @var string|NULL */
  protected $lang = NULL;
  
  function getLang(): ?string {
    return $this->lang;
  }
  
  function setLang(string $lang) {
    $this->lang = $lang;
  }
  
  function resolve(): ?string {
    return $this->getLang();
  }
}
?>