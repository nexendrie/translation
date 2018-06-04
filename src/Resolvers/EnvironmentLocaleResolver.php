<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

/**
 * EnvironmentResolver
 * Reads current language from an environment variable
 *
 * @author Jakub Konečný
 * @property string|null $lang
 * @property string $varName
 */
class EnvironmentLocaleResolver implements ISettableLocaleResolver {
  use \Nette\SmartObject;
  
  /** @var string */
  protected $varName = "TRANSLATOR_LANGUAGE";
  
  public function getLang(): ?string {
    $lang = getenv($this->varName);
    if($lang) {
      return $lang;
    }
    return null;
  }
  
  public function setLang(string $lang): void {
    putenv($this->varName . "=$lang");
  }
  
  public function getVarName(): string {
    return $this->varName;
  }
  
  public function setVarName(string $varName) {
    $this->varName = $varName;
  }
  
  public function resolve(): ?string {
    return $this->getLang();
  }
}
?>