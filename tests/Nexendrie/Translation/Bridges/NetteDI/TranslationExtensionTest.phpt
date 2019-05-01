<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\Localization\ITranslator;
use Nexendrie\Translation\Translator;
use Nexendrie\Translation\ILoader;
use Nexendrie\Translation\Loaders\FileLoader;
use Nexendrie\Translation\Loaders\NeonLoader;
use Nexendrie\Translation\Loaders\IniLoader;
use Nexendrie\Translation\Loaders\JsonLoader;
use Nexendrie\Translation\Loaders\YamlLoader;
use Nexendrie\Translation\Loaders\PhpLoader;
use Nexendrie\Translation\Loaders\MessagesCatalogue;
use Nexendrie\Translation\Loaders\Loader;
use Nexendrie\Translation\ILocaleResolver;
use Nexendrie\Translation\ILoaderAwareLocaleResolver;
use Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver;
use Nexendrie\Translation\Resolvers\FallbackLocaleResolver;
use Nexendrie\Translation\Resolvers\LocaleResolver;
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
use Tester\Assert;
use Nette\Application\Application;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nexendrie\Translation\IMessageSelector;
use Nexendrie\Translation\MessageSelector;
use Nexendrie\Translation\CustomMessageSelector;
use Nexendrie\Translation\InvalidMessageSelectorException;

require __DIR__ . "/../../../../bootstrap.php";

