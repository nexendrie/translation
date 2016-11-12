<?php
namespace Nexendrie\Translation\Loaders;

use Nexendrie\Translation\Resolvers\ManualLocaleResolver;

require __DIR__ . "/../../../bootstrap.php";

class IniLoaderTest extends \Tester\TestCase {
  /** @var IniLoader */
  protected $loader;
  
  use TFileLoaderTest;
  
  function setUp() {
    $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
    $this->loader = new IniLoader(new ManualLocaleResolver(), $folders);
  }
}

$test = new IniLoaderTest;
$test->run();
?>