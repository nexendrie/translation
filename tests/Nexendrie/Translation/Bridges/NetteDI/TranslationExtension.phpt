<?php
namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\Localization\ITranslator,
    Nexendrie\Translation\Translator,
    Nexendrie\Translation\ILoader,
    Nexendrie\Translation\Loader,
    Nexendrie\Translation\Resolvers\ILocaleResolver,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver,
    Nexendrie\Translation\InvalidLocaleResolverException,
    Nexendrie\Translation\InvalidFolderException,
    Nexendrie\Translation\Bridges\Tracy\TranslationPanel,
    Nette\DI\MissingServiceException;

use Tester\Assert;

require __DIR__ . "/../../../../bootstrap.php";

/**
 * FallbackLocaleResolver
 */
class FallbackLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  /**
   * Resolve language
   *
   * @return NULL
   */
  function resolve() {
    return NULL;
  }
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
  
  function testLoader() {
    /** @var ILoader $loader */
    $loader = $this->getService(ILoader::class);
    Assert::type(Loader::class, $loader);
    Assert::type("string", $loader->getDefaultLang());
    Assert::same("en", $loader->getDefaultLang());
    $config = [
      "translation" => [
        "default" => "cs"
      ]
    ];
    $this->refreshContainer($config);
    $loader = $this->getService(ILoader::class);
    Assert::type(Loader::class, $loader);
    Assert::type("string", $loader->getDefaultLang());
    Assert::same("cs", $loader->getDefaultLang());
  }
  
  function testDefaultResolver() {
    /** @var ManualLocaleResolver $resolver */
    $resolver = $this->getService(ILocaleResolver::class);
    Assert::type(ManualLocaleResolver::class, $resolver);
    Assert::type("null", $resolver->lang);
  }
  
  function testOtherResolver() {
    $config = [
      "translation" => [
        "localeResolver" => "environment"
      ]
    ];
    $this->refreshContainer($config);
    /** @var EnvironmentLocaleResolver $resolver */
    $resolver = $this->getService(ILocaleResolver::class);
    Assert::type(EnvironmentLocaleResolver::class, $resolver);
  }
  
  function testCustomResolver() {
    $config = [
      "translation" => [
        "localeResolver" => FallbackLocaleResolver::class
      ]
    ];
    $this->refreshContainer($config);
    /** @var FallbackLocaleResolver $resolver */
    $resolver = $this->getService(ILocaleResolver::class);
    Assert::type(FallbackLocaleResolver::class, $resolver);
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
}

$test = new TranslationExtensionTest;
$test->run();
?>