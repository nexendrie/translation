<?php
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
   * @param string $content
   * @return array
   */
  protected function parseFile($content) {
    return Neon::decode($content);
  }
}
?>