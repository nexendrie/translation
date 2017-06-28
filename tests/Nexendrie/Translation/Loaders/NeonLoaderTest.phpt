<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Nexendrie\Translation\Resolvers\ManualLocaleResolver;

require __DIR__ . "/../../../bootstrap.php";

class NeonLoaderTest extends FileLoaderTestAbstract {
  protected function setUp() {
    $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
    $this->loader = new NeonLoader(new ManualLocaleResolver(), $folders);
  }
}

$test = new NeonLoaderTest;
$test->run();
?>