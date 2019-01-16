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
  protected $extension = "neon";
  
  protected function parseFile(string $filename): array {
    return Neon::decode(file_get_contents($filename));
  }
}
?>