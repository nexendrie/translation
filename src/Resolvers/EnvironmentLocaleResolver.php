<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nexendrie\Translation\ISettableLocaleResolver;

/**
 * EnvironmentResolver
 * Reads current language from an environment variable
 *
 * @author Jakub Konečný
 * @property string|null $lang
 * @property string $varName
 */
final class EnvironmentLocaleResolver implements ISettableLocaleResolver {
  use \Nette\SmartObject;

  protected string $varName = "TRANSLATOR_LANGUAGE";
  
  public function getLang(): ?string {
    $lang = getenv($this->varName);
    if(is_string($lang)) {
      return $lang;
    }
    return null;
  }
  
  public function setLang(?string $lang): void {
    if($lang === null) {
      putenv($this->varName);
    } else {
      putenv($this->varName . "=$lang");
    }
  }
  
  public function getVarName(): string {
    return $this->varName;
  }
  
  public function setVarName(string $varName): void {
    $this->varName = $varName;
  }
  
  public function resolve(): ?string {
    return $this->getLang();
  }
}
?>