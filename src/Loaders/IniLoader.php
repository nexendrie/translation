<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

/**
 * IniLoader
 * Loads texts from ini files
 *
 * @author Jakub Konečný
 */
class IniLoader extends FileLoader {
  /** @var string */
  protected $extension = "ini";
  
  /**
   * Parse individual file
   *
   * @param string $filename
   * @return array
   */
  protected function parseFile($filename) {
    return parse_ini_file($filename, true);
  }
}
?>