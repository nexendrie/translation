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


class TranslationExtensionTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  function testTranslator() {
    /** @var Translator $translator */
    $translator = $this->getService(ITranslator::class);
    Assert::type(Translator::class, $translator);
    Assert::same("XYZ", $translator->translate("xyz"));
  }
  
  function testLoader() {
    $loader = $this->getService(Loader::class);
    Assert::type(Loader::class, $loader);
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
}

$test = new TranslationExtensionTest;
$test->run();
?>