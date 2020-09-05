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
final class NeonLoader extends FileLoader {
  protected string $extension = "neon";

  /**
   * @throws \RuntimeException
   */
  protected function parseFile(string $filename): array {
    $content = file_get_contents($filename);
    if($content === false) {
      throw new \RuntimeException("File $filename does not exist or cannot be read.");
    }
    return Neon::decode($content);
  }
}
?>