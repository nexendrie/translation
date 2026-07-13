<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteDI;

use Latte\Engine;
use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\Group;
use MyTester\Attributes\RequiresPhpVersion;
use MyTester\Attributes\TestSuite;
use Nexendrie\Translation\Loaders\TomlLoader;
use Nexendrie\Translation\Translator;
use Nexendrie\Translation\Loader;
use Nexendrie\Translation\Loaders\FileLoader;
use Nexendrie\Translation\Loaders\NeonLoader;
use Nexendrie\Translation\Loaders\IniLoader;
use Nexendrie\Translation\Loaders\JsonLoader;
use Nexendrie\Translation\Loaders\YamlLoader;
use Nexendrie\Translation\Loaders\PhpLoader;
use Nexendrie\Translation\Loaders\MessagesCatalogue;
use Nexendrie\Translation\LocaleResolver;
use Nexendrie\Translation\LoaderAwareLocaleResolver;
use Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver;
use Nexendrie\Translation\Resolvers\FallbackLocaleResolver;
use Nexendrie\Translation\Resolvers\ChainLocaleResolver;
use Nexendrie\Translation\Resolvers\SessionLocaleResolver;
use Nexendrie\Translation\Resolvers\HeaderLocaleResolver;
use Nexendrie\Translation\Bridges\NetteApplication\ParamLocaleResolver;
use Nexendrie\Translation\CatalogueCompiler;
use Nexendrie\Translation\InvalidLocaleResolverException;
use Nexendrie\Translation\InvalidFolderException;
use Nexendrie\Translation\InvalidLoaderException;
use Nexendrie\Translation\Bridges\Tracy\TranslationPanel;
use Nette\DI\MissingServiceException;
use Nette\Application\Application;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nexendrie\Translation\IMessageSelector;
use Nexendrie\Translation\IntervalsMessageSelector;
use Nexendrie\Translation\CustomMessageSelector;
use Nexendrie\Translation\InvalidMessageSelectorException;

#[TestSuite("TranslationExtension")]
#[Group("nette")]
final class TranslationExtensionTest extends \MyTester\TestCase
{
    use \MyTester\Bridges\NetteDI\TCompiledContainer;

    #[BeforeTest]
    public function setUp(): void
    {
        $this->refreshContainer();
    }

    public function testTranslator(): void
    {
        $translator = $this->getService(\Nette\Localization\Translator::class);
        $this->assertType(Translator::class, $translator);
        $this->assertSame("XYZ", $translator->translate("xyz"));
    }

    public function testDefaultLoader(): void
    {
        /** @var NeonLoader $loader */
        $loader = $this->getService(Loader::class);
        $this->assertType(NeonLoader::class, $loader);
        $this->assertType("string", $loader->getDefaultLang());
        $this->assertSame("en", $loader->getDefaultLang());
        $this->assertCount(1, $loader->folders);
        $config = [
            "translation" => [
                "default" => "cs"
            ]
        ];
        $this->refreshContainer($config);
        /** @var NeonLoader $loader */
        $loader = $this->getService(Loader::class);
        $this->assertType(NeonLoader::class, $loader);
        $this->assertType("string", $loader->getDefaultLang());
        $this->assertSame("cs", $loader->getDefaultLang());
    }

    protected function customLoader(string $name, string $class): void
    {
        $config = [
            "translation" => [
                "loader" => [
                    "name" => $name
                ]
            ]
        ];
        $this->refreshContainer($config);
        $loader = $this->getService(Loader::class);
        $this->assertType($class, $loader);
    }

    public function testCustomLoader(): void
    {
        $this->customLoader("ini", IniLoader::class);
        $this->customLoader("json", JsonLoader::class);
        $this->customLoader("yaml", YamlLoader::class);
        $this->customLoader("php", PhpLoader::class);
        $this->customLoader("toml", TomlLoader::class);
        $this->customLoader("catalogue", MessagesCatalogue::class);
        $this->customLoader(\Nexendrie\Translation\Loaders\Loader::class, \Nexendrie\Translation\Loaders\Loader::class);
    }

    public function testInvalidLoader(): void
    {
        $config = [
            "translation" => [
                "loader" => [
                    "name" => "invalid"
                ]
            ]
        ];
        $this->assertThrowsException(function () use ($config) {
            $this->refreshContainer($config);
        }, InvalidLoaderException::class);
        $config = [
            "translation" => [
                "loader" => [
                    "name" => \stdClass::class
                ]
            ]
        ];
        $this->assertThrowsException(function () use ($config) {
            $this->refreshContainer($config);
        }, InvalidLoaderException::class);
    }

    public function testDefaultResolver(): void
    {
        /** @var ChainLocaleResolver $resolver */
        $resolver = $this->getService(LocaleResolver::class);
        $this->assertType(ChainLocaleResolver::class, $resolver);
        $this->assertNull($resolver->resolve());
    }

