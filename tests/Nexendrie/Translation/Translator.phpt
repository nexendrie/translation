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
    $this->translator = new Translator;
    $this->translator->folder = __DIR__ . "/../../lang";
  }
  
  function testLang() {
    Assert::same(__DIR__ . "/../../lang", $this->translator->folder);
    Assert::exception(function() {
      $this->translator->folder = NULL;
    }, \Exception::class, "Folder  does not exist.");
  }
  
  function testTranslateEn() {
    Assert::same("en", $this->translator->lang);
    // non-existing string
    Assert::type("string", $this->translator->translate("abc"));
    Assert::same("", $this->translator->translate("abc"));
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
  }
  
  function testTranslateCs() {
    $this->translator->lang = "cs";
    Assert::same("cs", $this->translator->lang);
    // non-existing string
    Assert::type("string", $this->translator->translate("abc"));
    Assert::same("", $this->translator->translate("abc"));
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
  }
  
  /**
   * Test non-existing language
   */
  function testTranslateX() {
    $this->translator->lang = "x";
    Assert::same("x", $this->translator->lang);
    // non-existing string
    Assert::type("string", $this->translator->translate("abc"));
    Assert::same("", $this->translator->translate("abc"));
    // existing string
    Assert::type("string", $this->translator->translate("book.content"));
    Assert::same("Content", $this->translator->translate("book.content"));
    // string existing only in default translation
    Assert::type("string", $this->translator->translate("test"));
    Assert::same("Test", $this->translator->translate("test"));
  }
}

$test = new TranslatorTest;
$test->run();
?>