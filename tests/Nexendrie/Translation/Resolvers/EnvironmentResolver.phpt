<?php
namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

class EnvironmentResolverTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var EnvironmentResolver */
  protected $resolver;
  
  function setUp() {
    $this->resolver = new EnvironmentResolver;
  }
  
  function testResolve() {
    $lang = $this->resolver->resolve();
    Assert::type("string", $lang);
    Assert::same("en", $lang);
    putenv(EnvironmentResolver::VARNAME . "=cs");
    $lang = $this->resolver->resolve();
    Assert::type("string", $lang);
    Assert::same("cs", $lang);
  }
}

$test = new EnvironmentResolverTest;
$test->run();
?>