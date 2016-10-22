<?php
namespace Nexendrie\Translation;

use Nette\Neon\Neon;

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
   * @return void
   * @throws \Exception
   */
  protected function loadTexts() {
    if(!is_null($this->texts)) return;
    if(is_null($this->folder)) throw new \Exception("Folder for translations was not set.");
    if(!is_dir($this->folder)) throw new \Exception("Folder $this->folder does not exist.");
    $default = Neon::decode(file_get_contents("$this->folder/en.neon"));
    $lang = [];
    if($this->lang != "en" AND is_file("$this->folder/{$this->lang}.neon")) {
      $lang = Neon::decode(file_get_contents("$this->folder/{$this->lang}.neon"));
    }
    $this->texts = array_merge($default, $lang);
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