<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

final class ChainLocaleResolverTest extends \Tester\TestCase {
  /** @var ChainLocaleResolver */
  protected $resolver;
  
  protected function setUp() {
    $this->resolver = new ChainLocaleResolver();
  }
  
  public function testResolve() {
    Assert::null($this->resolver->resolve());
    $this->resolver->addResolver(new ManualLocaleResolver);
    Assert::null($this->resolver->resolve());
    $resolver = new ManualLocaleResolver();
    $this->resolver->addResolver($resolver);
    $resolver->lang = "en";
    Assert::same("en", $this->resolver->resolve());
  }
}

$test = new ChainLocaleResolverTest();
$test->run();
?>