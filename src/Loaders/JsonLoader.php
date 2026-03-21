<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

/**
 * JsonLoader
 * Loads texts from json files
 *
 * @author Jakub Konečný
 */
final class JsonLoader extends FileLoader
{
    protected string $extension = "json";

    /**
     * @throws \RuntimeException
     * @throws \JsonException
     */
    protected function parseFile(string $filename): array
    {
        $content = @file_get_contents($filename); // phpcs:ignore Generic.PHP.NoSilencedErrors
        if ($content === false) {
            throw new \RuntimeException("File $filename does not exist or cannot be read.");
        }
        return json_decode($content, flags: JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING | JSON_OBJECT_AS_ARRAY);
    }
}
