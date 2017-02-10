<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Nexendrie\Translation\Resolvers\ManualLocaleResolver;

require __DIR__ . "/../../../bootstrap.php";

class IniLoaderTest extends FileLoaderTest {
  function setUp() {
    $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
    $this->loader = new IniLoader(new ManualLocaleResolver(), $folders);
  }
}

$test = new IniLoaderTest;
$test->run();
?>