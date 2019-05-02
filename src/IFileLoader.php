<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

/**
 * IFileLoader
 *
 * @author Jakub Konečný
 */
interface IFileLoader extends ILoader {
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