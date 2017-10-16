<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

final class FallbackLocaleResolverTest extends \Tester\TestCase {
  /** @var FallbackLocaleResolver */
  protected $resolver;
  
  protected function setUp() {
    $this->resolver = new FallbackLocaleResolver();
  }
  
  public function testResolve() {
    $lang = $this->resolver->resolve();
    Assert::type("null", $lang);
  }
}

$test = new FallbackLocaleResolverTest();
$test->run();
?>