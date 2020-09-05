<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

/**
 * PhpLoader
 * Loads texts from php files
 *
 * @author Jakub Konečný
 */
final class PhpLoader extends FileLoader {
  protected string $extension = "php";
  
  protected function parseFile(string $filename): array {
    return require $filename;
  }
}
?>