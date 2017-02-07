<?php
namespace Nexendrie\Translation;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";


/**
 * TranslatorTest
 *
 * @author Jakub Konečný
 * @testCase
 */
class TranslatorTest extends \Tester\TestCase {
  /** @var Translator */
  private $translator;
  
  function setUp() {
    $loader = new Loaders\NeonLoader;
    $loader->folders = [__DIR__ . "/../../lang", __DIR__ . "/../../lang2"];
    $this->translator = new Translator($loader);
  }
  
  function testTranslateEn() {
    Assert::count(0, $this->translator->untranslated);
    Assert::same("en", $this->translator->lang);
    // non-existing string
    Assert::type("string", $this->translator->translate("abc"));
    Assert::same("abc", $this->translator->translate("abc"));
    // existing string
    Assert::type("string", $this->translator->translate("book.content"));
    Assert::same("Content", $this->translator->translate("book.content"));
    Assert::same("XYZ", $this->translator->translate("xyz"));
    // parameters
    $result = $this->translator->translate("param", 0, ["param1" => "value1"]);
    Assert::type("string", $result);
    Assert::same("Param1: value1", $result);
    // string existing only in default translation
    Assert::type("string", $this->translator->translate("test"));
    Assert::same("Test", $this->translator->translate("test"));
    Assert::count(2, $this->translator->untranslated);
    // multi-level message
    Assert::type("string", $this->translator->translate("abc.multi.abc"));
    Assert::same("ABC", $this->translator->translate("abc.multi.abc"));
    Assert::same("abc.multi.def", $this->translator->translate("abc.multi.def"));
    Assert::type("string", $this->translator->translate("abc.multi2.def"));
    Assert::same("abc.multi2.def", $this->translator->translate("abc.multi2.def"));
    // test untranslated messages
    Assert::count(5, $this->translator->untranslated);
  }
  
  function testTranslateCs() {
    Assert::count(0, $this->translator->untranslated);
    $this->translator->lang = "cs";
    Assert::same("cs", $this->translator->lang);
    // non-existing string
    Assert::type("string", $this->translator->translate("abc"));
    Assert::same("abc", $this->translator->translate("abc"));
    Assert::count(2, $this->translator->untranslated);
    // existing string
    Assert::type("string", $this->translator->translate("book.content"));
    Assert::same("Obsah", $this->translator->translate("book.content"));
    Assert::same("xyz", $this->translator->translate("xyz"));
    // parameters
    $result = $this->translator->translate("param", 0, ["param1" => "value1"]);
    Assert::type("string", $result);
    Assert::same("Param2: value1", $result);
    // string existing only in default translation
    Assert::type("string", $this->translator->translate("test"));
    Assert::same("Test", $this->translator->translate("test"));
    Assert::count(2, $this->translator->untranslated);
    // multi-level message
    Assert::type("string", $this->translator->translate("abc.multi.abc"));
    Assert::same("Abc", $this->translator->translate("abc.multi.abc"));
    Assert::same("abc.multi.def", $this->translator->translate("abc.multi.def"));
    Assert::type("string", $this->translator->translate("abc.multi2.def"));
    Assert::same("abc.multi2.def", $this->translator->translate("abc.multi2.def"));
    // test untranslated messages
    Assert::count(5, $this->translator->untranslated);
  }
  
  /**
   * Test non-existing language
   */
  function testTranslateX() {
    Assert::count(0, $this->translator->untranslated);
    $this->translator->lang = "x";
    Assert::same("x", $this->translator->lang);
    // non-existing string
    Assert::type("string", $this->translator->translate("abc"));
    Assert::same("abc", $this->translator->translate("abc"));
    // existing string
    Assert::type("string", $this->translator->translate("book.content"));
    Assert::same("Content", $this->translator->translate("book.content"));
    // string existing only in default translation
    Assert::type("string", $this->translator->translate("test"));
    Assert::same("Test", $this->translator->translate("test"));
    Assert::count(2, $this->translator->untranslated);
    // multi-level message
    Assert::type("string", $this->translator->translate("abc.multi.abc"));
    Assert::same("ABC", $this->translator->translate("abc.multi.abc"));
    Assert::same("abc.multi.def", $this->translator->translate("abc.multi.def"));
    Assert::type("string", $this->translator->translate("abc.multi2.def"));
    Assert::same("abc.multi2.def", $this->translator->translate("abc.multi2.def"));
    // test untranslated messages
    Assert::count(5, $this->translator->untranslated);
  }
}

$test = new TranslatorTest;
$test->run();
?>