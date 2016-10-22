<?php
namespace Nexendrie\Translation;

use Nette\Utils\Arrays;

/**
 * Translator
 *
 * @author Jakub Konečný
 * @property string $lang
 * @property string $folder
 */
class Translator implements \Nette\Localization\ITranslator {
  use \Nette\SmartObject;
  
  /** @var Loader */
  protected $loader;
  
  function __construct() {
    $this->loader = new Loader;
  }
  
  /**
   * @return string
   */
  function getLang() {
    return $this->loader->lang;
  }
  
  /**
   * @param string $lang
   */
  function setLang($lang) {
    $this->loader->lang = $lang;
  }
  
  /**
   * @return string
   */
  function getFolder() {
    return $this->loader->folder;
  }
  
  /**
   * @param string $folder
   */
  function setFolder($folder) {
    $this->loader->folder = $folder;
  }
  
  /**
   * @param string $message
   * @param int $count
   * @return string
   */
  function translate($message, $count = 0) {
    return Arrays::get($this->loader->texts, $message, "");
  }
}
?>