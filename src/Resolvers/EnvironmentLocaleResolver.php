<?php
namespace Nexendrie\Translation\Resolvers;

/**
 * EnvironmentResolver
 *
 * @author Jakub Konečný
 * @property string $defaultLang
 */
class EnvironmentLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  const VARNAME = "TRANSLATOR_LANGUAGE";
  
  /** @var string */
  protected $defaultLang = "en";
  
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
    $lang = getenv(static::VARNAME);
    if($lang) {
      return $lang;
    } else {
      return $this->defaultLang;
    }
  }
}
?>