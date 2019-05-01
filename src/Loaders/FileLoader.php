<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Nexendrie\Translation\ILocaleResolver;
use Nexendrie\Translation\Resolvers\ManualLocaleResolver;
use Nexendrie\Translation\ISettableLocaleResolver;
use Nexendrie\Translation\InvalidFolderException;
use Nexendrie\Translation\FolderNotSetException;
use Nette\Utils\Arrays;
use Nexendrie\Translation\ILoader;

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
 * @method void onLanguageChange(FileLoader $loader, string $oldLang, string $newLang)
 * @method void onFoldersChange(FileLoader $loader, string[] $folders)
 * @method void onLoad(FileLoader $loader, string $lang)
 */
abstract class FileLoader implements ILoader {
  use \Nette\SmartObject;
  
  /** @var string */
  protected $defaultLang = "en";
  /** @var string|null */
  protected $loadedLang = null;
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
  /** @var callable[] */
  public $onLanguageChange = [];
  /** @var callable[] */
  public $onFoldersChange = [];
  /** @var callable[] */
  public $onLoad = [];
  
  /**
   * @param string[] $folders
   */
  public function __construct(ILocaleResolver $resolver = null, array $folders = []) {
    $this->setFolders($folders);
    $this->resolver = $resolver ?? new ManualLocaleResolver();
  }
  
  public function getLang(): string {
    return $this->resolver->resolve() ?? $this->defaultLang;
  }
  
  public function setLang(string $lang): void {
    if(is_a($this->resolver, ISettableLocaleResolver::class)) {
      $oldLang = $this->lang;
      $this->resolver->setLang($lang);
      $this->onLanguageChange($this, $oldLang, $lang);
    }
  }
  
  public function getDefaultLang(): string {
    return $this->defaultLang;
  }
  
  public function setDefaultLang(string $defaultLang): void {
    $this->defaultLang = $defaultLang;
  }
  
  /**
   * @return string[]
   */
  public function getFolders(): array {
    return $this->folders;
  }
  
  /**
   * @param string[] $folders
   * @throws InvalidFolderException
   */
  public function setFolders(array $folders): void {
    foreach($folders as $folder) {
      if(!is_dir($folder)) {
        throw new InvalidFolderException("Folder $folder does not exist.");
      }
      $this->folders[] = $folder;
    }
    $this->onFoldersChange($this, $folders);
  }
  
  protected function addResource(string $filename, string $domain): void {
    if(!isset($this->resources[$domain]) OR !in_array($filename, $this->resources[$domain], true)) {
      $this->resources[$domain][] = $filename;
    }
  }
  
  public function getResources(): array {
    return $this->resources;
  }
  
  /**
   * Parse individual file
   */
  abstract protected function parseFile(string $filename): array;
  
  /**
   * Load texts from one text domain
   */
  protected function loadDomain(string $name): array {
    $return = [];
    $defaultLang = $this->defaultLang;
    $extension = $this->extension;
    $defaultFilename = "$name.$defaultLang.$extension";
    $files = Finder::findFiles($defaultFilename)
      ->from($this->folders);
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
      $return = Arrays::mergeTree($return, Arrays::mergeTree($lang, $default));
    }
    return $return;
  }
  
  /**
   * Load all texts
   *
   * @throws FolderNotSetException
   */
  protected function loadTexts(): void {
    if($this->lang === $this->loadedLang) {
      return;
    }
    if(count($this->folders) === 0) {
      throw new FolderNotSetException("Folder for translations was not set.");
    }
    $default = $this->defaultLang;
    $this->resources = $texts = [];
    $extension = $this->extension;
    $files = Finder::findFiles("*.$default.$extension")
      ->from($this->folders);
    /** @var \SplFileInfo $file */
    foreach($files as $file) {
      $domain = $file->getBasename(".$default.$extension");
      $texts[$domain] = $this->loadDomain($domain);
    }
    $this->texts = $texts;
    $this->loadedLang = $this->lang;
    $this->onLoad($this, $this->lang);
  }
  
  public function getTexts(): array {
    $this->loadTexts();
    return $this->texts;
  }
  
  public function getResolverName(): string {
    $class = get_class($this->resolver);
    return (string) Strings::after($class, '\\', -1);
  }
  
  protected function getLanguageFilenameMask(): string {
    return "*.$this->extension";
  }
  
  /**
   * @return string[]
   * @throws FolderNotSetException
   */
  public function getAvailableLanguages(): array {
    if(count($this->folders) === 0) {
      throw new FolderNotSetException("Folder for translations was not set.");
    }
    $languages = [];
    $extension = $this->extension;
    $files = Finder::findFiles($this->getLanguageFilenameMask())
      ->from($this->folders);
    /** @var \SplFileInfo $file */
    foreach($files as $file) {
      $filename = $file->getBasename(".$extension");
      $lang = (string) Strings::after($filename, ".");
      if(!in_array($lang, $languages, true)) {
        $languages[] = $lang;
      }
    }
    return $languages;
  }
}
?>