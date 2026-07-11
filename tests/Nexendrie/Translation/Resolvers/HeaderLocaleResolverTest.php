<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use Nexendrie\Translation\Loader;
use Nexendrie\Translation\LoaderNotSetException;

#[TestSuite("HeaderLocaleResolver")]
#[Group("localeResolvers")]
final class HeaderLocaleResolverTest extends \MyTester\TestCase
{
    use \MyTester\Bridges\NetteDI\TCompiledContainer;

    protected function prepareRequest(string $language): Request
    {
        $headers = [
            "Accept-Language" => $language
        ];
        return new Request(new UrlScript(), headers: $headers);
    }

    public function testResolve(): void
    {
        $resolver = new HeaderLocaleResolver();
        $this->assertThrowsException(function () use ($resolver) {
            $resolver->resolve();
        }, LoaderNotSetException::class);

        $loader = $this->getService(Loader::class);
        $resolver->setLoader($loader);
        $this->assertNull($resolver->resolve());

        $resolver = new HeaderLocaleResolver($this->prepareRequest("zh"));
        $resolver->setLoader($loader);
        $this->assertNull($resolver->resolve());

        $resolver = new HeaderLocaleResolver($this->prepareRequest("en"));
        $resolver->setLoader($loader);
        $this->assertSame("en", $resolver->resolve());

        $resolver = new HeaderLocaleResolver($this->prepareRequest("zh,en"));
        $resolver->setLoader($loader);
        $this->assertSame("en", $resolver->resolve());

        $resolver = new HeaderLocaleResolver($this->prepareRequest("cs,en"));
        $resolver->setLoader($loader);
        $this->assertSame("cs", $resolver->resolve());

        $resolver = new HeaderLocaleResolver($this->prepareRequest("zh,en;q=0.8"));
        $resolver->setLoader($loader);
        $this->assertSame("en", $resolver->resolve());

        $resolver = new HeaderLocaleResolver($this->prepareRequest("cs;q=0.7,en;q=0.8"));
        $resolver->setLoader($loader);
        $this->assertSame("en", $resolver->resolve());
    }
}
