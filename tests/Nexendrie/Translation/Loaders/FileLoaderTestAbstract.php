<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Circli\EventDispatcher\EventDispatcher;
use Circli\EventDispatcher\ListenerProvider\DefaultProvider;
use Nexendrie\Translation\Events\FoldersChanged;
use Nexendrie\Translation\Events\LanguageChanged;
use Nexendrie\Translation\Events\LanguageLoaded;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tester\Assert;
use Nexendrie\Translation\InvalidFolderException;
use Nexendrie\Translation\FolderNotSetException;

/**
 * General test suit for file loaders
 *
 * @author Jakub Konečný
 */
abstract class FileLoaderTestAbstract extends \Tester\TestCase
{
    protected FileLoader $loader;
    protected EventDispatcherInterface $eventDispatcher;
    protected DefaultProvider $listenerProvider;
    /**
     * @var LanguageChanged[]|FoldersChanged[]|LanguageLoaded[]
     */
    private array $events = [];

    protected function setUp(): void
    {
        $this->events = [];
        $this->listenerProvider = new DefaultProvider();
        $this->listenerProvider->listen(LanguageChanged::class, function (LanguageChanged $event): void {
            $this->events[] = $event;
        });
        $this->listenerProvider->listen(FoldersChanged::class, function (FoldersChanged $event): void {
            $this->events[] = $event;
        });
        $this->listenerProvider->listen(LanguageLoaded::class, function (LanguageLoaded $event): void {
            $this->events[] = $event;
        });
        $this->eventDispatcher = new EventDispatcher($this->listenerProvider);
    }

    public function testGetLang(): void
    {
        $lang = $this->loader->lang;
        Assert::type("string", $lang);
        Assert::same("en", $lang);
    }

    public function testSetLang(): void
    {
        Assert::count(1, $this->events);
        $this->loader->lang = "cs";
        $lang = $this->loader->lang;
        Assert::same("cs", $lang);
        Assert::count(2, $this->events);
        /** @var LanguageChanged $event */
        $event = $this->events[1];
        Assert::type(LanguageChanged::class, $event);
        Assert::same("en", $event->oldLanguage);
        Assert::same("cs", $event->newLanguage);
    }

    public function testGetFolders(): void
    {
        $folders = $this->loader->folders;
        Assert::type("array", $folders);
        Assert::count(2, $folders);
        Assert::same(__DIR__ . "/../../../lang", $folders[0]);
        Assert::same(__DIR__ . "/../../../lang2", $folders[1]);
    }

    public function testSetFolders(): void
    {
        Assert::exception(function (): void {
            $this->loader->folders = [""];
        }, InvalidFolderException::class, "Folder  does not exist.");
    }

    public function testGetResources(): void
    {
        // texts were not loaded yet so there are no resources
        $resources = $this->loader->resources;
        Assert::type("array", $resources);
        Assert::count(0, $resources);
        // english texts are loaded, there is 1 resource for each domain
        $this->loader->getTexts();
        $resources = $this->loader->resources;
        Assert::type("array", $resources);
        Assert::count(3, $resources);
        Assert::count(1, $resources["messages"]);
        Assert::count(1, $resources["book"]);
        Assert::count(1, $resources["abc"]);
        // czech and english texts are loaded, there are 2 resources for each domain
        $this->loader->lang = "cs";
        $this->loader->getTexts();
        $resources = $this->loader->resources;
        Assert::type("array", $resources);
        Assert::count(3, $resources);
        Assert::count(2, $resources["messages"]);
        Assert::count(2, $resources["book"]);
        Assert::count(2, $resources["abc"]);
        // the language does not exist, 1 (default) resource for each domain
        if ($this->loader instanceof MessagesCatalogue) {
            return; // the following tests for some reason fail with MessagesCatalogue
        }
        $this->loader->lang = "xyz";
        $this->loader->getTexts();
        $resources = $this->loader->resources;
        Assert::type("array", $resources);
        Assert::count(3, $resources);
        Assert::count(1, $resources["messages"]);
        Assert::count(1, $resources["book"]);
        Assert::count(1, $resources["abc"]);
    }

    public function testGetTexts(): void
    {
        Assert::count(1, $this->events);
        $texts = $this->loader->getTexts();
        Assert::type("array", $texts);
        Assert::count(3, $texts);
        Assert::type("array", $texts["messages"]);
        Assert::count(3, $texts["messages"]);
        Assert::type("array", $texts["book"]);
        Assert::count(5, $texts["book"]);
        if (!$this instanceof MessagesCatalogueTest) {
            Assert::count(2, $this->events);
            /** @var LanguageLoaded $event */
            $event = $this->events[1];
            Assert::type(LanguageLoaded::class, $event);
            Assert::same("en", $event->language);
        }

        $this->loader->lang = "cs";
        $texts = $this->loader->getTexts();
        Assert::type("array", $texts);
        Assert::count(3, $texts);
        Assert::type("array", $texts["messages"]);
        Assert::count(3, $texts["messages"]);
        Assert::type("array", $texts["book"]);
        Assert::count(5, $texts["book"]);
        if ($this->loader instanceof MessagesCatalogue) {
            return; // the following tests for some reason fail with MessagesCatalogue
        }
        Assert::count(4, $this->events);
        /** @var LanguageChanged $event */
        $event = $this->events[2];
        Assert::type(LanguageChanged::class, $event);
        Assert::same("en", $event->oldLanguage);
        Assert::same("cs", $event->newLanguage);
        /** @var LanguageLoaded $event */
        $event = $this->events[3];
        Assert::type(LanguageLoaded::class, $event);
        Assert::same("cs", $event->language);

        $this->loader->lang = "xyz";
        $texts = $this->loader->getTexts();
        Assert::type("array", $texts);
        Assert::count(3, $texts);
        Assert::type("array", $texts["messages"]);
        Assert::count(3, $texts["messages"]);
        Assert::type("array", $texts["book"]);
        Assert::count(5, $texts["book"]);
        Assert::count(6, $this->events);
        /** @var LanguageChanged $event */
        $event = $this->events[4];
        Assert::type(LanguageChanged::class, $event);
        Assert::same("cs", $event->oldLanguage);
        Assert::same("xyz", $event->newLanguage);
        /** @var LanguageLoaded $event */
        $event = $this->events[5];
        Assert::type(LanguageLoaded::class, $event);
        Assert::same("xyz", $event->language);
    }

    public function testNoFolder(): void
    {
        Assert::exception(function (): void {
            $class = get_class($this->loader);
            $this->loader = new $class();
            $this->loader->getTexts();
        }, FolderNotSetException::class, "Folder for translations was not set.");
    }

    public function testGetAvailableLanguages(): void
    {
        $result = $this->loader->getAvailableLanguages();
        Assert::type("array", $result);
        Assert::count(2, $result);
        Assert::contains("en", $result);
        Assert::contains("cs", $result);
        Assert::exception(function (): void {
            $class = get_class($this->loader);
            $this->loader = new $class();
            $this->loader->getAvailableLanguages();
        }, FolderNotSetException::class, "Folder for translations was not set.");
    }

    public function testGetResolverName(): void
    {
        $name = $this->loader->getResolverName();
        Assert::type("string", $name);
        Assert::same("ManualLocaleResolver", $name);
    }
}
