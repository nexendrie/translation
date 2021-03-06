<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Nexendrie\Translation\Resolvers\ManualLocaleResolver;

require __DIR__ . "/../../../bootstrap.php";


/**
 * @author Jakub Konečný
 * @testCase
 */
final class PhpLoaderTest extends FileLoaderTestAbstract {
  protected function setUp() {
    $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
    $this->loader = new PhpLoader(new ManualLocaleResolver(), $folders);
  }
}

$test = new PhpLoaderTest();
$test->run();
?>