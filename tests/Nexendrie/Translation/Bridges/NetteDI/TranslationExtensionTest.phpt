<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\Localization\ITranslator,
    Nexendrie\Translation\Translator,
    Nexendrie\Translation\Loaders\ILoader,
    Nexendrie\Translation\Loaders\FileLoader,
    Nexendrie\Translation\Loaders\NeonLoader,
    Nexendrie\Translation\Loaders\IniLoader,
    Nexendrie\Translation\Loaders\JsonLoader,
    Nexendrie\Translation\Loaders\YamlLoader,
    Nexendrie\Translation\Loaders\PhpLoader,
    Nexendrie\Translation\Loaders\MessagesCatalogue,
    Nexendrie\Translation\Loaders\Loader,
    Nexendrie\Translation\Resolvers\ILocaleResolver,
    Nexendrie\Translation\Resolvers\ILoaderAwareLocaleResolver,
    Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver,
    Nexendrie\Translation\Resolvers\FallbackLocaleResolver,
    Nexendrie\Translation\Resolvers\LocaleResolver,
    Nexendrie\Translation\Resolvers\ChainLocaleResolver,
    Nexendrie\Translation\Resolvers\SessionLocaleResolver,
    Nexendrie\Translation\Resolvers\HeaderLocaleResolver,
    Nexendrie\Translation\Bridges\NetteApplication\ParamLocaleResolver,
    Nexendrie\Translation\CatalogueCompiler,
    Nexendrie\Translation\InvalidLocaleResolverException,
    Nexendrie\Translation\InvalidFolderException,
    Nexendrie\Translation\InvalidLoaderException,
    Nexendrie\Translation\Bridges\Tracy\TranslationPanel,
    Nette\DI\MissingServiceException,
    Tester\Assert,
    Nette\Application\Application,
    Nette\Bridges\ApplicationLatte\ILatteFactory;

require __DIR__ . "/../../../../bootstrap.php";

class TranslationExtensionTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  static public $messages = [];
  
  function setUp() {
    $this->refreshContainer();
  }
  
  function testTranslator() {
    /** @var Translator $translator */
    $translator = $this->getService(ITranslator::class);
    Assert::type(Translator::class, $translator);
    Assert::same("XYZ", $translator->translate("xyz"));
  }
  
  function testDefaultLoader() {
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
  
  function testCustomLoader() {
    $this->customLoader("ini", IniLoader::class);
    $this->customLoader("json", JsonLoader::class);
    $this->customLoader("yaml", YamlLoader::class);
    $this->customLoader("php", PhpLoader::class);
    $this->customLoader("catalogue", MessagesCatalogue::class);
    $this->customLoader(Loader::class, Loader::class);
  }
  
  function testInvalidLoader() {
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
          "name" => "stdClass"
        ]
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidLoaderException::class);
  }
  
  function testDefaultResolver() {
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
  
  function testCustomResolver() {
    $this->customResolver("environment", EnvironmentLocaleResolver::class);
    $this->customResolver("fallback", FallbackLocaleResolver::class);
    $this->customResolver("session", SessionLocaleResolver::class);
    $this->customResolver("header", HeaderLocaleResolver::class);
    $this->customResolver("param", ParamLocaleResolver::class);
    $this->customResolver(LocaleResolver::class, LocaleResolver::class);
  }
  
  function testInvalidResolver() {
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
        "localeResolver" => "stdClass"
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidLocaleResolverException::class);
  }
  
  function testChainResolver() {
    $config = [
      "translation" => [
        "localeResolver" => [
          "fallback", "environment"
        ]
      ]
    ];
    $this->refreshContainer($config);
    putenv(EnvironmentLocaleResolver::VAR_NAME . "=cs");
    /** @var ChainLocaleResolver $resolver */
    $resolver = $this->getService(ILocaleResolver::class);
    Assert::type(ChainLocaleResolver::class, $resolver);
    Assert::same("cs", $resolver->resolve());
  }
  
  function testLoaderAwareResolver() {
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
  
  function testAppRequestAwareResolver() {
    /** @var Application $application */
    $application = $this->getService(Application::class);
    $count = count($application->onRequest);
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
  
  function testInvalidFolder() {
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
  
  function testPanel() {
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
  
  function testCompiler() {
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
  
  function testCompilerWithLanguages() {
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
    $result =$translator->translate("param", 0, ["param1" => "value1"]);
    Assert::same("Param2: value1", $result);
    $translator->lang = "xyz";
    Assert::same("Content", $translator->translate("book.content"));
    Assert::same("Test", $translator->translate("book.test"));
    Assert::same("ABC", $translator->translate("abc.multi.abc"));
    $result = $translator->translate("param", 0, ["param1" => "value1"]);
    Assert::same("Param1: value1", $result);
  }
  
  function testTranslationProvider() {
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
  
  function testLatte() {
    /** @var ILatteFactory $factory */
    $factory = $this->getService(ILatteFactory::class);
    $latte = $factory->create();
    Assert::contains("translate", $latte->getFilters());
    Assert::true(array_key_exists("translator", $latte->getProviders()));
  }
  
  static function onUntranslated(string $message): void {
    static::$messages[] = $message;
  }
  
  function testOnUntranslated() {
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

$test = new TranslationExtensionTest;
$test->run();
?>