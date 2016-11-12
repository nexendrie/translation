<?php
namespace Nexendrie\Translation\Loaders;

use Symfony\Component\Yaml\Yaml;

/**
 * YamlLoader
 * Loads texts from yaml files
 *
 * @author Jakub Konečný
 */
class YamlLoader extends FileLoader {
  /** @var string */
  protected $extension = "yaml";
  
  /**
   * Parse individual file
   *
   * @param string $filename
   * @return array
   */
  protected function parseFile($filename) {
    return Yaml::parse(file_get_contents($filename));
  }
}
?>