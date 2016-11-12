<?php
namespace Nexendrie\Translation\Loaders;

use Tester\Assert,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\InvalidFolderException,
    Nexendrie\Translation\FolderNotSetException;

require __DIR__ . "/../../../bootstrap.php";

class YamlLoaderTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var YamlLoader */
  protected $loader;
  
  function setUp() {
    $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
    $this->loader = new YamlLoader(new ManualLocaleResolver(), $folders);
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
    $folders = $this->loader->folders;
    Assert::type("array", $folders);
    Assert::count(2, $folders);
    Assert::same(__DIR__ . "/../../../lang", $folders[0]);
    Assert::same(__DIR__ . "/../../../lang2", $folders[1]);
  }
  
  function testSetFolder() {
    Assert::exception(function() {
      $this->loader->folders = [""];
    }, InvalidFolderException::class, "Folder  does not exist.");
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
    Assert::count(3, $resources);
    Assert::count(1, $resources["messages"]);
    Assert::count(1, $resources["book"]);
    Assert::count(1, $resources["abc"]);
    // czech and english texts are loaded, there are 2 resources for each domain
    $this->loader->lang = "cs";
    $this->loader->getTexts();
    $resources = $this->loader->resources;
    Assert::type("array", $resources);
    Assert::count(3, $resources);
    Assert::count(2, $resources["messages"]);
    Assert::count(2, $resources["book"]);
    Assert::count(2, $resources["abc"]);
  }
  
  function testGetTexts() {
    $texts = $this->loader->texts;
    Assert::type("array", $texts);
    Assert::count(3, $texts);
    Assert::type("array", $texts["messages"]);
    Assert::count(3, $texts["messages"]);
    Assert::type("array", $texts["book"]);
    Assert::count(5, $texts["book"]);
  }
  
  function testNoFolder() {
    Assert::exception(function() {
      $this->loader = new NeonLoader;
      $this->loader->getTexts();
    }, FolderNotSetException::class, "Folder for translations was not set.");
  }
}

$test = new YamlLoaderTest;
$test->run();
?>