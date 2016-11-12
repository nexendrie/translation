<?php
namespace Nexendrie\Translation\Loaders;

use Nexendrie\Translation\Resolvers\ManualLocaleResolver;

require __DIR__ . "/../../../bootstrap.php";


class JsonLoaderTest extends \Tester\TestCase {
  use TFileLoaderTest;
  
  function setUp() {
    $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
    $this->loader = new JsonLoader(new ManualLocaleResolver(), $folders);
  }
}

$test = new JsonLoaderTest;
$test->run();
?>