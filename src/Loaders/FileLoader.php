<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Nexendrie\Translation\LocaleResolver;
use Nexendrie\Translation\Resolvers\ManualLocaleResolver;
use Nexendrie\Translation\SettableLocaleResolver;
use Nexendrie\Translation\InvalidFolderException;
use Nexendrie\Translation\FolderNotSetException;
use Nette\Utils\Arrays;

/**
 * Generic file translations loader
 * Loads texts from {$this->extension} files
 * You need to define method parseFile() which processes individual file
 *
 * @author Jakub Konečný
 * @property string $lang
 * @property-read array $texts
 * @property string[] $folders
 * @property-read array $resources
 * @method void onLanguageChange(FileLoader $loader, string $oldLang, string $newLang)
 * @method void onFoldersChange(FileLoader $loader, string[] $folders)
 * @method void onLoad(FileLoader $loader, string $lang)
 */
abstract class FileLoader implements \Nexendrie\Translation\FileLoader
{
    use \Nette\SmartObject;

    protected const DOMAIN_MASK = "%domain%";
    protected const LANGUAGE_MASK = "%language%";

    public string $defaultLang = "en";
    protected ?string $loadedLang = null;
    protected array $texts = [];
    /** @var string[] */
    protected array $folders = [];
    protected array $resources = [];
    protected string $extension;
    /** @var callable[] */
    public array $onLanguageChange = [];
    /** @var callable[] */
    public array $onFoldersChange = [];
    /** @var callable[] */
    public array $onLoad = [];

    /**
     * @param string[] $folders
     */
    public function __construct(protected LocaleResolver $resolver = new ManualLocaleResolver(), array $folders = [])
    {
        $this->setFolders($folders);
    }

    /**
     * @deprecated Access the property directly
     */
    public function getLang(): string
    {
        return $this->resolver->resolve() ?? $this->defaultLang;
    }

    /**
     * @deprecated Access the property directly
     */
    public function setLang(string $lang): void
    {
        if (is_a($this->resolver, SettableLocaleResolver::class)) {
            $oldLang = $this->lang;
            $this->resolver->setLang($lang);
            $this->onLanguageChange($this, $oldLang, $lang);
        }
    }

    /**
     * @deprecated Access the property directly
     */
    public function getDefaultLang(): string
    {
        return $this->defaultLang;
    }

    /**
     * @deprecated Access the property directly
     */
    public function setDefaultLang(string $defaultLang): void
    {
        $this->defaultLang = $defaultLang;
    }

    /**
     * @return string[]
     * @deprecated Access the property directly
     */
    public function getFolders(): array
    {
        return $this->folders;
    }

    /**
     * @param string[] $folders
     * @throws InvalidFolderException
     * @deprecated Access the property directly
     */
    public function setFolders(array $folders): void
    {
        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                throw new InvalidFolderException("Folder $folder does not exist.");
            }
            $this->folders[] = $folder;
        }
        $this->onFoldersChange($this, $folders);
    }

    protected function addResource(string $filename, string $domain): void
    {
        if (!isset($this->resources[$domain]) || !in_array($filename, $this->resources[$domain], true)) {
            $this->resources[$domain][] = $filename;
        }
    }

    /**
     * @deprecated Access the property directly
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * Parse individual file
     */
    abstract protected function parseFile(string $filename): array;

    /**
     * Load texts from one text domain
     */
    protected function loadDomain(string $name): array
    {
        $return = [];
        $defaultLang = $this->defaultLang;
        $defaultFilename = $this->getLanguageFilenameMask();
        $defaultFilename = str_replace(
            [static::DOMAIN_MASK, static::LANGUAGE_MASK,],
            [$name, $defaultLang,],
            $defaultFilename
        );
        $files = Finder::findFiles($defaultFilename)
            ->from(...$this->folders);
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $default = $this->parseFile($file->getPathname());
            $this->addResource($file->getPathname(), $name);
            $lang = [];
            $filename = str_replace($defaultLang, $this->lang, $defaultFilename);
            $filename = str_replace($defaultFilename, $filename, $file->getPathname());
            if ($this->lang !== $defaultLang && is_file($filename)) {
                $lang = $this->parseFile($filename);
                $this->addResource($filename, $name);
            }
            $return = Arrays::mergeTree($return, Arrays::mergeTree($lang, $default));
        }
        return $return;
    }

    /**
     * Load all texts
     *
     * @throws FolderNotSetException
     */
    protected function loadTexts(): void
    {
        if ($this->lang === $this->loadedLang) {
            return;
        }
        if (count($this->folders) === 0) {
            throw new FolderNotSetException("Folder for translations was not set.");
        }
        $default = $this->defaultLang;
        $this->resources = $texts = [];
        $mask = $this->getLanguageFilenameMask();
        $mask = str_replace([static::DOMAIN_MASK, static::LANGUAGE_MASK,], ["*", $default,], $mask);
        $files = Finder::findFiles($mask)
            ->from(...$this->folders);
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $domain = $file->getBasename((string) Strings::after($mask, "*"));
            $texts[$domain] = $this->loadDomain($domain);
        }
        $this->texts = $texts;
        $this->loadedLang = $this->lang;
        $this->onLoad($this, $this->lang);
    }

    /**
     * @deprecated Access the property directly
     */
    public function getTexts(): array
    {
        $this->loadTexts();
        return $this->texts;
    }

    public function getResolverName(): string
    {
        $class = get_class($this->resolver);
        return (string) Strings::after($class, '\\', -1);
    }

    protected function getLanguageFilenameMask(): string
    {
        return static::DOMAIN_MASK . "." . static::LANGUAGE_MASK . "." . $this->extension;
    }

    /**
     * @return string[]
     * @throws FolderNotSetException
     */
    public function getAvailableLanguages(): array
    {
        if (count($this->folders) === 0) {
            throw new FolderNotSetException("Folder for translations was not set.");
        }
        $languages = [];
        $extension = $this->extension;
        $mask = $this->getLanguageFilenameMask();
        $mask = str_replace([static::DOMAIN_MASK, static::LANGUAGE_MASK,], "*", $mask);
        $files = Finder::findFiles($mask)
            ->from(...$this->folders);
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $filename = $file->getBasename(".$extension");
            $lang = (string) Strings::after($filename, ".");
            if (!in_array($lang, $languages, true)) {
                $languages[] = $lang;
            }
        }
        return $languages;
    }
}
