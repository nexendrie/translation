<?php
namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

class FallbackLocaleResolverTest extends \Tester\TestCase {
  /** @var FallbackLocaleResolver */
  protected $resolver;
  
  function setUp() {
    $this->resolver = new FallbackLocaleResolver;
  }
  
  function testResolve() {
    $lang = $this->resolver->resolve();
    Assert::type("null", $lang);
  }
}

$test = new FallbackLocaleResolverTest;
$test->run();
?>