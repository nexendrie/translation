<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Nette\Utils\Arrays,
    Nette\Utils\Strings,
    Nette\Localization\ITranslator,
    Nexendrie\Translation\Loaders\ILoader;

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
   * @param string $message
   * @return string[]
   */
  protected function extractDomainAndMessage(string $message): array {
    if(!Strings::contains($message, ".")) {
      $domain = "messages";
      $m = $message;
    } else {
      $domain = Strings::before($message, ".");
      $m = Strings::after($message, ".");
    }
    return [$domain, $m];
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
      $text = Arrays::get($text, $part, "");
      if($text === "") {
        break;
      }
    }
    return $text;
  }
  
  /**
   * Choose correct variant of message depending on $count
   *
   * @param string $message
   * @param int $count
   * @return string
   */
  protected function multiChoiceTrans(string $message, int $count): string {
    $variants = explode("|", $message);
    foreach($variants as $variant) {
      $interval = Intervals::findInterval($variant);
      if(is_string($interval) AND Intervals::isInInterval($count, $interval)) {
        return (string) Strings::after($variant, $interval);
      }
    }
    return "";
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
    list($domain, $m) = $this->extractDomainAndMessage($message);
    $texts = Arrays::get($this->loader->getTexts(), $domain, []);
    $parts = explode(".", $m);
    if(count($parts) === 1) {
      $text = Arrays::get($texts, $m, "");
    } else {
      $text = $this->multiLevelTrans($parts, $texts);
    }
    if($text === "") {
      $this->onUntranslated($message);
      return $message;
    }
    if(is_string(Intervals::findInterval($text))) {
      $text = Strings::trim($this->multiChoiceTrans($text, $count));
    }
    $params["count"] = $count;
    foreach($params as $key => $value) {
      $text = str_replace("%$key%", $value, $text);
    }
    return $text;
  }
}
?>