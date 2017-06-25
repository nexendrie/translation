<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Nexendrie\Translation\Loaders\ILoader,
    Nette\PhpGenerator\Helpers,
    Nette\Utils\FileSystem;

/**
 * CatalogueCompiler
 * Compiles messages catalogues from resources found by loader
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
   * @param string $folder
   * @param string[] $languages
   */
  function __construct(ILoader $loader, string $folder, array $languages = []) {
    $this->loader = $loader;
    if(!count($languages)) {
      $languages = $loader->getAvailableLanguages();
    }
    $this->languages = $languages;
    $this->folder = $folder;
  }
  
  /**
   * Compile catalogues
   */
  function compile(): void {
    foreach($this->languages as $language) {
      $this->loader->setLang($language);
      $texts = $this->loader->getTexts();
      $texts["__resources"] = $this->loader->getResources();
      $content = "<?php
return " . Helpers::dump($texts) . ";
?>";
      $filename = $this->folder . "/catalogue.$language.php";
      FileSystem::write($filename, $content);
    }
  }
}
?>