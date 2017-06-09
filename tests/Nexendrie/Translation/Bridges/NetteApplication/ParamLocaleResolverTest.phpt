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
    $parameters = [
      "callback" => function() {
        return "";
      }
    ];
    Assert::null($this->resolver->resolve());
    /** @var Application $application */
    $application = $this->getService(Application::class);
    $request = new Request("Micro", Request::FORWARD, $parameters);
    Assert::null($this->resolver->resolve());
    $request->method = NULL;
    $application->processRequest($request);
    $parameters["locale"] = "en";
    $request->parameters = $parameters;
    $application->processRequest($request);
    Assert::same("en", $this->resolver->resolve());
  }
  
  function testCustomParamName() {
    $this->resolver->param = "language";
    Assert::same("language", $this->resolver->param);
    $parameters = [
      "callback" => function() {
        return "";
      }, "language" => "en",
    ];
    /** @var Application $application */
    $application = $this->getService(Application::class);
    $request = new Request("Micro", NULL, $parameters);
    $application->processRequest($request);
    Assert::same("en", $this->resolver->resolve());
  }
}

$test = new ParamLocaleResolverTest;
$test->run();
?>