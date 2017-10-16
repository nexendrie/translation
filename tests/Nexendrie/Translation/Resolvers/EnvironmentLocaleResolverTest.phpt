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
    $lang = $this->resolver->resolve();
    Assert::type("null", $lang);
    $this->resolver->lang = "cs";
    $lang = $this->resolver->resolve();
    Assert::type("string", $lang);
    Assert::same("cs", $lang);
  }
  
  public function testCustomVarName() {
    $oldValue = $this->resolver->varName;
    Assert::type("string", $oldValue);
    $this->resolver->varName = "LANGUAGE";
    $this->resolver->lang = "cs";
    $lang = $this->resolver->resolve();
    Assert::type("string", $lang);
    Assert::same("cs", $lang);
    $this->resolver->varName = $oldValue;
  }
}

$test = new EnvironmentLocaleResolverTest();
$test->run();
?>