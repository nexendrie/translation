<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\TestSuite;


#[TestSuite("IntervalsMessageSelector")]
final class IntervalsMessageSelectorTest extends \MyTester\TestCase
{
    protected MessageSelector $messageSelector;

    #[BeforeTest]
    public function setUp(): void
    {
        $this->messageSelector = new MessageSelector();
    }

    public function testIsMultiChoice(): void
    {
        $this->assertFalse($this->messageSelector->isMultiChoice("abc"));
        $this->assertTrue($this->messageSelector->isMultiChoice("{0}abc|{1}def"));
    }

    public function testChoose(): void
    {
        $message = "abc";
        $this->assertSame($message, $this->messageSelector->choose($message, 0));
        $this->assertSame("abc", $this->messageSelector->choose("{0}abc|{1}def", 0));
        $this->assertSame("Číh", $this->messageSelector->choose("{0}Číh|{1}def", 0));
    }
}
