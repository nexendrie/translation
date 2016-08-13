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
  private $translator;
  
  function setUp() {
    $this->translator = new Translator;
  }
  
  function testTranslateEn() {
    Assert::same("en", $this->translator->lang);
    // non-existing string
    Assert::type("string", $this->translator->translate("abc"));
    Assert::same("", $this->translator->translate("abc"));
    // existing string
    Assert::type("string", $this->translator->translate("content"));
    Assert::same("Content", $this->translator->translate("content"));
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
    Assert::type("string", $this->translator->translate("content"));
    Assert::same("Obsah", $this->translator->translate("content"));
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
    Assert::type("string", $this->translator->translate("content"));
    Assert::same("Content", $this->translator->translate("content"));
    // string existing only in default translation
    Assert::type("string", $this->translator->translate("test"));
    Assert::same("Test", $this->translator->translate("test"));
  }
}

$test = new TranslatorTest;
$test->run();
?>