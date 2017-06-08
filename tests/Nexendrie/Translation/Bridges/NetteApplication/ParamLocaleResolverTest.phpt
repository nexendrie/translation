<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteApplication;

use Tester\Assert,
    Nette\Application\Request,
    Nette\Application\Application;

require __DIR__ . "/../../../../bootstrap.php";

class ParamLocaleResolverTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var ParamLocaleResolver */
  protected $resolver;
  
  function setUp() {
    $config = [
      "translation" => [
        "localeResolver" => "param"
      ]
    ];
    $this->refreshContainer($config);
    $this->resolver = $this->getService(ParamLocaleResolver::class);
  }
  
  function testResolve() {
    $callback = function() {
      return "";
    };
    Assert::null($this->resolver->resolve());
    /** @var Application $application */
    $application = $this->getService(Application::class);
    $request = new Request("Micro", Request::FORWARD, ["callback" => $callback]);
    $application->processRequest($request);
    Assert::null($this->resolver->resolve());
    $request = new Request("Micro", NULL, ["callback" => $callback]);
    $application->processRequest($request);
    $request = new Request("Micro", NULL, [
      "callback" => $callback, "locale" => "en"
    ]);
    $application->processRequest($request);
    Assert::same("en", $this->resolver->resolve());
  }
}

$test = new ParamLocaleResolverTest;
$test->run();
?>