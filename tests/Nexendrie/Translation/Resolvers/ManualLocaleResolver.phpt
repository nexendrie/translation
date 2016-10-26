<?php
namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";


class ManualLocaleResolverTest extends \Tester\TestCase {
  /** @var ManualLocaleResolver */
  protected $resolver;
  
  function setUp() {
    $this->resolver = new ManualLocaleResolver;
  }
  
  function testResolve() {
    $lang = $this->resolver->resolve();
    Assert::type("string", $lang);
    Assert::same("en", $lang);
    $this->resolver->lang = "cs";
    $lang = $this->resolver->resolve();
    Assert::type("string", $lang);
    Assert::same("cs", $lang);
  }
}

$test = new ManualLocaleResolverTest;
$test->run();
?>