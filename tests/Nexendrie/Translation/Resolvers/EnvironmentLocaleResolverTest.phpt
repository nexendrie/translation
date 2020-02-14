<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

final class EnvironmentLocaleResolverTest extends \Tester\TestCase {
  /** @var EnvironmentLocaleResolver */
  protected $resolver;
  
  protected function setUp() {
    $this->resolver = new EnvironmentLocaleResolver();
  }
  
  public function testResolve() {
    Assert::null($this->resolver->resolve());
    $this->resolver->lang = "cs";
    Assert::same("cs", $this->resolver->resolve());
    $this->resolver->lang = null;
    Assert::null($this->resolver->resolve());
  }
  
  public function testCustomVarName() {
    $oldValue = $this->resolver->varName;
    Assert::type("string", $oldValue);
    $this->resolver->varName = "LANGUAGE";
    $this->resolver->lang = "cs";
    Assert::same("cs", $this->resolver->resolve());
    $this->resolver->varName = $oldValue;
  }
}

$test = new EnvironmentLocaleResolverTest();
$test->run();
?>