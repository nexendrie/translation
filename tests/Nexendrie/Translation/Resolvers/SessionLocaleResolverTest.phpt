<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

class SessionLocaleResolverTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var SessionLocaleResolver */
  protected $resolver;
  
  protected function setUp() {
    $this->resolver = new SessionLocaleResolver();
  }
  
  public function testResolver() {
    Assert::null($this->resolver->resolve());
    $this->resolver->lang = "en";
    Assert::same("en", $this->resolver->resolve());
  }
  
  public function testCustomVarName() {
    $this->resolver->varName = "locale";
    Assert::same("locale", $this->resolver->varName);
    $this->resolver->lang = "en";
    Assert::same("en", $this->resolver->resolve());
  }
}

$test = new SessionLocaleResolverTest();
$test->run();
?>