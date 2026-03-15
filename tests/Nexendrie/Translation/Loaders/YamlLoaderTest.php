<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Nexendrie\Translation\Resolvers\ManualLocaleResolver;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class YamlLoaderTest extends FileLoaderTestAbstract
{
    protected function setUp(): void
    {
        parent::setUp();
        $folders = [__DIR__ . "/../../../lang", __DIR__ . "/../../../lang2"];
        $this->loader = new YamlLoader(new ManualLocaleResolver(), $folders, $this->eventDispatcher);
    }
}

$test = new YamlLoaderTest();
$test->run();
