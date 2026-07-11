<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;

#[TestSuite("FallbackLocaleResolver")]
#[Group("localeResolvers")]
final class FallbackLocaleResolverTest extends \MyTester\TestCase
{
    protected FallbackLocaleResolver $resolver;

    #[BeforeTest]
    public function setUp(): void
    {
        $this->resolver = new FallbackLocaleResolver();
    }

    public function testResolve(): void
    {
        $lang = $this->resolver->resolve();
        $this->assertType("null", $lang);
    }
}
