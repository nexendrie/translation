<?php
namespace Nexendrie\Translation\Resolvers;

/**
 * EnvironmentResolver
 *
 * @author Jakub Konečný
 */
class EnvironmentLocaleResolver implements ILocaleResolver {
  const VARNAME = "TRANSLATOR_LANGUAGE";
  
  /** @var string */
  protected $defaultLang = "en";
  
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