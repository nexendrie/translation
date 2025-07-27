<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Nette\Utils\Arrays;

/**
 * Translator
 *
 * @author Jakub Konečný
 * @property string $lang
 * @property-read string[] $untranslated
 * @method void onUntranslated(string $message)
 */
final class Translator implements \Nette\Localization\Translator {
  use \Nette\SmartObject;
  
  /** @internal */
  public const DEFAULT_DOMAIN = "messages";

  /** @var string[] */
  private array $untranslated = [];
  /** @var callable[] */
  public array $onUntranslated = [];
  
  public function __construct(private readonly ILoader $loader, private readonly IMessageSelector $messageSelector = new MessageSelector()) {
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
  private function extractDomainAndMessage(string $message): array {
    if(!str_contains($message, ".")) {
      return [self::DEFAULT_DOMAIN, $message];
    }
    return explode(".", $message, 2);
  }
  
  /**
   * Translate multi-level message
   */
  private function multiLevelTrans(array $message, array $texts): string {
    $text = $texts;
    foreach($message as $part) {
      $text = Arrays::get($text, $part, "");
      if($text === "") {
        break;
      }
    }
    /** @var string $text */
    return $text; // @phpstan-ignore varTag.type
  }
  
  public function logUntranslatedMessage(string $message): void {
    $this->untranslated[] = $message;
  }

  /**
   * Translate the string
   *
   * @param mixed $message
   * @param mixed ...$parameters
   * @return string
   */
  public function translate($message, ... $parameters): string {
    $message = (string) $message;
    if(count($parameters) === 1 && is_array($parameters[0])) {
      $count = $parameters[0]["count"] ?? 0;
      $params = $parameters[0];
    } else {
      $params = $parameters[1] ?? [];
      $params["count"] = $count = $parameters[0] ?? 0;
    }
    [$domain, $m] = $this->extractDomainAndMessage($message);
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
      $text = str_replace("%$key%", (string) $value, $text);
    }
    return $text;
  }
}
?>