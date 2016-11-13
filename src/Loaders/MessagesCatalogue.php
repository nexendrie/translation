<?php
namespace Nexendrie\Translation\Loaders;

use Nette\Utils\Finder,
    Nexendrie\Translation\FolderNotSetException;

/**
 * MessagesCatalogue
 *
 * @author Jakub Konečný
 */
class MessagesCatalogue extends PhpLoader {
  protected function loadDomain($name) {
    
  }
  
  /**
   * Load all texts
   *
   * @return void
   * @throws FolderNotSetException
   */
  protected function loadTexts() {
    if(!count($this->folders)) {
      throw new FolderNotSetException("Folder for translations was not set.");
    }
    $this->resources = $texts = [];
    $extension = $this->extension;
    $lang = $this->lang;
    $files = Finder::findFiles("catalogue.$lang.$extension")->from($this->folders);
    /** @var \SplFileInfo $file */
    foreach($files as $file) {
      $texts = array_merge($texts, $this->parseFile($file->getPathname()));
      $this->resources["*"][] = $file->getPathname();
    }
    $this->texts = $texts;
    $this->loadedLang = $this->lang;
  }
}
?>