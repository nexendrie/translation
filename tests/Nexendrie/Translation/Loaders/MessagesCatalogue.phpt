<?php
namespace Nexendrie\Translation\Loaders;

use Tester\Assert,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\CatalogueCompiler,
    Nexendrie\Translation\Loaders\NeonLoader,
    Nexendrie\Translation\FolderNotSetException;

require __DIR__ . "/../../../bootstrap.php";

class MessagesCatalogueTest extends \Tester\TestCase {
  use TFileLoaderTest;
  
  function setUp() {
    $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
    $folder = __DIR__ . "/../../../_temp/catalogues";
    $loader = new NeonLoader(new ManualLocaleResolver(), $folders);
    $compiler = new CatalogueCompiler($loader, ["en", "cs"], $folder);
    $compiler->compile();
    $this->loader = new MessagesCatalogue(new ManualLocaleResolver(), [$folder]);
  }
  
  function testGetFolders() {
    $folders = $this->loader->folders;
    Assert::type("array", $folders);
    Assert::count(1, $folders);
    Assert::same(__DIR__ . "/../../../_temp/catalogues", $folders[0]);
  }
  
  function testGetResources() {
    // texts were not loaded yet so there are no resources
    $resources = $this->loader->resources;
    Assert::type("array", $resources);
    Assert::count(0, $resources);
    // english texts are loaded, there is 1 resource
    $this->loader->getTexts();
    $resources = $this->loader->resources;
    Assert::type("array", $resources);
    Assert::count(1, $resources);
    Assert::count(1, $resources["*"]);
    // czech and english texts are loaded, there is 1 resource
    $this->loader->lang = "cs";
    $this->loader->getTexts();
    $resources = $this->loader->resources;
    Assert::type("array", $resources);
    Assert::count(1, $resources);
    Assert::count(1, $resources["*"]);
  }
  
  function testNoFolder() {
    Assert::exception(function() {
      $this->loader = new MessagesCatalogue();
      $this->loader->getTexts();
    }, FolderNotSetException::class, "Folder for translations was not set.");
  }
}

$test = new MessagesCatalogueTest;
$test->run();
?>