    /**
     * @param string $name
     * @param string $class
     * @return void
     */
    protected function customResolver(string $name, string $class): void
    {
        $config = [
            "translation" => [
                "localeResolver" => $name
            ]
        ];
        $this->refreshContainer($config);
        $resolver = $this->getService(LocaleResolver::class);
        $this->assertType($class, $resolver);
    }

    public function testCustomResolver(): void
    {
        $this->customResolver("environment", EnvironmentLocaleResolver::class);
        $this->customResolver("fallback", FallbackLocaleResolver::class);
        $this->customResolver("session", SessionLocaleResolver::class);
        $this->customResolver("header", HeaderLocaleResolver::class);
        $this->customResolver("param", ParamLocaleResolver::class);
        $this->customResolver(
            \Nexendrie\Translation\Resolvers\LocaleResolver::class,
            \Nexendrie\Translation\Resolvers\LocaleResolver::class
        );
    }

    public function testInvalidResolver(): void
    {
        $config = [
            "translation" => [
                "localeResolver" => "invalid"
            ]
        ];
        $this->assertThrowsException(function () use ($config) {
            $this->refreshContainer($config);
        }, InvalidLocaleResolverException::class);
        $config = [
            "translation" => [
                "localeResolver" => \stdClass::class
            ]
        ];
        $this->assertThrowsException(function () use ($config) {
            $this->refreshContainer($config);
        }, InvalidLocaleResolverException::class);
    }

    public function testChainResolver(): void
    {
        $config = [
            "translation" => [
                "localeResolver" => [
                    "fallback", "environment"
                ]
            ]
        ];
        $this->refreshContainer($config);
        /** @var ChainLocaleResolver $resolver */
        $resolver = $this->getService(LocaleResolver::class);
        $this->assertType(ChainLocaleResolver::class, $resolver);
        $this->assertType(FallbackLocaleResolver::class, $resolver[0]);
        $this->assertType(EnvironmentLocaleResolver::class, $resolver[1]);
        /** @var EnvironmentLocaleResolver $environmentResolver */
        $environmentResolver = $resolver[1];
        $environmentResolver->lang = "cs";
        $this->assertSame("cs", $resolver->resolve());
    }

    public function testLoaderAwareResolver(): void
    {
        $config = [
            "translation" => [
                "localeResolver" => "header"
            ]
        ];
        $this->refreshContainer($config);
        /** @var HeaderLocaleResolver $resolver */
        $resolver = $this->getService(LocaleResolver::class);
        $this->assertType(LoaderAwareLocaleResolver::class, $resolver);
        $this->assertNull($resolver->resolve());
    }

    public function testAppRequestAwareResolver(): void
    {
        $application = $this->getService(Application::class);
        $count = (is_null($application->onRequest) ? 0 : count($application->onRequest));
        $config = [
            "translation" => [
                "localeResolver" => "param"
            ]
        ];
        $this->refreshContainer($config);
        $application = $this->getService(Application::class);
        $this->assertCount($count + 1, $application->onRequest);
    }

    public function testInvalidFolder(): void
    {
        $config = [
            "translation" => [
                "loader" => [
                    "folders" => [
                        "/dev/null"
                    ]
                ]
            ]
        ];
        $this->assertThrowsException(function () use ($config) {
            $this->refreshContainer($config);
        }, InvalidFolderException::class);
    }

    public function testDefaultMessageSelector(): void
    {
        /** @var IntervalsMessageSelector $resolver */
        $resolver = $this->getService(IMessageSelector::class);
        $this->assertType(IntervalsMessageSelector::class, $resolver);
    }

    public function testCustomMessageSelector(): void
    {
        $config = [
            "translation" => [
                "messageSelector" => CustomMessageSelector::class
            ]
        ];
        $this->refreshContainer($config);
        /** @var CustomMessageSelector $resolver */
        $resolver = $this->getService(IMessageSelector::class);
        $this->assertType(CustomMessageSelector::class, $resolver);
    }

    public function testInvalidMessageSelector(): void
    {
        $config = [
            "translation" => [
                "messageSelector" => \stdClass::class
            ]
        ];
        $this->assertThrowsException(function () use ($config) {
            $this->refreshContainer($config);
        }, InvalidMessageSelectorException::class);
    }

    public function testPanel(): void
    {
        $panel = $this->getService(TranslationPanel::class);
        $this->assertType(TranslationPanel::class, $panel);
        $panel = \Tracy\Debugger::getBar()->getPanel("translation");
        $this->assertType(TranslationPanel::class, $panel);
        $this->assertThrowsException(function () {
            $this->refreshContainer([
                "translation" => [
                    "debugger" => false
                ]
            ]);
            $panel = $this->getService(TranslationPanel::class);
            $this->assertType(TranslationPanel::class, $panel);
        }, MissingServiceException::class);
    }

