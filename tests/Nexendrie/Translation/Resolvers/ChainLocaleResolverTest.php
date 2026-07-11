<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\TestSuite;

#[TestSuite("ChainLocaleResolver")]
final class ChainLocaleResolverTest extends \MyTester\TestCase
{
    protected ChainLocaleResolver $resolver;

    #[BeforeTest]
    public function setUp(): void
    {
        $this->resolver = new ChainLocaleResolver();
    }

    public function testResolve(): void
    {
        $this->assertNull($this->resolver->resolve());
        $this->resolver[] = new ManualLocaleResolver();
        $this->assertNull($this->resolver->resolve());
        $resolver = new ManualLocaleResolver();
        $this->resolver[] = $resolver;
        $resolver->lang = "en";
        $this->assertSame("en", $this->resolver->resolve());
    }
}
