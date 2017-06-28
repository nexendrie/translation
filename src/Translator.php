<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Nette\Utils\Arrays,
    Nette\Utils\Strings,
    Nette\Localization\ITranslator,
    Nexendrie\Translation\Loaders\ILoader,
    Nexendrie\Utils\Intervals;

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
  
  public function __construct(ILoader $loader) {
    $this->loader = $loader;
  }
  
  public function getLang(): string {
    return $this->loader->getLang();
  }
  
  public function setLang(string $lang) {
    $this->loader->setLang($lang);
  }
  
  /**
   * @return string[]
   */
  public function getUntranslated(): array {
    return $this->untranslated;
  }
  
  /**
   * @param string $message
   * @return string[]
   */
  protected function extractDomainAndMessage(string $message): array {
    if(!Strings::contains($message, ".")) {
      return ["messages", $message];
    }
    return explode(".", $message, 2);
  }
  
  /**
   * Translate multi-level message
   */
  protected function multiLevelTrans(array $message, array $texts): string {
    $text = $texts;
    foreach($message as $part) {
      $text = Arrays::get($text, $part, "");
      if($text === "") {
        break;
      }
    }
    return $text;
  }
  
  /**
   * Choose correct variant of message depending on $count
   */
  protected function multiChoiceTrans(string $message, int $count): string {
    $variants = explode("|", $message);
    foreach($variants as $variant) {
      $interval = Intervals::findInterval($variant);
      if(is_string($interval) AND Intervals::isInInterval($count, $interval)) {
        return Strings::trim(Strings::after($variant, $interval));
      }
    }
    return "";
  }
  
  public function logUntranslatedMessage(string $message): void {
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
  public function translate($message, $count = 0, $params = []) {
    list($domain, $m) = $this->extractDomainAndMessage($message);
    $texts = Arrays::get($this->loader->getTexts(), $domain, []);
    $parts = explode(".", $m);
    if(count($parts) === 1) {
      $parts = [$m];
    }
    $text = $this->multiLevelTrans($parts, $texts);
    if($text === "") {
      $this->onUntranslated($message);
      return $message;
    }
    if(is_string(Intervals::findInterval($text))) {
      $text = $this->multiChoiceTrans($text, $count);
    }
    $params["count"] = $count;
    foreach($params as $key => $value) {
      $text = str_replace("%$key%", $value, $text);
    }
    return $text;
  }
}
?>