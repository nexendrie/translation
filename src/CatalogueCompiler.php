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
final class CatalogueCompiler
{
    use \Nette\SmartObject;

    /** @var string[] */
    private readonly array $languages;
    /** @var callable[] */
    public array $onCompile = [];

    /**
     * @param string[] $languages
     */
    public function __construct(private readonly Loader $loader, private readonly string $folder, array $languages = [])
    {
        if (count($languages) === 0) {
            $languages = $loader->getAvailableLanguages();
        }
        $this->languages = $languages;
    }

    private function getCatalogueFilename(string $language): string
    {
        return $this->folder . "/catalogue.$language.php";
    }

    private function isCatalogueExpired(string $language): bool
    {
        $catalogueFilename = $this->getCatalogueFilename($language);
        if (!is_file($catalogueFilename)) {
            return true;
        }
        $catalogueInfo = new \SplFileInfo($catalogueFilename);
        $lastGenerated = $catalogueInfo->getCTime();
        foreach ($this->loader->getResources() as $domain) {
            foreach ($domain as $filename) {
                $fileinfo = new \SplFileInfo($filename);
                if ($fileinfo->getMTime() > $lastGenerated) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Compile catalogues
     */
    public function compile(): void
    {
        $lang = $this->loader->getLang();
        foreach ($this->languages as $language) {
            $this->loader->setLang($language);
            $texts = $this->loader->getTexts();
            if (!$this->isCatalogueExpired($language)) {
                continue;
            }
            $texts["__resources"] = $this->loader->getResources();
            $content = "<?php
return " . (new Dumper())->dump($texts) . ";
";
            $filename = $this->getCatalogueFilename($language);
            FileSystem::write($filename, $content);
            $this->onCompile($this, $language);
        }
        $this->loader->setLang($lang);
    }
}
