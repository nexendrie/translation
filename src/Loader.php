<?php
namespace Nexendrie\Translation;

use Nette\Neon\Neon,
    Nette\Utils\Finder;

/**
 * Translations loader
 *
 * @author Jakub Konečný
 * @property string $lang
 * @property array $texts
 * @property string $folder
 */
class Loader {
  use \Nette\SmartObject;
  
  /** @var string */
  protected $lang;
  /** @var array */
  protected $texts = NULL;
  /** @var string */
  protected $folder = NULL;
  
  /**
   * Loader constructor.
   * @param string $lang
   * @param string $folder
   */
  function __construct($lang = "en", $folder = NULL) {
    $this->lang = $lang;
    $this->folder = $folder;
  }
  
  
  /**
   * @return string
   */
  function getLang() {
    return $this->lang;
  }
  
  /**
   * @param string $lang
   */
  function setLang($lang) {
    if($lang !== $this->lang) {
      $this->lang = $lang;
      $this->texts = NULL;
      $this->loadTexts();
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
   */
  function setFolder($folder) {
    $this->folder = $folder;
  }
  
  /**
   * @param string $name
   * @return array
   */
  protected function loadDomain($name) {
    $default = Neon::decode(file_get_contents("$this->folder/$name.en.neon"));
    $lang = [];
    if($this->lang != "en" AND is_file("$this->folder/$name.{$this->lang}.neon")) {
      $lang = Neon::decode(file_get_contents("$this->folder/$name.{$this->lang}.neon"));
    }
    return array_merge($default, $lang);
  }
  
  /**
   * @return void
   * @throws \Exception
   */
  protected function loadTexts() {
    if(!is_null($this->texts)) return;
    if(is_null($this->folder)) throw new \Exception("Folder for translations was not set.");
    if(!is_dir($this->folder)) throw new \Exception("Folder $this->folder does not exist.");
    $texts = [];
    $files = Finder::findFiles("*.en.neon")->from($this->folder);
    /** @var \SplFileInfo $file */
    foreach($files as $file) {
      $domain = $file->getBasename(".en.neon");
      $texts[$domain] = $this->loadDomain($domain);
    }
    $this->texts = $texts;
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