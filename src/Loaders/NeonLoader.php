<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Nette\Neon\Neon;

/**
 * Translations loader
 * Loads texts from neon files
 *
 * @author Jakub Konečný
 */
class NeonLoader extends FileLoader {
  /** @var string */
  protected $extension = "neon";
  
  /**
   * Parse individual file
   *
   * @param string $filename
   * @return array
   */
  protected function parseFile($filename) {
    return Neon::decode(file_get_contents($filename));
  }
}
?>