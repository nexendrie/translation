<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\TestSuite;
use Nexendrie\Translation\Resolvers\ManualLocaleResolver;
use ReflectionMethod;
use RuntimeException;

#[TestSuite("TomlLoader")]
final class TomlLoaderTest extends FileLoaderTestAbstract
{
    #[BeforeTest]
    public function setUp(): void
    {
        parent::setUp();
        $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
        $this->loader = new TomlLoader(new ManualLocaleResolver(), $folders, $this->eventDispatcher);
    }

    public function testParseNonExistingFile(): void
    {
        $this->assertThrowsException(function () {
            $rm = new ReflectionMethod($this->loader, "parseFile");
            $rm->invoke($this->loader, "/non-existing");
        }, RuntimeException::class);
    }
}
