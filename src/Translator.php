<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use Nette\Localization\ITranslator;
use Nexendrie\Translation\Loaders\ILoader;

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
  
  /** @internal */
  public const DEFAULT_DOMAIN = "messages";
  
  /** @var ILoader */
  protected $loader;
  /** @var IMessageSelector */
  protected $messageSelector;
  /** @var string[] */
  protected $untranslated = [];
  /** @var callable[] */
  public $onUntranslated = [];
  
  public function __construct(ILoader $loader, IMessageSelector $messageSelector = null) {
    $this->loader = $loader;
    $this->messageSelector = $messageSelector ?? new MessageSelector();
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
   * @return string[]
   */
  protected function extractDomainAndMessage(string $message): array {
    if(!Strings::contains($message, ".")) {
      return [static::DEFAULT_DOMAIN, $message];
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
    if($this->messageSelector->isMultiChoice($text)) {
      $text = $this->messageSelector->choose($text, $count);
    }
    $params["count"] = $count;
    foreach($params as $key => $value) {
      $text = str_replace("%$key%", $value, $text);
    }
    return $text;
  }
}
?>