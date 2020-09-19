<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Nette\PhpGenerator\Dumper;
use Nette\Utils\FileSystem;

/**
 * CatalogueCompiler
 * Compiles messages catalogues from resources found by loader
 *
 * @author Jakub Konečný
 * @method void onCompile(CatalogueCompiler $compiler, string $language)
 */
final class CatalogueCompiler {
  use \Nette\SmartObject;

  private ILoader $loader;
  /** @var string[] */
  private array $languages = [];
  private string $folder = "";
  /** @var callable[] */
  public array $onCompile = [];
  
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
    $lang = $this->loader->getLang();
    foreach($this->languages as $language) {
      $this->loader->setLang($language);
      $texts = $this->loader->getTexts();
      if(!$this->isCatalogueExpired($language)) {
        continue;
      }
      $texts["__resources"] = $this->loader->getResources();
      $content = "<?php
return " . (new Dumper())->dump($texts) . ";
?>";
      $filename = $this->getCatalogueFilename($language);
      FileSystem::write($filename, $content);
      $this->onCompile($this, $language);
    }
    $this->loader->setLang($lang);
  }
}
?>