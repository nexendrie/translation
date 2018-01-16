<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

/**
 * IMessageSelector
 *
 * @author Jakub Konečný
 */
interface IMessageSelector {
  /**
   * Does the message contain multiple variants?
   */
  public function isMultiChoice(string $message): bool;
  
  /**
   * Choose correct variant of message depending on $count
   */
  public function choose(string $message, int $count): string;
}
?>