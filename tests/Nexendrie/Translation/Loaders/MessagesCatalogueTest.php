<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\TestSuite;
use Nexendrie\Translation\Resolvers\ManualLocaleResolver;
use Nexendrie\Translation\CatalogueCompiler;

#[TestSuite("MessagesCatalogue")]
final class MessagesCatalogueTest extends FileLoaderTestAbstract
{
    #[BeforeTest]
    public function setUp(): void
    {
        parent::setUp();
        $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
        $folder = __DIR__ . "/../../../temp/catalogues";
        $loader = new NeonLoader(new ManualLocaleResolver(), $folders);
        $compiler = new CatalogueCompiler($loader, $folder, ["en", "cs"]);
        $compiler->compile();
        $this->loader = new MessagesCatalogue(new ManualLocaleResolver(), [$folder], $this->eventDispatcher);
    }

    public function testGetFolders(): void
    {
        $folders = $this->loader->folders;
        $this->assertType("array", $folders);
        $this->assertCount(1, $folders);
        $this->assertSame(__DIR__ . "/../../../temp/catalogues", $folders[0]);
    }

    public function testCatalogueWithoutResources(): void
    {
        $folder = __DIR__ . "/../../../catalogue";
        $loader = new MessagesCatalogue(new ManualLocaleResolver(), [$folder]);
        $loader->getTexts();
        $this->assertCount(3, $loader->resources);
    }
}
