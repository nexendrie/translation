<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Nette\Utils\Finder;
use Nexendrie\Translation\FolderNotSetException;

/**
 * MessagesCatalogue
 * Loads texts from compiled (PHP) messages catalogue
 *
 * @author Jakub Konečný
 */
final class MessagesCatalogue extends FileLoader {
  protected string $extension = "php";

  protected function parseFile(string $filename): array {
    return require $filename;
  }

  protected function loadDomain(string $name): array {
    return [];
  }
  
  protected function loadTexts(): void {
    if(count($this->folders) === 0) {
      throw new FolderNotSetException("Folder for translations was not set.");
    }
    $this->resources = $texts = [];
    $filename = str_replace(static::LANGUAGE_MASK, $this->lang, $this->getLanguageFilenameMask());
    $files = Finder::findFiles($filename)
      ->from(...$this->folders);
    /** @var \SplFileInfo $file */
    foreach($files as $file) {
      $texts = array_merge($texts, $this->parseFile($file->getPathname()));
      if(isset($texts["__resources"])) {
        $this->resources = array_merge($this->resources, $texts["__resources"]);
        unset($texts["__resources"]);
      } else {
        /** @var string[] $domains */
        $domains = array_keys($texts);
        foreach($domains as $domain) {
          $this->addResource($file->getPathname(), $domain);
        }
      }
    }
    $this->texts = $texts;
    $this->loadedLang = $this->lang;
  }

  protected function getLanguageFilenameMask(): string {
    return "catalogue." . static::LANGUAGE_MASK . "." . $this->extension;
  }
}
?>