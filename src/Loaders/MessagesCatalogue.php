<?php
declare(strict_types=1);

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
  protected function loadDomain(string $name) {
    
  }
  
  /**
   * Load all texts
   *
   * @return void
   * @throws FolderNotSetException
   */
  protected function loadTexts(): void {
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
        $domains = array_keys($texts);
        foreach($domains as $domain) {
          $this->addResource($file->getPathname(), $domain);
        }
      }
    }
    $this->texts = $texts;
    $this->loadedLang = $this->lang;
  }
  
  /**
   * @return string
   */
  protected function getLanguageFilenameMask(): string {
    return "catalogue.*.$this->extension";
  }
}
?>