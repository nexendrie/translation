<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

/**
 * EnvironmentResolver
 * Reads current language from an environment variable
 *
 * @author Jakub Konečný
 */
class EnvironmentLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  const VARNAME = "TRANSLATOR_LANGUAGE";
  
  /**
   * @return string|NULL
   */
  function resolve(): ?string {
    $lang = getenv(static::VARNAME);
    if($lang) {
      return $lang;
    } else {
      return NULL;
    }
  }
}
?>