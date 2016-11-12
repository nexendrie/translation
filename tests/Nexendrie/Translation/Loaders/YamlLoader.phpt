<?php
namespace Nexendrie\Translation\Loaders;

use Nexendrie\Translation\Resolvers\ManualLocaleResolver;

require __DIR__ . "/../../../bootstrap.php";

class YamlLoaderTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  use TFileLoaderTest;
  
  /** @var YamlLoader */
  protected $loader;
  
  function setUp() {
    $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
    $this->loader = new YamlLoader(new ManualLocaleResolver(), $folders);
  }
}

$test = new YamlLoaderTest;
$test->run();
?>