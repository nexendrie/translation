<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

/**
 * FileLoader
 *
 * @author Jakub Konečný
 */
interface FileLoader extends Loader {
  /**
   * @return string[]
   */
  public function getFolders(): array;

  /**
   * @param string[] $folders
   */
  public function setFolders(array $folders): void;
}
?>