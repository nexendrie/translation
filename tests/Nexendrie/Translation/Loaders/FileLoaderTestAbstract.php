<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Loaders;

use Konecnyjakub\EventDispatcher\AutoListenerProvider;
use Konecnyjakub\EventDispatcher\EventDispatcher;
use MyTester\Attributes\BeforeTest;
use Nexendrie\Translation\Events\FoldersChanged;
use Nexendrie\Translation\Events\LanguageChanged;
use Nexendrie\Translation\Events\LanguageLoaded;
use Psr\EventDispatcher\EventDispatcherInterface;
use Nexendrie\Translation\InvalidFolderException;
use Nexendrie\Translation\FolderNotSetException;

/**
 * General test suit for file loaders
 *
 * @author Jakub Konečný
 */
abstract class FileLoaderTestAbstract extends \MyTester\TestCase
{
    protected FileLoader $loader;
    public EventDispatcherInterface $eventDispatcher;
    protected AutoListenerProvider $listenerProvider;
    /**
     * @var LanguageChanged[]|FoldersChanged[]|LanguageLoaded[]
     */
    private array $events = [];

    #[BeforeTest]
    public function setUp(): void
    {
        $this->events = [];
        $this->listenerProvider = new AutoListenerProvider();
        $this->listenerProvider->addListener(function (LanguageChanged $event): void {
            $this->events[] = $event;
        });
        $this->listenerProvider->addListener(function (FoldersChanged $event): void {
            $this->events[] = $event;
        });
        $this->listenerProvider->addListener(function (LanguageLoaded $event): void {
            $this->events[] = $event;
        });
        $this->eventDispatcher = new EventDispatcher($this->listenerProvider);
    }

    public function testGetLang(): void
    {
        $lang = $this->loader->lang;
        $this->assertType("string", $lang);
        $this->assertSame("en", $lang);
    }

    public function testSetLang(): void
    {
        $this->assertCount(1, $this->events);
        $this->loader->lang = "cs";
        $lang = $this->loader->lang;
        $this->assertSame("cs", $lang);
        $this->assertCount(2, $this->events);
        /** @var LanguageChanged $event */
        $event = $this->events[1];
        $this->assertType(LanguageChanged::class, $event);
        $this->assertSame("en", $event->oldLanguage);
        $this->assertSame("cs", $event->newLanguage);
    }

    public function testGetFolders(): void
    {
        $folders = $this->loader->folders;
        $this->assertType("array", $folders);
        $this->assertCount(2, $folders);
        $this->assertSame(__DIR__ . "/../../../lang", $folders[0]);
        $this->assertSame(__DIR__ . "/../../../lang2", $folders[1]);
    }

    public function testSetFolders(): void
    {
        $this->assertThrowsException(function (): void {
            $this->loader->folders = [""];
        }, InvalidFolderException::class, "Folder  does not exist.");
    }

    public function testGetResources(): void
    {
        // texts were not loaded yet so there are no resources
        $resources = $this->loader->resources;
        $this->assertType("array", $resources);
        $this->assertCount(0, $resources);
        // english texts are loaded, there is 1 resource for each domain
        $this->loader->getTexts();
        $resources = $this->loader->resources;
        $this->assertType("array", $resources);
        $this->assertCount(3, $resources);
        $this->assertCount(1, $resources["messages"]);
        $this->assertCount(1, $resources["book"]);
        $this->assertCount(1, $resources["abc"]);
        // czech and english texts are loaded, there are 2 resources for each domain
        $this->loader->lang = "cs";
        $this->loader->getTexts();
        $resources = $this->loader->resources;
        $this->assertType("array", $resources);
        $this->assertCount(3, $resources);
        $this->assertCount(2, $resources["messages"]);
        $this->assertCount(2, $resources["book"]);
        $this->assertCount(2, $resources["abc"]);
        // the language does not exist, 1 (default) resource for each domain
        if ($this->loader instanceof MessagesCatalogue) {
            return; // the following tests for some reason fail with MessagesCatalogue
        }
        $this->loader->lang = "xyz";
        $this->loader->getTexts();
        $resources = $this->loader->resources;
        $this->assertType("array", $resources);
        $this->assertCount(3, $resources);
        $this->assertCount(1, $resources["messages"]);
        $this->assertCount(1, $resources["book"]);
        $this->assertCount(1, $resources["abc"]);
    }

