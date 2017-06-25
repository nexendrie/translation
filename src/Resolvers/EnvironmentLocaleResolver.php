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
  
  function getLang(): ?string {
    $lang = getenv($this->varName);
    if($lang) {
      return $lang;
    }
    return NULL;
  }
  
  function setLang(string $lang) {
    putenv($this->varName . "=$lang");
  }
  
  function getVarName(): string {
    return $this->varName;
  }
  
  function setVarName(string $varName) {
    $this->varName = $varName;
  }
  
  function resolve(): ?string {
    return $this->getLang();
  }
}
?>