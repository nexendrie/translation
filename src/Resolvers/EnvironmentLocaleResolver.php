<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

/**
 * EnvironmentResolver
 * Reads current language from an environment variable
 *
 * @author Jakub Konečný
 * @property string|NULL $lang
 */
class EnvironmentLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  const VARNAME = "TRANSLATOR_LANGUAGE";
  
  /**
   * @return string|NULL
   */
  function getLang(): ?string {
    $lang = getenv(static::VARNAME);
    if($lang) {
      return $lang;
    } else {
      return NULL;
    }
  }
  
  /**
   * @param string $lang
   */
  function setLang(string $lang) {
    putenv(static::VARNAME . "=$lang");
  }
  
  /**
   * @return string|NULL
   */
  function resolve(): ?string {
    return $this->getLang();
  }
}
?>