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
class JsonLoader extends FileLoader {
  /** @var string */
  protected $extension = "json";
  
  protected function parseFile(string $filename): array {
    return Json::decode(file_get_contents($filename), Json::FORCE_ARRAY);
  }
}
?>