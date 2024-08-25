<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class FallbackLocaleResolverTest extends \Tester\TestCase {
  protected FallbackLocaleResolver $resolver;
  
  protected function setUp(): void {
    $this->resolver = new FallbackLocaleResolver();
  }
  
  public function testResolve(): void {
    $lang = $this->resolver->resolve();
    Assert::type("null", $lang);
  }
}

$test = new FallbackLocaleResolverTest();
$test->run();
?>