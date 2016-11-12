<?php
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
 */
class Translator implements ITranslator {
  use \Nette\SmartObject;
  
  /** @var ILoader */
  protected $loader;
  /** @var string[] */
  protected $untranslated = [];
  
  function __construct(ILoader $loader) {
    $this->loader = $loader;
  }
  
  /**
   * @return string
   */
  function getLang() {
    return $this->loader->getLang();
  }
  
  /**
   * @param string $lang
   */
  function setLang($lang) {
    $this->loader->setLang($lang);
  }
  
  /**
   * @return string[]
   */
  function getUntranslated() {
    return $this->untranslated;
  }
  
  /**
   * Translate multi-level message
   *
   * @param array $message
   * @param array $texts
   * @return string
   */
  protected function multiLevelTrans(array $message, array $texts) {
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
      $this->untranslated[] = $message;
      return $message;
    } else {
      return $text;
    }
  }
}
?>