<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteHttp;

use Tester\Assert;

require __DIR__ . "/../../../../bootstrap.php";

class SessionLocaleResolverTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var SessionLocaleResolver */
  protected $resolver;
  
  function setUp() {
    $config = [
      "translation" => [
        "localeResolver" => "session"
      ]
    ];
    $this->refreshContainer($config);
    $this->resolver = $this->getService(SessionLocaleResolver::class);
  }
  
  function testResolver() {
    Assert::null($this->resolver->resolve());
    $this->resolver->lang = "en";
    Assert::same("en", $this->resolver->resolve());
  }
}

$test = new SessionLocaleResolverTest;
$test->run();
?>