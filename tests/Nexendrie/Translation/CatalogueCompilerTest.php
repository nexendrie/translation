<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Konecnyjakub\EventDispatcher\AutoListenerProvider;
use Konecnyjakub\EventDispatcher\EventDispatcher;
use MyTester\Attributes\TestSuite;
use Nette\Utils\FileSystem;
use Nexendrie\Translation\Events\CatalogueCompiled;
use Nexendrie\Translation\Loaders\NeonLoader;
use Nexendrie\Translation\Resolvers\ManualLocaleResolver;

#[TestSuite("CatalogueCompiler")]
final class CatalogueCompilerTest extends \MyTester\TestCase
{
    public function testEvent(): void
    {
        $folder = __DIR__ . "/../../temp/catalogues";
        FileSystem::delete($folder);
        FileSystem::createDir($folder);
        $folders = [__DIR__ . "/../../lang", __DIR__ . "/../../lang2",];
        $loader = new NeonLoader(new ManualLocaleResolver(), $folders);
        $provider = new AutoListenerProvider();
        $compiledLanguages = [];
        $provider->addListener(
            static function (CatalogueCompiled $event) use (&$compiledLanguages): void {
                $compiledLanguages[] = $event->language;
            }
        );
        $dispatcher = new EventDispatcher($provider);
        $catalogueCompiler = new CatalogueCompiler($loader, $folder, ["en", "cs",], $dispatcher);
        $catalogueCompiler->compile();
        $this->assertSame(["en", "cs",], $compiledLanguages);
    }
}
