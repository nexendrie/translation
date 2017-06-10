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
  
  const VAR_NAME = "TRANSLATOR_LANGUAGE";
  
  /**
   * @return string|NULL
   */
  function getLang(): ?string {
    $lang = getenv(static::VAR_NAME);
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
    putenv(static::VAR_NAME . "=$lang");
  }
  
  /**
   * @return string|NULL
   */
  function resolve(): ?string {
    return $this->getLang();
  }
}
?>