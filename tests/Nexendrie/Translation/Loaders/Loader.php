<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

/**
 * Loader
 *
 * @author Jakub Konečný
 */
final class Loader extends FileLoader {
  protected $extension = "php";

  protected function parseFile(string $filename): array {
    return require $filename;
  }
}
?>