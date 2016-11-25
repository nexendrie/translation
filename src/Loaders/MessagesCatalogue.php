<?php
namespace Nexendrie\Translation\Loaders;

use Nette\Utils\Finder,
    Nexendrie\Translation\FolderNotSetException;

/**
 * MessagesCatalogue
 * Loads texts from compiled (PHP) messages catalogue
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
      if(isset($texts["__resources"])) {
        $this->resources = array_merge($this->resources, $texts["__resources"]);
        unset($texts["__resources"]);
      } else {
        $this->resources["*"][] = $file->getPathname();
      }
    }
    $this->texts = $texts;
    $this->loadedLang = $this->lang;
  }
  
  
  /**
   * @return string[]
   * @throws FolderNotSetException
   */
  function getAvailableLanguages() {
    if(!count($this->folders)) {
      throw new FolderNotSetException("Folder for translations was not set.");
    }
    $languages = [];
    $extension = $this->extension;
    $files = Finder::findFiles("catalogue.*.$extension")->from($this->folders);
    /** @var \SplFileInfo $file */
    foreach($files as $file) {
      $filename = $file->getBasename(".$extension");
      $lang = substr($filename, strpos($filename, ".") + 1);
      if(!in_array($lang, $languages)) {
        $languages[] = $lang;
      }
    }
    return $languages;
  }
}
?>