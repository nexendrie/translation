<?php
namespace Nexendrie\Translation;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

class LoaderTest extends \Tester\TestCase {
  /** @var Loader */
  protected $loader;
  
  function setUp() {
    $this->loader = new Loader("en", __DIR__ . "/../../lang", new Resolvers\ManualLocaleResolver());
  }
  
  function testGetLang() {
    $lang = $this->loader->lang;
    Assert::type("string", $lang);
    Assert::same("en", $lang);
  }
  
  function testSetLang() {
    $this->loader->lang = "cs";
    $lang = $this->loader->lang;
    Assert::type("string", $lang);
    Assert::same("cs", $lang);
  }
  
  function testGetFolder() {
    $folder = $this->loader->folder;
    Assert::type("string", $folder);
    Assert::same(__DIR__ . "/../../lang", $folder);
  }
  
  function testSetFolder() {
    Assert::exception(function() {
      $this->loader->folder = NULL;
    }, \Exception::class, "Folder  does not exist.");
  }
  
  function testGetTexts() {
    $texts = $this->loader->texts;
    Assert::type("array", $texts);
    Assert::count(2, $texts);
    Assert::type("array", $texts["messages"]);
    Assert::count(2, $texts["messages"]);
    Assert::type("array", $texts["book"]);
    Assert::count(5, $texts["book"]);
  }
}

$test = new LoaderTest;
$test->run();
?>