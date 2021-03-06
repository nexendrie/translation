<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Tester\Assert;
use Nexendrie\Translation\Resolvers\ManualLocaleResolver;
use Nexendrie\Translation\CatalogueCompiler;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class MessagesCatalogueTest extends FileLoaderTestAbstract {
  protected function setUp(): void {
    $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
    $folder = __DIR__ . "/../../../_temp/catalogues";
    $loader = new NeonLoader(new ManualLocaleResolver(), $folders);
    $compiler = new CatalogueCompiler($loader, $folder, ["en", "cs"]);
    $compiler->compile();
    $this->loader = new MessagesCatalogue(new ManualLocaleResolver(), [$folder]);
  }
  
  public function testGetFolders(): void {
    $folders = $this->loader->folders;
    Assert::type("array", $folders);
    Assert::count(1, $folders);
    Assert::same(__DIR__ . "/../../../_temp/catalogues", $folders[0]);
  }
  
  public function testCatalogueWithoutResources(): void {
    $folder = __DIR__ . "/../../../catalogue";
    $loader = new MessagesCatalogue(new ManualLocaleResolver(), [$folder]);
    $loader->getTexts();
    Assert::count(3, $loader->resources);
  }
}

$test = new MessagesCatalogueTest();
$test->run();
?>