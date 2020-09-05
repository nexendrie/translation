<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Symfony\Component\Yaml\Yaml;

/**
 * YamlLoader
 * Loads texts from yaml files
 *
 * @author Jakub Konečný
 */
final class YamlLoader extends FileLoader {
  protected string $extension = "yaml";

  /**
   * @throws \Symfony\Component\Yaml\Exception\ParseException
   */
  protected function parseFile(string $filename): array {
    return Yaml::parseFile($filename);
  }
}
?>