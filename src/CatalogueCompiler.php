<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Nexendrie\Translation\Loaders\ILoader;
use Nette\PhpGenerator\Helpers;
use Nette\Utils\FileSystem;

/**
 * CatalogueCompiler
 * Compiles messages catalogues from resources found by loader
 *
 * @author Jakub Konečný
 * @method void onCompile(CatalogueCompiler $compiler, string $language)
 */
class CatalogueCompiler {
  use \Nette\SmartObject;
  
  /** @var ILoader */
  protected $loader;
  /** @var string[] */
  protected $languages = [];
  /** @var string */
  protected $folder = "";
  /** @var callable[] */
  public $onCompile = [];
  
  /**
   * @param string[] $languages
   */
  public function __construct(ILoader $loader, string $folder, array $languages = []) {
    $this->loader = $loader;
    if(count($languages) === 0) {
      $languages = $loader->getAvailableLanguages();
    }
    $this->languages = $languages;
    $this->folder = $folder;
  }
  
  protected function getCatalogueFilename(string $language): string {
    return $this->folder . "/catalogue.$language.php";
  }
  
  protected function isCatalogueExpired(string $language): bool {
    $catalogueFilename = $this->getCatalogueFilename($language);
    if(!is_file($catalogueFilename)) {
      return true;
    }
    $catalogueInfo = new \SplFileInfo($catalogueFilename);
    $lastGenerated = $catalogueInfo->getCTime();
    foreach($this->loader->getResources() as $domain) {
      foreach($domain as $filename) {
        $fileinfo = new \SplFileInfo($filename);
        if($fileinfo->getMTime() > $lastGenerated) {
          return true;
        }
      }
    }
    return false;
  }
  
  /**
   * Compile catalogues
   */
  public function compile(): void {
    foreach($this->languages as $language) {
      $this->loader->setLang($language);
      $texts = $this->loader->getTexts();
      if(!$this->isCatalogueExpired($language)) {
        continue;
      }
      $texts["__resources"] = $this->loader->getResources();
      $content = "<?php
return " . Helpers::dump($texts) . ";
?>";
      $filename = $this->getCatalogueFilename($language);
      FileSystem::write($filename, $content);
      $this->onCompile($this, $language);
    }
  }
}
?>