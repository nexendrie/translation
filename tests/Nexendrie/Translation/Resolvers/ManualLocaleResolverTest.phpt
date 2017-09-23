<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

class ManualLocaleResolverTest extends \Tester\TestCase {
  /** @var ManualLocaleResolver */
  protected $resolver;
  
  protected function setUp() {
    $this->resolver = new ManualLocaleResolver();
  }
  
  public function testResolve() {
    $lang = $this->resolver->resolve();
    Assert::type("null", $lang);
    $this->resolver->lang = "cs";
    $lang = $this->resolver->resolve();
    Assert::type("string", $lang);
    Assert::same("cs", $lang);
  }
}

$test = new ManualLocaleResolverTest();
$test->run();
?>