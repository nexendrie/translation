<?php
namespace Nexendrie\Translation\Loaders;

use Tester\Assert,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\FolderNotSetException;

require __DIR__ . "/../../../bootstrap.php";

class MessagesCatalogueTest extends \Tester\TestCase {
  use TFileLoaderTest;
  
  function setUp() {
    $folders = [__DIR__ . "/../../../catalogues"];
    $this->loader = new MessagesCatalogue(new ManualLocaleResolver(), $folders);
  }
  
  function testGetFolders() {
    $folders = $this->loader->folders;
    Assert::type("array", $folders);
    Assert::count(1, $folders);
    Assert::same(__DIR__ . "/../../../catalogues", $folders[0]);
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