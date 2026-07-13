<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteApplication;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\Group;
use MyTester\Attributes\RequiresPhpVersion;
use MyTester\Attributes\TestSuite;
use Nette\Application\Request;
use Nette\Application\Application;

#[TestSuite("ParamLocaleResolver")]
#[Group("localeResolvers")]
#[Group("nette")]
final class ParamLocaleResolverTest extends \MyTester\TestCase
{
    use \MyTester\Bridges\NetteDI\TCompiledContainer;

    protected ParamLocaleResolver $resolver;

    #[BeforeTest]
    public function setUp(): void
    {
        $config = [
            "translation" => [
                "localeResolver" => "param"
            ]
        ];
        $this->refreshContainer($config);
        $this->resolver = $this->getService(ParamLocaleResolver::class);
    }

    public function testResolve(): void
    {
        $parameters = [
            "callback" => function () {
                return "";
            }
        ];
        $this->assertNull($this->resolver->resolve());
        /** @var Application $application */
        $application = $this->getService(Application::class);
        $request = new Request("Micro", Request::FORWARD, $parameters);
        $this->assertNull($this->resolver->resolve());
        $request->setMethod("GET");
        $application->processRequest($request);
        $parameters["locale"] = "en";
        $request->setParameters($parameters);
        $application->processRequest($request);
        $this->assertSame("en", $this->resolver->resolve());
    }

    public function testCustomParamName(): void
    {
        $this->resolver->setParam("language");
        $this->assertSame("language", $this->resolver->getParam());
        $parameters = [
            "callback" => function () {
                return "";
            }, "language" => "en",
        ];
        /** @var Application $application */
        $application = $this->getService(Application::class);
        $request = new Request("Micro", "GET", $parameters);
        $application->processRequest($request);
        $this->assertSame("en", $this->resolver->resolve());
    }
}
