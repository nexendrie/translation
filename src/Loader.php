<?php
namespace Nexendrie\Translation;

use Nette\Neon\Neon,
    Nette\Utils\Finder,
    Nexendrie\Translation\Resolvers\ILocaleResolver,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver;

/**
 * Translations loader
 *
 * @author Jakub Konečný
 * @property string $lang
 * @property array $texts
 * @property string $folder
 * @property-read array $resources
 */
class Loader {
  use \Nette\SmartObject;
  
  /** @var string */
  protected $loadedLang = NULL;
  /** @var array */
  protected $texts = NULL;
  /** @var string */
  protected $folder = NULL;
  /** @var ILocaleResolver|NULL */
  protected $resolver = NULL;
  /** @var array */
  protected $resources = [];
  
  /**
   * @param string $lang
   * @param string $folder
   */
  function __construct($lang = "en", $folder = NULL, ILocaleResolver $resolver = NULL) {
    $this->lang = $lang;
    if(is_string($folder)) {
      $this->setFolder($folder);
    }
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
    return $this->resolver->resolve();
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
  function getFolder() {
    return $this->folder;
  }
  
  /**
   * @param string $folder
   * @throws \Exception
   */
  function setFolder($folder) {
    if(!is_dir($folder)) {
      throw new \Exception("Folder $folder does not exist.");
    }
    $this->folder = $folder;
  }
  
  /**
   * @return array
   */
  function getResources() {
    return $this->resources;
  }
  
  /**
   * @param string $name
   * @return array
   */
  protected function loadDomain($name) {
    $default = Neon::decode(file_get_contents("$this->folder/$name.en.neon"));
    $this->resources[$name][] = "$this->folder/$name.en.neon";
    $lang = [];
    $filename = "$this->folder/$name.{$this->lang}.neon";
    if($this->lang != "en" AND is_file($filename)) {
      $lang = Neon::decode(file_get_contents($filename));
      $this->resources[$name][] = $filename;
    }
    return array_merge($default, $lang);
  }
  
  /**
   * @return void
   * @throws \Exception
   */
  protected function loadTexts() {
    if($this->lang === $this->loadedLang) return;
    if(is_null($this->folder)) throw new \Exception("Folder for translations was not set.");
    $this->resources = $texts = [];
    $files = Finder::findFiles("*.en.neon")->from($this->folder);
    /** @var \SplFileInfo $file */
    foreach($files as $file) {
      $domain = $file->getBasename(".en.neon");
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
}
?>