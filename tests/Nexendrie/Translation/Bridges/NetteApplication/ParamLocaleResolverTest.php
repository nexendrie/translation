<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteApplication;

use Tester\Assert;
use Nette\Application\Request;
use Nette\Application\Application;

require __DIR__ . "/../../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class ParamLocaleResolverTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;

  protected ParamLocaleResolver $resolver;
  
  protected function setUp(): void {
    $config = [
      "translation" => [
        "localeResolver" => "param"
      ]
    ];
    $this->refreshContainer($config);
    $this->resolver = $this->getService(ParamLocaleResolver::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testResolve(): void {
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
    $request->method = "GET";
    $application->processRequest($request);
    $parameters["locale"] = "en";
    $request->parameters = $parameters;
    $application->processRequest($request);
    Assert::same("en", $this->resolver->resolve());
  }

  public function testCustomParamName(): void {
    $this->resolver->param = "language";
    $parameters = [
      "callback" => function() {
        return "";
      }, "language" => "en",
    ];
    /** @var Application $application */
    $application = $this->getService(Application::class);
    $request = new Request("Micro", "GET", $parameters);
    $application->processRequest($request);
    Assert::same("en", $this->resolver->resolve());
  }
}

$test = new ParamLocaleResolverTest();
$test->run();
?>