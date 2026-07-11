<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\TestSuite;

#[TestSuite("ManualLocaleResolver")]
final class ManualLocaleResolverTest extends \MyTester\TestCase
{
    protected ManualLocaleResolver $resolver;

    #[BeforeTest]
    public function setUp(): void
    {
        $this->resolver = new ManualLocaleResolver();
    }

    public function testResolve(): void
    {
        $this->assertNull($this->resolver->resolve());
        $this->resolver->lang = "cs";
        $this->assertSame("cs", $this->resolver->resolve());
        $this->assertSame("cs", $this->resolver->getLang());
        $this->resolver->lang = null;
        $this->assertNull($this->resolver->resolve());
        $this->assertNull($this->resolver->getLang());
    }
}
