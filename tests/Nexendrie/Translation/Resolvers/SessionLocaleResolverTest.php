<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nette\Http\Session;

#[TestSuite("SessionLocaleResolver")]
#[Group("localeResolvers")]
final class SessionLocaleResolverTest extends \MyTester\TestCase
{
    use \MyTester\Bridges\NetteDI\TCompiledContainer;

    protected SessionLocaleResolver $resolver;

    #[BeforeTest]
    public function setUp(): void
    {
        $this->resolver = new SessionLocaleResolver($this->getService(Session::class));
    }

    public function testResolver(): void
    {
        $this->assertNull($this->resolver->resolve());
        $this->resolver->lang = "en";
        $this->assertSame("en", $this->resolver->resolve());
        $this->resolver->lang = null;
        $this->assertNull($this->resolver->resolve());
    }

    public function testCustomVarName(): void
    {
        $this->resolver->setVarName("locale");
        $this->assertSame("locale", $this->resolver->getVarName());
        $this->resolver->lang = "en";
        $this->assertSame("en", $this->resolver->resolve());
    }
}