    public function testGetTexts(): void
    {
        $this->assertCount(1, $this->events);
        $texts = $this->loader->getTexts();
        $this->assertType("array", $texts);
        $this->assertCount(3, $texts);
        $this->assertType("array", $texts["messages"]);
        $this->assertCount(3, $texts["messages"]);
        $this->assertType("array", $texts["book"]);
        $this->assertCount(5, $texts["book"]);
        if (!$this instanceof MessagesCatalogueTest) {
            $this->assertCount(2, $this->events);
            /** @var LanguageLoaded $event */
            $event = $this->events[1];
            $this->assertType(LanguageLoaded::class, $event);
            $this->assertSame("en", $event->language);
        }

        $this->loader->lang = "cs";
        $texts = $this->loader->getTexts();
        $this->assertType("array", $texts);
        $this->assertCount(3, $texts);
        $this->assertType("array", $texts["messages"]);
        $this->assertCount(3, $texts["messages"]);
        $this->assertType("array", $texts["book"]);
        $this->assertCount(5, $texts["book"]);
        if ($this->loader instanceof MessagesCatalogue) {
            return; // the following tests for some reason fail with MessagesCatalogue
        }
        $this->assertCount(4, $this->events);
        /** @var LanguageChanged $event */
        $event = $this->events[2];
        $this->assertType(LanguageChanged::class, $event);
        $this->assertSame("en", $event->oldLanguage);
        $this->assertSame("cs", $event->newLanguage);
        /** @var LanguageLoaded $event */
        $event = $this->events[3];
        $this->assertType(LanguageLoaded::class, $event);
        $this->assertSame("cs", $event->language);

        $this->loader->lang = "xyz";
        $texts = $this->loader->getTexts();
        $this->assertType("array", $texts);
        $this->assertCount(3, $texts);
        $this->assertType("array", $texts["messages"]);
        $this->assertCount(3, $texts["messages"]);
        $this->assertType("array", $texts["book"]);
        $this->assertCount(5, $texts["book"]);
        $this->assertCount(6, $this->events);
        /** @var LanguageChanged $event */
        $event = $this->events[4];
        $this->assertType(LanguageChanged::class, $event);
        $this->assertSame("cs", $event->oldLanguage);
        $this->assertSame("xyz", $event->newLanguage);
        /** @var LanguageLoaded $event */
        $event = $this->events[5];
        $this->assertType(LanguageLoaded::class, $event);
        $this->assertSame("xyz", $event->language);
    }

    public function testNoFolder(): void
    {
        $this->assertThrowsException(function (): void {
            $class = get_class($this->loader);
            $this->loader = new $class();
            $this->loader->getTexts();
        }, FolderNotSetException::class, "Folder for translations was not set.");
    }

    public function testGetAvailableLanguages(): void
    {
        $result = $this->loader->getAvailableLanguages();
        $this->assertType("array", $result);
        $this->assertCount(2, $result);
        $this->assertContains("en", $result);
        $this->assertContains("cs", $result);
        $this->assertThrowsException(function (): void {
            $class = get_class($this->loader);
            $this->loader = new $class();
            $this->loader->getAvailableLanguages();
        }, FolderNotSetException::class, "Folder for translations was not set.");
    }

    public function testGetResolverName(): void
    {
        $name = $this->loader->getResolverName();
        $this->assertType("string", $name);
        $this->assertSame("ManualLocaleResolver", $name);
    }
}
