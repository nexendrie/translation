<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class ChainLocaleResolverTest extends \Tester\TestCase
{
    protected ChainLocaleResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new ChainLocaleResolver();
    }

    public function testResolve(): void
    {
        Assert::null($this->resolver->resolve());
        $this->resolver[] = new ManualLocaleResolver();
        Assert::null($this->resolver->resolve());
        $resolver = new ManualLocaleResolver();
        $this->resolver[] = $resolver;
        $resolver->lang = "en";
        Assert::same("en", $this->resolver->resolve());
    }
}

$test = new ChainLocaleResolverTest();
$test->run();
