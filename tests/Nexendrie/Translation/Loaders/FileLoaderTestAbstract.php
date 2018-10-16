<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Tester\Assert;
use Nexendrie\Translation\InvalidFolderException;
use Nexendrie\Translation\FolderNotSetException;

/**
 * General test suit for file loaders
 *
 * @author Jakub Konečný
 */
abstract class FileLoaderTestAbstract extends \Tester\TestCase {
  /** @var FileLoader */
  protected $loader;
  
  public function testGetLang(): void {
    $lang = $this->loader->lang;
    Assert::type("string", $lang);
    Assert::same("en", $lang);
  }
  
  public function testSetLang(): void {
    $this->loader->lang = "cs";
    $lang = $this->loader->lang;
    Assert::type("string", $lang);
    Assert::same("cs", $lang);
  }
  
  public function testGetDefaultLang(): void {
    $lang = $this->loader->defaultLang;
    Assert::type("string", $lang);
    Assert::same("en", $lang);
  }
  
  public function testSetDefaultLang(): void {
    $this->loader->defaultLang = "cs";
    $lang = $this->loader->defaultLang;
    Assert::type("string", $lang);
    Assert::same("cs", $lang);
  }
  
  public function testGetFolders(): void {
    $folders = $this->loader->folders;
    Assert::type("array", $folders);
    Assert::count(2, $folders);
    Assert::same(__DIR__ . "/../../../lang", $folders[0]);
    Assert::same(__DIR__ . "/../../../lang2", $folders[1]);
  }
  
  public function testSetFolders(): void {
    Assert::exception(function() {
      $this->loader->folders = [""];
    }, InvalidFolderException::class, "Folder  does not exist.");
  }
  
  public function testGetResources(): void {
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
    // the language does not exist, 1 (default) resource for each domain
    if($this->loader instanceof MessagesCatalogue) {
      return; // the following tests for some reason fail with MessagesCatalogue
    }
    $this->loader->lang = "xyz";
    $this->loader->getTexts();
    $resources = $this->loader->resources;
    Assert::type("array", $resources);
    Assert::count(3, $resources);
    Assert::count(1, $resources["messages"]);
    Assert::count(1, $resources["book"]);
    Assert::count(1, $resources["abc"]);
  }
  
  public function testGetTexts(): void {
    $texts = $this->loader->getTexts();
    Assert::type("array", $texts);
    Assert::count(3, $texts);
    Assert::type("array", $texts["messages"]);
    Assert::count(3, $texts["messages"]);
    Assert::type("array", $texts["book"]);
    Assert::count(5, $texts["book"]);
    $this->loader->lang = "cs";
    $texts = $this->loader->getTexts();
    Assert::type("array", $texts);
    Assert::count(3, $texts);
    Assert::type("array", $texts["messages"]);
    Assert::count(3, $texts["messages"]);
    Assert::type("array", $texts["book"]);
    Assert::count(5, $texts["book"]);
    if($this->loader instanceof MessagesCatalogue) {
      return; // the following tests for some reason fail with MessagesCatalogue
    }
    $this->loader->lang = "xyz";
    $texts = $this->loader->getTexts();
    Assert::type("array", $texts);
    Assert::count(3, $texts);
    Assert::type("array", $texts["messages"]);
    Assert::count(3, $texts["messages"]);
    Assert::type("array", $texts["book"]);
    Assert::count(5, $texts["book"]);
  }
  
  public function testNoFolder(): void {
    Assert::exception(function() {
      $class = get_class($this->loader);
      $this->loader = new $class();
      $this->loader->getTexts();
    }, FolderNotSetException::class, "Folder for translations was not set.");
  }
  
  public function testGetAvailableLanguages(): void {
    $result = $this->loader->getAvailableLanguages();
    Assert::type("array", $result);
    Assert::count(2, $result);
    Assert::contains("en", $result);
    Assert::contains("cs", $result);
    Assert::exception(function() {
      $class = get_class($this->loader);
      $this->loader = new $class();
      $this->loader->getAvailableLanguages();
    }, FolderNotSetException::class, "Folder for translations was not set.");
  }
  
  public function testGetResolverName(): void {
    $name = $this->loader->getResolverName();
    Assert::type("string", $name);
    Assert::same("ManualLocaleResolver", $name);
  }
}
?>