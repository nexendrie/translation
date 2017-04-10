<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Tester\Assert,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\CatalogueCompiler;

require __DIR__ . "/../../../bootstrap.php";

class MessagesCatalogueTestAbstract extends FileLoaderTestAbstract {
  function setUp() {
    $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
    $folder = __DIR__ . "/../../../_temp/catalogues";
    $loader = new NeonLoader(new ManualLocaleResolver(), $folders);
    $compiler = new CatalogueCompiler($loader, $folder, ["en", "cs"]);
    $compiler->compile();
    $this->loader = new MessagesCatalogue(new ManualLocaleResolver(), [$folder]);
  }
  
  function testGetFolders() {
    $folders = $this->loader->folders;
    Assert::type("array", $folders);
    Assert::count(1, $folders);
    Assert::same(__DIR__ . "/../../../_temp/catalogues", $folders[0]);
  }
}

$test = new MessagesCatalogueTestAbstract;
$test->run();
?>