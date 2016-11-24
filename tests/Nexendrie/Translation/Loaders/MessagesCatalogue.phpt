<?php
namespace Nexendrie\Translation\Loaders;

use Tester\Assert,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\CatalogueCompiler,
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
  
  function testNoFolder() {
    Assert::exception(function() {
      $this->loader = new MessagesCatalogue();
      $this->loader->getTexts();
    }, FolderNotSetException::class, "Folder for translations was not set.");
  }
  
  function testGetAvailableLanguages() {
    $result = $this->loader->getAvailableLanguages();
    Assert::type("array", $result);
    Assert::count(2, $result);
    Assert::contains("en", $result);
    Assert::contains("cs", $result);
    Assert::exception(function() {
      $this->loader = new MessagesCatalogue();
      $this->loader->getAvailableLanguages();
    }, FolderNotSetException::class, "Folder for translations was not set.");
  }
}

$test = new MessagesCatalogueTest;
$test->run();
?>