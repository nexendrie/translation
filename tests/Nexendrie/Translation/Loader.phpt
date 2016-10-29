<?php
namespace Nexendrie\Translation;

use Tester\Assert,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver;

require __DIR__ . "/../../bootstrap.php";

class LoaderTest extends \Tester\TestCase {
  /** @var Loader */
  protected $loader;
  
  function setUp() {
    $this->loader = new Loader("en", __DIR__ . "/../../lang", new ManualLocaleResolver());
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
  
  function testGetDefaultLang() {
    $lang = $this->loader->defaultLang;
    Assert::type("string", $lang);
    Assert::same("en", $lang);
  }
  
  function testSetDefaultLang() {
    $this->loader->defaultLang = "cs";
    $lang = $this->loader->defaultLang;
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
  
  function testGetResources() {
    // texts were not loaded yet so there are no resources
    $resources = $this->loader->resources;
    Assert::type("array", $resources);
    Assert::count(0, $resources);
    // english texts are loaded, there is 1 resource for each domain
    $this->loader->getTexts();
    $resources = $this->loader->resources;
    Assert::type("array", $resources);
    Assert::count(2, $resources);
    Assert::count(1, $resources["messages"]);
    Assert::count(1, $resources["book"]);
    // czech and english texts are loaded so there are 2 resources for each domain
    $this->loader->lang = "cs";
    $this->loader->getTexts();
    $resources = $this->loader->resources;
    Assert::type("array", $resources);
    Assert::count(2, $resources);
    Assert::count(2, $resources["messages"]);
    Assert::count(2, $resources["book"]);
  }
  
  function testGetTexts() {
    $texts = $this->loader->texts;
    Assert::type("array", $texts);
    Assert::count(2, $texts);
    Assert::type("array", $texts["messages"]);
    Assert::count(3, $texts["messages"]);
    Assert::type("array", $texts["book"]);
    Assert::count(5, $texts["book"]);
  }
  
  function testNoFolder() {
    Assert::exception(function() {
      $this->loader = new Loader;
      $this->loader->getTexts();
    }, \Exception::class, "Folder for translations was not set.");
  }
}

$test = new LoaderTest;
$test->run();
?>