<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

/**
 * Loader
 *
 * @author Jakub Konečný
 * @testCase
 */
final class Loader extends FileLoader {
  protected string $extension = "php";

  protected function parseFile(string $filename): array {
    return require $filename;
  }
}
?>