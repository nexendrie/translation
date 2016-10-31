<?php
namespace Nexendrie\Translation;

use Nette\Utils\Arrays,
    Nette\Localization\ITranslator;

/**
 * Translator
 *
 * @author Jakub Konečný
 * @property string $lang
 * @property string[] $folders
 * @property-read string[] $untranslated
 */
class Translator implements ITranslator {
  use \Nette\SmartObject;
  
  /** @var Loader */
  protected $loader;
  /** @var string[] */
  protected $untranslated = [];
  
  function __construct(Loader $loader = NULL) {
    $this->loader = (is_null($loader)) ? new Loader : $loader;
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
   * @return string[]
   */
  function getFolders() {
    return $this->loader->folders;
  }
  
  /**
   * @param string[] $folders
   */
  function setFolders($folders) {
    $this->loader->folders = $folders;
  }
  
  /**
   * @return string[]
   */
  function getUntranslated() {
    return $this->untranslated;
  }
  
  /**
   * Translate the string
   *
   * @param string $message
   * @param int $count
   * @param array $params
   * @return string
   */
  function translate($message, $count = 0, $params = []) {
    $dotPos = strpos($message, ".");
    if($dotPos === false) {
      $domain = "messages";
    } else {
      $domain = substr($message, 0, $dotPos);
      $message = substr($message, $dotPos + 1);
    }
    $texts = Arrays::get($this->loader->texts, $domain, []);
    $text = Arrays::get($texts, $message, "");
    foreach($params as $key => $value) {
      $text = str_replace("%$key%", $value, $text);
    }
    if($text === "") {
      $this->untranslated[] = "$domain.$message";
    }
    return $text;
  }
}
?>