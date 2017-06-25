<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

/**
 * EnvironmentResolver
 * Reads current language from an environment variable
 *
 * @author Jakub Konečný
 * @property string|NULL $lang
 * @property string $varName
 */
class EnvironmentLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  /** @var string */
  protected $varName = "TRANSLATOR_LANGUAGE";
  
  /**
   * @return string|NULL
   */
  function getLang(): ?string {
    $lang = getenv($this->varName);
    if($lang) {
      return $lang;
    }
    return NULL;
  }
  
  /**
   * @param string $lang
   */
  function setLang(string $lang) {
    putenv($this->varName . "=$lang");
  }
  
  /**
   * @return string
   */
  function getVarName(): string {
    return $this->varName;
  }
  
  /**
   * @param string $varName
   */
  function setVarName(string $varName) {
    $this->varName = $varName;
  }
  
  /**
   * @return string|NULL
   */
  function resolve(): ?string {
    return $this->getLang();
  }
}
?>