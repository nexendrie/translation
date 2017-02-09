<?php
namespace Nexendrie\Translation\Loaders;

use Nexendrie\Translation\Resolvers\ManualLocaleResolver;

require __DIR__ . "/../../../bootstrap.php";


class PhpLoaderTest extends FileLoaderTest {
  function setUp() {
    $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
    $this->loader = new PhpLoader(new ManualLocaleResolver(), $folders);
  }
}

$test = new PhpLoaderTest;
$test->run();
?>