<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;

/**
 * MessageSelectorTest
 *
 * @author Jakub Konečný
 * @testCase
 */
final class MessageSelectorTest extends \Tester\TestCase {
  protected MessageSelector $messageSelector;
  
  public function setUp() {
    $this->messageSelector = new MessageSelector();
  }
  
  public function testIsMultiChoice() {
    Assert::false($this->messageSelector->isMultiChoice("abc"));
    Assert::true($this->messageSelector->isMultiChoice("{0}abc|{1}def"));
  }
  
  public function testChoose() {
    $message = "abc";
    Assert::same($message, $this->messageSelector->choose($message, 0));
    Assert::same("abc", $this->messageSelector->choose("{0}abc|{1}def", 0));
  }
}

$test = new MessageSelectorTest();
$test->run();
?>