    public function testCompiler(): void
    {
        $config = [
            "translation" => [
                "localeResolver" => "manual",
                "compiler" => [
                    "enabled" => true,
                    "languages" => [],
                ],
                "loader" => [
                    "folders" => [
                        "%appDir%/lang", "%appDir%/lang2"
                    ]
                ]
            ]
        ];
        $this->refreshContainer($config);
        /** @var MessagesCatalogue $loader */
        $loader = $this->getService(Loader::class);
        $this->assertType(MessagesCatalogue::class, $loader);
        /** @var NeonLoader $originalLoader */
        $originalLoader = $this->getContainer()
            ->getService("translation.originalLoader");
        $this->assertType(NeonLoader::class, $originalLoader);
        $compiler = $this->getService(CatalogueCompiler::class);
        $this->assertType(CatalogueCompiler::class, $compiler);
        /** @var Translator $translator */
        $translator = $this->getService(\Nette\Localization\Translator::class);
        $this->assertSame("Content", $translator->translate("book.content"));
        $this->assertSame("Test", $translator->translate("book.test"));
        $this->assertSame("ABC", $translator->translate("abc.multi.abc"));
        $result = $translator->translate("param", 0, ["param1" => "value1"]);
        $this->assertSame("Param1: value1", $result);
        $translator->lang = "cs";
        $this->assertSame("Obsah", $translator->translate("book.content"));
        $this->assertSame("Test", $translator->translate("book.test"));
        $this->assertSame("Abc", $translator->translate("abc.multi.abc"));
        $result = $translator->translate("param", 0, ["param1" => "value1"]);
        $this->assertSame("Param2: value1", $result);
        $translator->lang = "xyz";
        $this->assertSame("book.content", $translator->translate("book.content"));
        $this->assertSame("book.test", $translator->translate("book.test"));
        $this->assertSame("abc.multi.abc", $translator->translate("abc.multi.abc"));
        $result = $translator->translate("param", 0, ["param1" => "value1"]);
        $this->assertSame("param", $result);
    }

    public function testCompilerWithLanguages(): void
    {
        $config = [
            "translation" => [
                "localeResolver" => "manual",
                "compiler" => [
                    "enabled" => true,
                    "languages" => ["en", "cs", "xyz"],
                ],
                "loader" => [
                    "folders" => [
                        "%appDir%/lang", "%appDir%/lang2"
                    ]
                ]
            ]
        ];
        $this->refreshContainer($config);
        /** @var Loader $loader */
        $loader = $this->getService(Loader::class);
        $this->assertType(MessagesCatalogue::class, $loader);
        /** @var NeonLoader $originalLoader */
        $originalLoader = $this->getContainer()
            ->getService("translation.originalLoader");
        $this->assertType(NeonLoader::class, $originalLoader);
        $compiler = $this->getService(CatalogueCompiler::class);
        $this->assertType(CatalogueCompiler::class, $compiler);
        /** @var Translator $translator */
        $translator = $this->getService(\Nette\Localization\Translator::class);
        $this->assertSame("Content", $translator->translate("book.content"));
        $this->assertSame("Test", $translator->translate("book.test"));
        $this->assertSame("ABC", $translator->translate("abc.multi.abc"));
        $result = $translator->translate("param", 0, ["param1" => "value1"]);
        $this->assertSame("Param1: value1", $result);
        $translator->lang = "cs";
        $this->assertSame("Obsah", $translator->translate("book.content"));
        $this->assertSame("Test", $translator->translate("book.test"));
        $this->assertSame("Abc", $translator->translate("abc.multi.abc"));
        $result = $translator->translate("param", 0, ["param1" => "value1"]);
        $this->assertSame("Param2: value1", $result);
        $translator->lang = "xyz";
        $this->assertSame("book.content", $translator->translate("book.content"));
        $this->assertSame("book.test", $translator->translate("book.test"));
        $this->assertSame("abc.multi.abc", $translator->translate("abc.multi.abc"));
        $result = $translator->translate("param", 0, ["param1" => "value1"]);
        $this->assertSame("param", $result);
    }

    public function testTranslationProvider(): void
    {
        $config = [
            "extensions" => [
                "provider" => ProviderExtension::class
            ]
        ];
        $this->refreshContainer($config);
        /** @var FileLoader $loader */
        $loader = $this->getService(FileLoader::class);
        $this->assertContains(__DIR__ . "/../../../../temp", $loader->getFolders());
    }

    public function testLatte(): void
    {
        if (version_compare(Engine::VERSION, "3", ">=")) {
            $this->markTestSkipped("Latte 3 support cannot be tested at the moment.");
        }
        /** @var LatteFactory $factory */
        $factory = $this->getService(LatteFactory::class);
        $latte = $factory->create();
        $this->assertContains("translate", $latte->getFilters());
        $this->assertTrue(array_key_exists("translator", $latte->getProviders()));
    }
}
