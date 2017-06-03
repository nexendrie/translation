<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Nette\Utils\Finder,
    Nexendrie\Translation\Resolvers\ILocaleResolver,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\InvalidFolderException,
    Nexendrie\Translation\FolderNotSetException;

/**
 * Generic file translations loader
 * Loads texts from {$this->extension} files
 * You need to define method parseFile() which processes individual file
 *
 * @author Jakub Konečný
 * @property string $defaultLang
 * @property string $lang
 * @property array $texts
 * @property string[] $folders
 * @property-read array $resources
 */
abstract class FileLoader implements ILoader {
  use \Nette\SmartObject;
  
  /** @var string */
  protected $defaultLang = "en";
  /** @var string|NULL */
  protected $loadedLang = NULL;
  /** @var array */
  protected $texts = [];
  /** @var string[] */
  protected $folders = [];
  /** @var ILocaleResolver */
  protected $resolver;
  /** @var array */
  protected $resources = [];
  /** @var string */
  protected $extension;
  
  /**
   * @param ILocaleResolver $resolver
   * @param string[] $folders
   */
  function __construct(ILocaleResolver $resolver = NULL, array $folders = []) {
    $this->setFolders($folders);
    $this->resolver = $resolver ?? new ManualLocaleResolver;
  }
  
  /**
   * @return string
   */
  function getLang(): string {
    $lang = $this->resolver->resolve();
    return $lang ?? $this->defaultLang;
  }
  
  /**
   * @param string $lang
   */
  function setLang(string $lang) {
    if($this->resolver instanceof ManualLocaleResolver) {
      $this->resolver->lang = $lang;
    }
  }
  
  /**
   * @return string
   */
  function getDefaultLang(): string {
    return $this->defaultLang;
  }
  
  /**
   * @param string $defaultLang
   */
  function setDefaultLang(string $defaultLang) {
    $this->defaultLang = $defaultLang;
  }
  
  /**
   * @return string[]
   */
  function getFolders(): array {
    return $this->folders;
  }
  
  /**
   * @param string[] $folders
   * @throws InvalidFolderException
   */
  function setFolders(array $folders) {
    foreach($folders as $folder) {
      if(!is_dir($folder)) {
        throw new InvalidFolderException("Folder $folder does not exist.");
      }
      $this->folders[] = $folder;
    }
  }
  
  /**
   * @param string $filename
   * @param string $domain
   * @return void
   */
  protected function addResource(string $filename, string $domain): void {
    if(!isset($this->resources[$domain]) OR !in_array($filename, $this->resources[$domain])) {
      $this->resources[$domain][] = $filename;
    }
  }
  
  /**
   * @return array
   */
  function getResources(): array {
    return $this->resources;
  }
  
  /**
   * Parse individual file
   * 
   * @param string $filename
   * @return array
   */
  abstract protected function parseFile(string $filename): array;
  
  /**
   * Load texts from one text domain
   *
   * @param string $name
   * @return array
   */
  protected function loadDomain(string $name) {
    $return = [];
    $defaultLang = $this->defaultLang;
    $extension = $this->extension;
    $defaultFilename = "$name.$defaultLang.$extension";
    $files = Finder::findFiles($defaultFilename)->from($this->folders);
    /** @var \SplFileInfo $file */
    foreach($files as $file) {
      $default = $this->parseFile($file->getPathname());
      $this->addResource($file->getPathname(), $name);
      $lang = [];
      $filename = "$name.$this->lang.$extension";
      $filename = str_replace($defaultFilename, $filename, $file->getPathname());
      if($this->lang != $defaultLang AND is_file($filename)) {
        $lang = $this->parseFile($filename);
        $this->addResource($filename, $name);
      }
      $return = array_merge($return, $default, $lang);
    }
    return $return;
  }
  
  /**
   * Load all texts
   *
   * @return void
   * @throws FolderNotSetException
   */
  protected function loadTexts(): void {
    if($this->lang === $this->loadedLang) {
      return;
    }
    if(!count($this->folders)) {
      throw new FolderNotSetException("Folder for translations was not set.");
    }
    $default = $this->defaultLang;
    $this->resources = $texts = [];
    $extension = $this->extension;
    $files = Finder::findFiles("*.$default.$extension")->from($this->folders);
    /** @var \SplFileInfo $file */
    foreach($files as $file) {
      $domain = $file->getBasename(".$default.$extension");
      $texts[$domain] = $this->loadDomain($domain);
    }
    $this->texts = $texts;
    $this->loadedLang = $this->lang;
  }
  
  /**
   * @return array
   */
  function getTexts(): array {
    $this->loadTexts();
    return $this->texts;
  }
  
  /**
   * @return string
   */
  function getResolverName(): string {
    $class = get_class($this->resolver);
    $pos = strrpos($class, '\\');
    return substr($class, $pos + 1);
  }
  
  /**
   * @return string[]
   * @throws FolderNotSetException
   */
  function getAvailableLanguages(): array {
    if(!count($this->folders)) {
      throw new FolderNotSetException("Folder for translations was not set.");
    }
    $languages = [];
    $extension = $this->extension;
    $files = Finder::findFiles("*.$extension")->from($this->folders);
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