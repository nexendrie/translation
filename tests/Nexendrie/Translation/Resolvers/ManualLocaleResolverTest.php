<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class ManualLocaleResolverTest extends \Tester\TestCase {
  protected ManualLocaleResolver $resolver;
  
  protected function setUp(): void {
    $this->resolver = new ManualLocaleResolver();
  }
  
  public function testResolve(): void {
    Assert::null($this->resolver->resolve());
    $this->resolver->lang = "cs";
    Assert::same("cs", $this->resolver->resolve());
    $this->resolver->lang = null;
    Assert::null($this->resolver->resolve());
  }
}

$test = new ManualLocaleResolverTest();
$test->run();
?>