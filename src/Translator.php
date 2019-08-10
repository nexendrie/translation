<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use Nette\Localization\ITranslator;

/**
 * Translator
 *
 * @author Jakub Konečný
 * @property string $lang
 * @property-read string[] $untranslated
 * @method void onUntranslated(string $message)
 */
final class Translator implements ITranslator {
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
  
  public function setLang(string $lang): void {
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
   */
  public function translate($message, ... $parameters): string {
    if(count($parameters) === 1 && is_array($parameters[0])) {
      $count = $parameters[0]["count"] ?? 0;
      $params = $parameters[0];
    } else {
      $params = $parameters[1] ?? [];
      $params["count"] = $count = $parameters[0] ?? 0;
    }
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
    foreach($params as $key => $value) {
      $text = str_replace("%$key%", $value, $text);
    }
    return $text;
  }
}
?>