<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Nexendrie\Translation\Resolvers\ManualLocaleResolver;
use ReflectionMethod;
use RuntimeException;
use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class IniLoaderTest extends FileLoaderTestAbstract
{
    protected function setUp(): void
    {
        parent::setUp();
        $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
        $this->loader = new IniLoader(new ManualLocaleResolver(), $folders, $this->eventDispatcher);
    }

    public function testParseNonExistingFile(): void
    {
        Assert::exception(function () {
            $rm = new ReflectionMethod($this->loader, "parseFile");
            $rm->invoke($this->loader, "/non-existing");
        }, RuntimeException::class);
    }
}

$test = new IniLoaderTest();
$test->run();
