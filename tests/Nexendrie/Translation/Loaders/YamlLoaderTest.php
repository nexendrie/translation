<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\TestSuite;
use Nexendrie\Translation\Resolvers\ManualLocaleResolver;

#[TestSuite("YamlLoader")]
final class YamlLoaderTest extends FileLoaderTestAbstract
{
    #[BeforeTest]
    public function setUp(): void
    {
        parent::setUp();
        $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
        $this->loader = new YamlLoader(new ManualLocaleResolver(), $folders, $this->eventDispatcher);
    }
}
