<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

/**
 * PhpLoader
 * Loads texts from php files
 *
 * @author Jakub Konečný
 */
class PhpLoader extends FileLoader {
  /** @var string */
  protected $extension = "php";
  
  /**
   * Parse individual file
   *
   * @param string $filename
   * @return array
   */
  protected function parseFile($filename) {
    return require $filename;
  }
}
?>