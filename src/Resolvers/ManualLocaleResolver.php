<?php
namespace Nexendrie\Translation\Resolvers;

/**
 * ManualLocaleResolver
 *
 * @author Jakub Konečný
 * @property string $lang
 * @property string $defaultLang
 */
class ManualLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  /** @var string */
  protected $defaultLang = "en";
  /** @var string */
  protected $lang;
  
  /**
   * @return string
   */
  function getLang() {
    if($this->lang) {
      return $this->lang;
    } else {
      return $this->defaultLang;
    }
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
  function getDefaultLang() {
    return $this->defaultLang;
  }
  
  /**
   * Set default language
   *
   * @param string $default
   */
  function setDefaultLang($default) {
    $this->defaultLang = (string) $default;
  }
  
  /**
   * @return string
   */
  function resolve() {
    return $this->getLang();
  }
}
?>