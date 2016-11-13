<?php
namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\Localization\ITranslator,
    Nexendrie\Translation\Translator,
    Nexendrie\Translation\Loaders\ILoader,
    Nexendrie\Translation\Loaders\NeonLoader,
    Nexendrie\Translation\Loaders\IniLoader,
    Nexendrie\Translation\Loaders\JsonLoader,
    Nexendrie\Translation\Loaders\YamlLoader,
    Nexendrie\Translation\Loaders\MessagesCatalogue,
    Nexendrie\Translation\Resolvers\ILocaleResolver,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver,
    Nexendrie\Translation\Resolvers\FallbackLocaleResolver,
    Nexendrie\Translation\CatalogueCompiler,
    Nexendrie\Translation\InvalidLocaleResolverException,
    Nexendrie\Translation\InvalidFolderException,
    Nexendrie\Translation\InvalidLoaderException,
    Nexendrie\Translation\Bridges\Tracy\TranslationPanel,
    Nette\DI\MissingServiceException,
    Nexendrie\Translation\NoLanguageSpecifiedException,
    Tester\Assert;

require __DIR__ . "/../../../../bootstrap.php";

class Loader extends NeonLoader {
  
}

class TranslationExtensionTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
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
    /** @var ILoader $loader */
    $loader = $this->getService(ILoader::class);
    Assert::type(NeonLoader::class, $loader);
    Assert::type("string", $loader->getDefaultLang());
    Assert::same("en", $loader->getDefaultLang());
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
  protected function customLoader($name, $class) {
    $config = [
      "translation" => [
        "loader" => $name
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
    $this->customLoader("catalogue", MessagesCatalogue::class);
    $this->customLoader(Loader::class, Loader::class);
  }
  
  function testInvalidLoader() {
    $config = [
      "translation" => [
        "loader" => "invalid"
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidLoaderException::class, "Invalid translation loader.");
    $config = [
      "translation" => [
        "loader" => "stdClass"
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidLoaderException::class, "Invalid translation loader.");
  }
  
  function testDefaultResolver() {
    /** @var ManualLocaleResolver $resolver */
    $resolver = $this->getService(ILocaleResolver::class);
    Assert::type(ManualLocaleResolver::class, $resolver);
    Assert::type("null", $resolver->lang);
  }
  
  /**
   * @param string $name
   * @param string $class
   * @return void
   */
  protected function customResolver($name, $class) {
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
    $this->customResolver(FallbackLocaleResolver::class, FallbackLocaleResolver::class);
  }
  
  function testInvalidResolver() {
    $config = [
      "translation" => [
        "localeResolver" => "invalid"
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidLocaleResolverException::class, "Invalid locale resolver.");
    $config = [
      "translation" => [
        "localeResolver" => "stdClass"
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidLocaleResolverException::class, "Invalid locale resolver.");
  }
  
  function testInvalidFolder() {
    $config = [
      "translation" => [
        "folders" => ["/dev/null"]
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidFolderException::class, "Folder /dev/null does not exist.");
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
        "compiler" => [
          "enabled" => true,
          "languages" => [],
        ],
        "folders" => [
          "%appDir%/lang", "%appDir%/lang2"
        ]
      ]
    ];
    Assert::exception(function() use($config){
      $this->refreshContainer($config);
    }, NoLanguageSpecifiedException::class);
    $config["translation"]["compiler"]["languages"] = ["en", "cs", "xyz"];
    $this->refreshContainer($config);
    $loader = $this->getService(ILoader::class);
    Assert::type(MessagesCatalogue::class, $loader);
    /** @var NeonLoader $originalLoader */
    $originalLoader = $this->getContainer()->getService("translation.originalLoader");
    Assert::type(NeonLoader::class, $originalLoader);
    $compiler =  $this->getService(CatalogueCompiler::class);
    Assert::type(CatalogueCompiler::class, $compiler);
    /** @var Translator $translator */
    $translator = $this->getService(ITranslator::class);
    Assert::same("Content", $translator->translate("book.content"));
    Assert::same("Test", $translator->translate("book.test"));
    Assert::same("ABC", $translator->translate("abc.multi.abc"));
    Assert::same("Param1: value1", $translator->translate("param", 0, ["param1" => "value1"]));
    $translator->lang = "cs";
    Assert::same("Obsah", $translator->translate("book.content"));
    Assert::same("Test", $translator->translate("book.test"));
    Assert::same("Abc", $translator->translate("abc.multi.abc"));
    Assert::same("Param2: value1", $translator->translate("param", 0, ["param1" => "value1"]));
    $translator->lang = "xyz";
    Assert::same("Content", $translator->translate("book.content"));
    Assert::same("Test", $translator->translate("book.test"));
    Assert::same("ABC", $translator->translate("abc.multi.abc"));
    Assert::same("Param1: value1", $translator->translate("param", 0, ["param1" => "value1"]));
  }
}

$test = new TranslationExtensionTest;
$test->run();
?>