<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Nexendrie\Utils\Intervals,
    Nette\Utils\Strings;

/**
 * MessageSelector
 *
 * @author Jakub Konečný
 */
final class MessageSelector implements IMessageSelector {
  public function isMultiChoice(string $message): bool {
    return is_string(Intervals::findInterval($message));
  }
  
  public function choose(string $message, int $count): string {
    if(!$this->isMultiChoice($message)) {
      return $message;
    }
    $variants = explode("|", $message);
    foreach($variants as $variant) {
      $interval = Intervals::findInterval($variant);
      if(is_string($interval) AND Intervals::isInInterval($count, $interval)) {
        return Strings::trim(Strings::after($variant, $interval));
      }
    }
    return "";
  }
}
?>