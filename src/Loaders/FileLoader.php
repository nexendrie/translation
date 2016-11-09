<?php
namespace Nexendrie\Translation\Loaders;

use Nette\Utils\Finder,
    Nexendrie\Translation\Resolvers\ILocaleResolver,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\InvalidFolderException,
    Nexendrie\Translation\FolderNotSetException;

/**
 * Translations loader
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
  /** @var string */
  protected $loadedLang = NULL;
  /** @var array */
  protected $texts = NULL;
  /** @var string[] */
  protected $folders = [];
  /** @var ILocaleResolver|NULL */
  protected $resolver = NULL;
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
    if($resolver) {
      $this->resolver = $resolver;
    } else {
      $this->resolver = new ManualLocaleResolver;
    }
  }
  
  /**
   * @return string
   */
  function getLang() {
    $lang = $this->resolver->resolve();
    if(is_null($lang)) {
      $lang = $this->defaultLang;
    }
    return $lang;
  }
  
  /**
   * @param string $lang
   */
  function setLang($lang) {
    if($this->resolver instanceof ManualLocaleResolver) {
      $this->resolver->lang = $lang;
    }
  }
  
  /**
   * @return string
   */
  function getDefaultLang() {
    return $this->defaultLang;
  }
  
  /**
   * @param string $defaultLang
   */
  function setDefaultLang($defaultLang) {
    $this->defaultLang = $defaultLang;
  }
  
  /**
   * @return string[]
   */
  function getFolders() {
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
   * @return array
   */
  function getResources() {
    return $this->resources;
  }
  
  /**
   * Parse individual file
   * 
   * @param string $content
   * @return array
   */
  abstract protected function parseFile($content);
  
  /**
   * Load texts from one text domain
   *
   * @param string $name
   * @return array
   */
  protected function loadDomain($name) {
    $return = [];
    $extension = $this->extension;
    foreach($this->folders as $folder) {
      $defaultFilename = "$folder/$name.$this->defaultLang.$extension";
      if(!is_file($defaultFilename)) continue;
      $default = $this->parseFile(file_get_contents($defaultFilename));
      $this->resources[$name][] = $defaultFilename;
      $lang = [];
      $filename = "$folder/$name.{$this->lang}.$extension";
      if($this->lang != $this->defaultLang AND is_file($filename)) {
        $lang = $this->parseFile(file_get_contents($filename));
        $this->resources[$name][] = $filename;
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
  protected function loadTexts() {
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
  function getTexts() {
    $this->loadTexts();
    return $this->texts;
  }
  
  /**
   * @return string
   */
  function getResolverName() {
    $class = get_class($this->resolver);
    $pos = strrpos($class, '\\');
    return substr($class, $pos + 1);
  }
}
?>