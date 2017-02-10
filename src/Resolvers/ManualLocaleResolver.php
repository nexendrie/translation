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
  
  /**
   * @return string
   */
  function getLang() {
    return $this->lang;
  }
  
  /**
   * @param string $lang
   */
  function setLang($lang) {
    $this->lang = (string) $lang;
  }
  
  /**
   * @return string
   */
  function resolve() {
    return $this->getLang();
  }
}
?>