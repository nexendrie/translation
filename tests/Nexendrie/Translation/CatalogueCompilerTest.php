<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

require __DIR__ . "/../../bootstrap.php";

use Konecnyjakub\EventDispatcher\AutoListenerProvider;
use Konecnyjakub\EventDispatcher\EventDispatcher;
use Nette\Utils\FileSystem;
use Nexendrie\Translation\Events\CatalogueCompiled;
use Nexendrie\Translation\Loaders\NeonLoader;
use Nexendrie\Translation\Resolvers\ManualLocaleResolver;
use Tester\Assert;

/**
 * CatalogueCompilerTest
 *
 * @author Jakub Konečný
 * @testCase
 */
final class CatalogueCompilerTest extends \Tester\TestCase
{
    public function testEvent(): void
    {
        $folder = __DIR__ . "/../../_temp/catalogues";
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
        Assert::same(["en", "cs",], $compiledLanguages);
    }
}

$test = new CatalogueCompilerTest();
$test->run();
