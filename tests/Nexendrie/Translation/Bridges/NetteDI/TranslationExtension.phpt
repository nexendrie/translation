<?php
namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\Localization\ITranslator,
    Nexendrie\Translation\Translator,
    Nexendrie\Translation\Loader,
    Nexendrie\Translation\Resolvers\ILocaleResolver,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver;

use Tester\Assert;

require __DIR__ . "/../../../../bootstrap.php";



/**
 * FallbackLocaleResolver
 *
 * @property string $defaultLang
 */
class FallbackLocaleResolver implements ILocaleResolver {
  use \Nette\SmartObject;
  
  protected $defaultLang = "en";
  
  /**
   * Resolve language
   *
   * @return string
   */
  function resolve() {
    return $this->defaultLang;
  }
  
  /**
   * @return string
   */
  function getDefaultLang() {
    return $this->defaultLang;
  }
  
  /**
   * Set default language
   *
   * @param string $default
   */
  function setDefaultLang($default) {
    $this->defaultLang = (string) $default;
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
    $loader = $this->getService(Loader::class);
    Assert::type(Loader::class, $loader);
    Assert::type("string", $loader->defaultLang);
    Assert::same("en", $loader->defaultLang);
    $config = [
      "translation" => [
        "default" => "cs"
      ]
    ];
    $this->refreshContainer($config);
    $loader = $this->getService(Loader::class);
    Assert::type(Loader::class, $loader);
    Assert::type("string", $loader->defaultLang);
    Assert::same("cs", $loader->defaultLang);
  }
  
  function testDefaultResolver() {
    /** @var ManualLocaleResolver $resolver */
    $resolver = $this->getService(ILocaleResolver::class);
    Assert::type(ManualLocaleResolver::class, $resolver);
    Assert::type("string", $resolver->defaultLang);
    Assert::same("en", $resolver->defaultLang);
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
    Assert::same("en", $resolver->defaultLang);
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
    Assert::same("en", $resolver->defaultLang);
  }
  
  function testInvalidResolver() {
    $config = [
      "translation" => [
        "localeResolver" => "invalid"
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, \Exception::class, "Invalid locale resolver.");
  }
}

$test = new TranslationExtensionTest;
$test->run();
?>