final class TranslationExtensionTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  public static $messages = [];
  
  protected function setUp() {
    $this->refreshContainer();
  }
  
  public function testTranslator() {
    /** @var Translator $translator */
    $translator = $this->getService(ITranslator::class);
    Assert::type(Translator::class, $translator);
    Assert::same("XYZ", $translator->translate("xyz"));
  }
  
  public function testDefaultLoader() {
    /** @var NeonLoader $loader */
    $loader = $this->getService(ILoader::class);
    Assert::type(NeonLoader::class, $loader);
    Assert::type("string", $loader->getDefaultLang());
    Assert::same("en", $loader->getDefaultLang());
    Assert::count(1, $loader->folders);
    $config = [
      "translation" => [
        "default" => "cs"
      ]
    ];
    $this->refreshContainer($config);
    $loader = $this->getService(ILoader::class);
    Assert::type(NeonLoader::class, $loader);
    Assert::type("string", $loader->getDefaultLang());
    Assert::same("cs", $loader->getDefaultLang());
  }
  
  /**
   * @param string $name
   * @param string $class
   * @return void
   */
  protected function customLoader(string $name, string $class) {
    $config = [
      "translation" => [
        "loader" => [
          "name" => $name
        ]
      ]
    ];
    $this->refreshContainer($config);
    $loader = $this->getService(ILoader::class);
    /** @var ILoader $loader */
    Assert::type($class, $loader);
  }
  
  public function testCustomLoader() {
    $this->customLoader("ini", IniLoader::class);
    $this->customLoader("json", JsonLoader::class);
    $this->customLoader("yaml", YamlLoader::class);
    $this->customLoader("php", PhpLoader::class);
    $this->customLoader("catalogue", MessagesCatalogue::class);
    $this->customLoader(Loader::class, Loader::class);
  }
  
  public function testInvalidLoader() {
    $config = [
      "translation" => [
        "loader" => [
          "name" => "invalid"
        ]
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidLoaderException::class);
    $config = [
      "translation" => [
        "loader" => [
          "name" => \stdClass::class
        ]
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidLoaderException::class);
  }
  
  public function testDefaultResolver() {
    /** @var ChainLocaleResolver $resolver */
    $resolver = $this->getService(ILocaleResolver::class);
    Assert::type(ChainLocaleResolver::class, $resolver);
    Assert::null($resolver->resolve());
  }
  
  /**
   * @param string $name
   * @param string $class
   * @return void
   */
  protected function customResolver(string $name, string $class) {
    $config = [
      "translation" => [
        "localeResolver" => $name
      ]
    ];
    $this->refreshContainer($config);
    $resolver = $this->getService(ILocaleResolver::class);
    /** @var ILocaleResolver $resolver */
    Assert::type($class, $resolver);
  }
  
  public function testCustomResolver() {
    $this->customResolver("environment", EnvironmentLocaleResolver::class);
    $this->customResolver("fallback", FallbackLocaleResolver::class);
    $this->customResolver("session", SessionLocaleResolver::class);
    $this->customResolver("header", HeaderLocaleResolver::class);
    $this->customResolver("param", ParamLocaleResolver::class);
    $this->customResolver(LocaleResolver::class, LocaleResolver::class);
  }
  
  public function testInvalidResolver() {
    $config = [
      "translation" => [
        "localeResolver" => "invalid"
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidLocaleResolverException::class);
    $config = [
      "translation" => [
        "localeResolver" => \stdClass::class
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidLocaleResolverException::class);
  }
  
  public function testChainResolver() {
    $config = [
      "translation" => [
        "localeResolver" => [
          "fallback", "environment"
        ]
      ]
    ];
    $this->refreshContainer($config);
    /** @var ChainLocaleResolver $resolver */
    $resolver = $this->getService(ILocaleResolver::class);
    Assert::type(ChainLocaleResolver::class, $resolver);
    $resolver[1]->lang = "cs";
    Assert::same("cs", $resolver->resolve());
  }
  
  public function testLoaderAwareResolver() {
    $config = [
      "translation" => [
        "localeResolver" => "header"
      ]
    ];
    $this->refreshContainer($config);
    /** @var HeaderLocaleResolver $resolver */
    $resolver = $this->getService(ILocaleResolver::class);
    Assert::type(ILoaderAwareLocaleResolver::class, $resolver);
    Assert::null($resolver->resolve());
  }
  
  public function testAppRequestAwareResolver() {
    /** @var Application $application */
    $application = $this->getService(Application::class);
    $count = (is_null($application->onRequest) ? 0 : count($application->onRequest));
    $config = [
      "translation" => [
        "localeResolver" => "param"
      ]
    ];
    $this->refreshContainer($config);
    /** @var Application $application */
    $application = $this->getService(Application::class);
    Assert::count($count + 1, $application->onRequest);
  }
  
  public function testInvalidFolder() {
    $config = [
      "translation" => [
        "loader" => [
          "folders" => [
            "/dev/null"
          ]
        ]
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidFolderException::class);
  }
  
  public function testDefaultMessageSelector() {
    /** @var MessageSelector $resolver */
    $resolver = $this->getService(IMessageSelector::class);
    Assert::type(MessageSelector::class, $resolver);
  }
  
  public function testCustomMessageSelector() {
    $config = [
      "translation" => [
        "messageSelector" => CustomMessageSelector::class
      ]
    ];
    $this->refreshContainer($config);
    /** @var CustomMessageSelector $resolver */
    $resolver = $this->getService(IMessageSelector::class);
    Assert::type(CustomMessageSelector::class, $resolver);
  }
  
  public function testInvalidMessageSelector() {
    $config = [
      "translation" => [
        "messageSelector" => \stdClass::class
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidMessageSelectorException::class);
  }
  
  public function testPanel() {
    $panel = $this->getService(TranslationPanel::class);
    Assert::type(TranslationPanel::class, $panel);
    $panel = \Tracy\Debugger::getBar()->getPanel("translation");
    Assert::type(TranslationPanel::class, $panel);
    $config = [
      "translation" => [
        "debugger" => false
      ]
    ];
    Assert::exception(function() use($config){
      $this->refreshContainer($config);
      $panel = $this->getService(TranslationPanel::class);
      Assert::type(TranslationPanel::class, $panel);
    }, MissingServiceException::class);
  }
  
  public function testCompiler() {
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
    $loader = $this->getService(ILoader::class);
    Assert::type(MessagesCatalogue::class, $loader);
    /** @var NeonLoader $originalLoader */
    $originalLoader = $this->getContainer()
      ->getService("translation.originalLoader");
    Assert::type(NeonLoader::class, $originalLoader);
    $compiler =  $this->getService(CatalogueCompiler::class);
    Assert::type(CatalogueCompiler::class, $compiler);
    /** @var Translator $translator */
    $translator = $this->getService(ITranslator::class);
    Assert::same("Content", $translator->translate("book.content"));
    Assert::same("Test", $translator->translate("book.test"));
    Assert::same("ABC", $translator->translate("abc.multi.abc"));
    $result = $translator->translate("param", 0, ["param1" => "value1"]);
    Assert::same("Param1: value1", $result);
    $translator->lang = "cs";
    Assert::same("Obsah", $translator->translate("book.content"));
    Assert::same("Test", $translator->translate("book.test"));
    Assert::same("Abc", $translator->translate("abc.multi.abc"));
    $result = $translator->translate("param", 0, ["param1" => "value1"]);
    Assert::same("Param2: value1", $result);
    $translator->lang = "xyz";
    Assert::same("book.content", $translator->translate("book.content"));
    Assert::same("book.test", $translator->translate("book.test"));
    Assert::same("abc.multi.abc", $translator->translate("abc.multi.abc"));
    $result = $translator->translate("param", 0, ["param1" => "value1"]);
    Assert::same("param", $result);
  }
  
  public function testCompilerWithLanguages() {
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
    /** @var ILoader $loader */
    $loader = $this->getService(ILoader::class);
    Assert::type(MessagesCatalogue::class, $loader);
    /** @var NeonLoader $originalLoader */
    $originalLoader = $this->getContainer()
      ->getService("translation.originalLoader");
    Assert::type(NeonLoader::class, $originalLoader);
    $compiler =  $this->getService(CatalogueCompiler::class);
    Assert::type(CatalogueCompiler::class, $compiler);
    /** @var Translator $translator */
    $translator = $this->getService(ITranslator::class);
    Assert::same("Content", $translator->translate("book.content"));
    Assert::same("Test", $translator->translate("book.test"));
    Assert::same("ABC", $translator->translate("abc.multi.abc"));
    $result = $translator->translate("param", 0, ["param1" => "value1"]);
    Assert::same("Param1: value1", $result);
    $translator->lang = "cs";
    Assert::same("Obsah", $translator->translate("book.content"));
    Assert::same("Test", $translator->translate("book.test"));
    Assert::same("Abc", $translator->translate("abc.multi.abc"));
    $result = $translator->translate("param", 0, ["param1" => "value1"]);
    Assert::same("Param2: value1", $result);
    $translator->lang = "xyz";
    Assert::same("book.content", $translator->translate("book.content"));
    Assert::same("book.test", $translator->translate("book.test"));
    Assert::same("abc.multi.abc", $translator->translate("abc.multi.abc"));
    $result = $translator->translate("param", 0, ["param1" => "value1"]);
    Assert::same("param", $result);
  }
  
  public function testTranslationProvider() {
    $config = [
      "extensions" => [
        "provider" => ProviderExtension::class
      ]
    ];
    $this->refreshContainer($config);
    /** @var FileLoader $loader */
    $loader = $this->getService(FileLoader::class);
    Assert::contains(__DIR__ . "/../../../../_temp", $loader->getFolders());
  }
  
  public function testLatte() {
    /** @var ILatteFactory $factory */
    $factory = $this->getService(ILatteFactory::class);
    $latte = $factory->create();
    Assert::contains("translate", $latte->getFilters());
    Assert::true(array_key_exists("translator", $latte->getProviders()));
  }
  
  public static function onUntranslated(string $message): void {
    static::$messages[] = $message;
  }
  
  public function testOnUntranslated() {
    $config = [
      "translation" => [
        "onUntranslated" => [
          static::class . "::onUntranslated"
        ]
      ]
    ];
    $this->refreshContainer($config);
    Assert::count(0, static::$messages);
    /** @var Translator $translator */
    $translator = $this->getService(Translator::class);
    $translator->translate("messages.nonsense");
    Assert::count(1, static::$messages);
  }
}

$test = new TranslationExtensionTest();
$test->run();
?>