<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Nette\Utils\Arrays,
    Nette\Localization\ITranslator,
    Nexendrie\Translation\Loaders\ILoader,
    Nette\InvalidArgumentException;

/**
 * Translator
 *
 * @author Jakub Konečný
 * @property string $lang
 * @property-read string[] $untranslated
 * @method void onUntranslated(string $message)
 */
class Translator implements ITranslator {
  use \Nette\SmartObject;
  
  /** @var ILoader */
  protected $loader;
  /** @var string[] */
  protected $untranslated = [];
  /** @var callable[] */
  public $onUntranslated = [];
  
  function __construct(ILoader $loader) {
    $this->onUntranslated[] = [$this, "logUntranslatedMessage"];
    $this->loader = $loader;
  }
  
  /**
   * @return string
   */
  function getLang(): string {
    return $this->loader->getLang();
  }
  
  /**
   * @param string $lang
   */
  function setLang(string $lang) {
    $this->loader->setLang($lang);
  }
  
  /**
   * @return string[]
   */
  function getUntranslated(): array {
    return $this->untranslated;
  }
  
  /**
   * Translate multi-level message
   *
   * @param array $message
   * @param array $texts
   * @return string
   */
  protected function multiLevelTrans(array $message, array $texts): string {
    $text = $texts;
    foreach($message as $index => $part) {
      try {
        $text = Arrays::get($text, $part);
      } catch(InvalidArgumentException $e) {
        $text = "";
        break;
      }
    }
    return $text;
  }
  
  /**
   * @param string $message
   * @return void
   */
  function logUntranslatedMessage(string $message): void {
    $this->untranslated[] = $message;
  }
  
  /**
   * Translate the string
   *
   * @param string $message
   * @param int $count
   * @param array $params
   * @return string
   */
  function translate($message, $count = 0, $params = []) {
    $dotPos = strpos($message, ".");
    if($dotPos === false) {
      $domain = "messages";
      $m = $message;
    } else {
      $domain = substr($message, 0, $dotPos);
      $m = substr($message, $dotPos + 1);
    }
    $texts = Arrays::get($this->loader->getTexts(), $domain, []);
    $parts = explode(".", $m);
    if(count($parts) === 1) {
      $text = Arrays::get($texts, $m, "");
    } else {
      $text = $this->multiLevelTrans($parts, $texts);
    }
    foreach($params as $key => $value) {
      $text = str_replace("%$key%", $value, $text);
    }
    if($text === "") {
      $this->onUntranslated($message);
      return $message;
    } else {
      return $text;
    }
  }
}
?>