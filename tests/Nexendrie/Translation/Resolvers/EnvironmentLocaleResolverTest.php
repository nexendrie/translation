<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;

#[TestSuite("EnvironmentLocaleResolver")]
#[Group("localeResolvers")]
final class EnvironmentLocaleResolverTest extends \MyTester\TestCase
{
    protected EnvironmentLocaleResolver $resolver;

    #[BeforeTest]
    public function setUp(): void
    {
        $this->resolver = new EnvironmentLocaleResolver();
    }

    public function testResolve(): void
    {
        $this->assertNull($this->resolver->resolve());
        $this->resolver->lang = "cs";
        $this->assertSame("cs", $this->resolver->resolve());
        $this->resolver->lang = null;
        $this->assertNull($this->resolver->resolve());
    }

    public function testCustomVarName(): void
    {
        $oldValue = $this->resolver->getVarName();
        $this->resolver->setVarName("LANGUAGE");
        $this->assertSame("LANGUAGE", $this->resolver->getVarName());
        $this->resolver->lang = "cs";
        $this->assertSame("cs", $this->resolver->resolve());
        $this->resolver->varName = $oldValue;
    }
}
