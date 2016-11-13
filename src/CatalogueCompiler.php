<?php
namespace Nexendrie\Translation;

use Nexendrie\Translation\Loaders\ILoader,
    Nette\PhpGenerator\Helpers;

/**
 * CatalogueCompiler
 *
 * @author Jakub Konečný
 */
class CatalogueCompiler {
  use \Nette\SmartObject;
  
  /** @var ILoader */
  protected $loader;
  /** @var string[] */
  protected $languages = [];
  /** @var string */
  protected $folder = "";
  
  /**
   * @param ILoader $loader
   * @param string[] $languages
   * @param string $folder
   */
  function __construct(ILoader $loader, array $languages, $folder) {
    $this->loader = $loader;
    $this->languages = $languages;
    $this->folder = $folder;
  }
  
  /**
   * Compile catalogues
   *
   * @return void
   */
  function compile() {
    foreach($this->languages as $language) {
      $this->loader->setLang($language);
      $texts = $this->loader->getTexts();
      $content = "<?php
return " . Helpers::dump($texts) . ";
?>";
      $filename = $this->folder . "/catalogue.$language.php";
      file_put_contents($filename, $content);
    }
  }
}
?>