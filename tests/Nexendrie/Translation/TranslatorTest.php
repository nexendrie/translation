<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Konecnyjakub\EventDispatcher\AutoListenerProvider;
use Konecnyjakub\EventDispatcher\EventDispatcher;
use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\TestSuite;
use Nexendrie\Translation\Events\UntranslatedMessage;

#[TestSuite("Translator")]
final class TranslatorTest extends \MyTester\TestCase
{
    private Translator $translator;

    #[BeforeTest]
    public function setUp(): void
    {
        $loader = new Loaders\NeonLoader();
        $loader->folders = [__DIR__ . "/../../lang", __DIR__ . "/../../lang2"];
        $provider = new AutoListenerProvider();
        $dispatcher = new EventDispatcher($provider);
        $this->translator = new Translator($loader, eventDispatcher: $dispatcher);
        $provider->addListener(function (UntranslatedMessage $event): void {
            $this->translator->logUntranslatedMessage($event);
        });
        $this->translator->onUntranslated[] = $this->translator->logUntranslatedMessage(...);
    }

    public function testTranslateEn(): void
    {
        $this->assertCount(0, $this->translator->untranslated);
        $this->assertSame("en", $this->translator->lang);
        // non-existing string
        $this->assertType("string", $this->translator->translate("abc"));
        $this->assertSame("abc", $this->translator->translate("abc"));
        // existing string
        $this->assertType("string", $this->translator->translate("book.content"));
        $this->assertSame("Content", $this->translator->translate("book.content"));
        $this->assertSame("XYZ", $this->translator->translate("xyz"));
        // parameters
        $result = $this->translator->translate("param", 0, ["param1" => "value1"]);
        $this->assertType("string", $result);
        $this->assertSame("Param1: value1", $result);
        // string existing only in default translation
        $this->assertType("string", $this->translator->translate("test"));
        $this->assertSame("Test", $this->translator->translate("test"));
        $this->assertCount(4, $this->translator->untranslated);
        // multi-level message
        $this->assertType("string", $this->translator->translate("abc.multi.abc"));
        $this->assertSame("ABC", $this->translator->translate("abc.multi.abc"));
        $this->assertSame("abc.multi.def", $this->translator->translate("abc.multi.def"));
        $this->assertType("string", $this->translator->translate("abc.multi2.def"));
        $this->assertSame("abc.multi2.def", $this->translator->translate("abc.multi2.def"));
        // plurals
        $this->assertSame("There are no apples.", $this->translator->translate("abc.pluralSimple", 0));
        $this->assertSame("There is one apple.", $this->translator->translate("abc.pluralSimple", 1));
        $this->assertSame("", $this->translator->translate("abc.pluralSimple", 5));
        $this->assertSame("There are 0 apples.", $this->translator->translate("abc.pluralSimpleParams", 0));
        $this->assertSame("There is 1 apple.", $this->translator->translate("abc.pluralSimpleParams", 1));
        $this->assertSame("There are no apples.", $this->translator->translate("abc.multi.pluralSimple", 0));
        $this->assertSame("There is one apple.", $this->translator->translate("abc.multi.pluralSimple", 1));
        $this->assertSame("There are 0 apples.", $this->translator->translate("abc.multi.pluralSimpleParams", 0));
        $this->assertSame("There is 1 apple.", $this->translator->translate("abc.multi.pluralSimpleParams", 1));
        // new style count
        $this->assertSame("There are no apples.", $this->translator->translate("abc.pluralSimple", ["count" => 0]));
        $this->assertSame("Param1: value1", $this->translator->translate("param", ["param1" => "value1"]));
        // test untranslated messages
        $this->assertCount(10, $this->translator->untranslated);
    }

