<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

/**
 * IniLoader
 * Loads texts from ini files
 *
 * @author Jakub Konečný
 */
final class IniLoader extends FileLoader
{
    protected string $extension = "ini";

    /**
     * @throws \RuntimeException
     */
    protected function parseFile(string $filename): array
    {
        $result = parse_ini_file($filename, true);
        if ($result === false) {
            throw new \RuntimeException("File $filename does not exist or cannot be read.");
        }
        return $result;
    }
}
