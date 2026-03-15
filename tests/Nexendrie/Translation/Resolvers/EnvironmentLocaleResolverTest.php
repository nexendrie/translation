<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class EnvironmentLocaleResolverTest extends \Tester\TestCase
{
    protected EnvironmentLocaleResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new EnvironmentLocaleResolver();
    }

    public function testResolve(): void
    {
        Assert::null($this->resolver->resolve());
        $this->resolver->lang = "cs";
        Assert::same("cs", $this->resolver->resolve());
        $this->resolver->lang = null;
        Assert::null($this->resolver->resolve());
    }

    public function testCustomVarName(): void
    {
        $oldValue = $this->resolver->getVarName();
        $this->resolver->setVarName("LANGUAGE");
        Assert::same("LANGUAGE", $this->resolver->getVarName());
        $this->resolver->lang = "cs";
        Assert::same("cs", $this->resolver->resolve());
        $this->resolver->varName = $oldValue;
    }
}

$test = new EnvironmentLocaleResolverTest();
$test->run();
