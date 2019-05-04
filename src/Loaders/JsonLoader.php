<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Nette\Utils\Json;

/**
 * JsonLoader
 * Loads texts from json files
 *
 * @author Jakub Konečný
 */
final class JsonLoader extends FileLoader {
  protected $extension = "json";
  
  /**
   * @throws \RuntimeException
   * @throws \Nette\Utils\JsonException
   */
  protected function parseFile(string $filename): array {
    $content = file_get_contents($filename);
    if($content === false) {
      throw new \RuntimeException("File $filename does not exist or cannot be read.");
    }
    return Json::decode($content, Json::FORCE_ARRAY);
  }
}
?>