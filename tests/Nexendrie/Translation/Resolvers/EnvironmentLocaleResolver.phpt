<?php
namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

class EnvironmentLocaleResolverTest extends \Tester\TestCase {
  /** @var EnvironmentLocaleResolver */
  protected $resolver;
  
  function setUp() {
    $this->resolver = new EnvironmentLocaleResolver;
  }
  
  function testResolve() {
    $lang = $this->resolver->resolve();
    Assert::type("null", $lang);
    putenv(EnvironmentLocaleResolver::VARNAME . "=cs");
    $lang = $this->resolver->resolve();
    Assert::type("string", $lang);
    Assert::same("cs", $lang);
  }
}

$test = new EnvironmentLocaleResolverTest;
$test->run();
?>