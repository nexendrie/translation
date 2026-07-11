<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nexendrie\Translation\Resolvers\ManualLocaleResolver;

#[TestSuite("PhpLoader")]
#[Group("loaders")]
final class PhpLoaderTest extends FileLoaderTestAbstract
{
    #[BeforeTest]
    public function setUp(): void
    {
        parent::setUp();
        $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
        $this->loader = new PhpLoader(new ManualLocaleResolver(), $folders, $this->eventDispatcher);
    }
}
