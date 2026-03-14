<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Devium\Toml\Toml;
use Devium\Toml\TomlError;

/**
 * TomlLoader
 * Loads texts from toml files
 *
 * @author Jakub Konečný
 */
final class TomlLoader extends FileLoader
{
    protected string $extension = "toml";

    /**
     * @throws \RuntimeException
     * @throws TomlError
     */
    protected function parseFile(string $filename): array
    {
        $content = @file_get_contents($filename);
        if ($content === false) {
            throw new \RuntimeException("File $filename does not exist or cannot be read.");
        }
        return Toml::decode($content, true); // @phpstan-ignore return.type
    }
}