    public function testTranslateCs(): void
    {
        $this->assertCount(0, $this->translator->untranslated);
        $this->translator->lang = "cs";
        $this->assertSame("cs", $this->translator->lang);
        // non-existing string
        $this->assertType("string", $this->translator->translate("abc"));
        $this->assertSame("abc", $this->translator->translate("abc"));
        $this->assertCount(4, $this->translator->untranslated);
        // existing string
        $this->assertType("string", $this->translator->translate("book.content"));
        $this->assertSame("Obsah", $this->translator->translate("book.content"));
        $this->assertSame("xyz", $this->translator->translate("xyz"));
        // parameters
        $result = $this->translator->translate("param", 0, ["param1" => "value1"]);
        $this->assertType("string", $result);
        $this->assertSame("Param2: value1", $result);
        // string existing only in default translation
        $this->assertType("string", $this->translator->translate("test"));
        $this->assertSame("Test", $this->translator->translate("test"));
        $this->assertCount(4, $this->translator->untranslated);
        // multi-level message
        $this->assertType("string", $this->translator->translate("abc.multi.abc"));
        $this->assertSame("Abc", $this->translator->translate("abc.multi.abc"));
        $this->assertSame("abc.multi.def", $this->translator->translate("abc.multi.def"));
        $this->assertType("string", $this->translator->translate("abc.multi2.def"));
        $this->assertSame("abc.multi2.def", $this->translator->translate("abc.multi2.def"));
        // plurals
        $this->assertSame("Není tu žádné jablko.", $this->translator->translate("abc.pluralSimple", 0));
        $this->assertSame("Je tu jedno jablko.", $this->translator->translate("abc.pluralSimple", 1));
        $this->assertSame("", $this->translator->translate("abc.pluralSimple", 5));
        $this->assertSame("Je tu 0 jablek.", $this->translator->translate("abc.pluralSimpleParams", 0));
        $this->assertSame("Je tu 1 jablko.", $this->translator->translate("abc.pluralSimpleParams", 1));
        $this->assertSame("Není tu žádné jablko.", $this->translator->translate("abc.multi.pluralSimple", 0));
        $this->assertSame("Je tu jedno jablko.", $this->translator->translate("abc.multi.pluralSimple", 1));
        $this->assertSame("Je tu 0 jablek.", $this->translator->translate("abc.multi.pluralSimpleParams", 0));
        $this->assertSame("Je tu 1 jablko.", $this->translator->translate("abc.multi.pluralSimpleParams", 1));
        // new style count
        $this->assertSame("Není tu žádné jablko.", $this->translator->translate("abc.pluralSimple", ["count" => 0]));
        $this->assertSame("Param2: value1", $this->translator->translate("param", ["param1" => "value1"]));
        // test untranslated messages
        $this->assertCount(10, $this->translator->untranslated);
    }

    /**
     * Test non-existing language
     */
    public function testTranslateX(): void
    {
        $this->assertCount(0, $this->translator->untranslated);
        $this->translator->lang = "x";
        $this->assertSame("x", $this->translator->lang);
        // non-existing string
        $this->assertType("string", $this->translator->translate("abc"));
        $this->assertSame("abc", $this->translator->translate("abc"));
        // existing string
        $this->assertType("string", $this->translator->translate("book.content"));
        $this->assertSame("Content", $this->translator->translate("book.content"));
        // string existing only in default translation
        $this->assertType("string", $this->translator->translate("test"));
        $this->assertSame("Test", $this->translator->translate("test"));
        $this->assertCount(4, $this->translator->untranslated);
        // multi-level message
        $this->assertType("string", $this->translator->translate("abc.multi.abc"));
        $this->assertSame("ABC", $this->translator->translate("abc.multi.abc"));
        $this->assertSame("abc.multi.def", $this->translator->translate("abc.multi.def"));
        $this->assertType("string", $this->translator->translate("abc.multi2.def"));
        $this->assertSame("abc.multi2.def", $this->translator->translate("abc.multi2.def"));
        // plurals
        $this->assertSame("There are no apples.", $this->translator->translate("abc.pluralSimple", 0));
        $this->assertSame("There is one apple.", $this->translator->translate("abc.pluralSimple", 1));
        $this->assertSame("", $this->translator->translate("abc.pluralSimple", 5));
        $this->assertSame("There are 0 apples.", $this->translator->translate("abc.pluralSimpleParams", 0));
        $this->assertSame("There is 1 apple.", $this->translator->translate("abc.pluralSimpleParams", 1));
        $this->assertSame("There are no apples.", $this->translator->translate("abc.multi.pluralSimple", 0));
        $this->assertSame("There is one apple.", $this->translator->translate("abc.multi.pluralSimple", 1));
        $this->assertSame("There are 0 apples.", $this->translator->translate("abc.multi.pluralSimpleParams", 0));
        $this->assertSame("There is 1 apple.", $this->translator->translate("abc.multi.pluralSimpleParams", 1));
        // new style count
        $this->assertSame("There are no apples.", $this->translator->translate("abc.pluralSimple", ["count" => 0]));
        $this->assertSame("Param1: value1", $this->translator->translate("param", ["param1" => "value1"]));
        // test untranslated messages
        $this->assertCount(10, $this->translator->untranslated);
    }
}
