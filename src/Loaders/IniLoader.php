<?php
namespace Nexendrie\Translation\Loaders;

/**
 * IniLoader
 *
 * @author Jakub Konečný
 */
class IniLoader extends FileLoader {
  /** @var string */
  protected $extension = "ini";
  
  /**
   * Parse individual file
   *
   * @param string $content
   * @return array
   */
  protected function parseFile($content) {
    return parse_ini_string($content, true);
  }
}
?>