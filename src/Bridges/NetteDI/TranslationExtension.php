<?php
namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\DI\CompilerExtension,
    Nette\Utils\Validators;

/**
 * TranslationExtension for Nette DI Container
 *
 * @author Jakub Konečný
 */
class TranslationExtension extends CompilerExtension {
  /** @var array */
  protected $defaults = [
    "localeResolver" => "manual",
    "folder" => "%appDir%/lang",
    "default" => "en",
  ];
  
  /**
   * @return void
   */
  function loadConfiguration() {
    $config = $this->getConfig($this->defaults);
    Validators::assertField($config, "localeResolver", "string");
    Validators::assertField($config, "folder", "string");
    Validators::assertField($config, "default", "string");
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("translator"))
      ->setClass(\Nexendrie\Translation\Translator::class);
    $builder->addDefinition($this->prefix("loader"))
      ->setClass(\Nexendrie\Translation\Loader::class)
      ->addSetup("setFolder", [$config["folder"]])
      ->addSetup("setDefaultLang", [$config["default"]]);
    $resolverName = strtolower($config["localeResolver"]);
    switch($resolverName) {
      case "environment":
        $resolver = $builder->addDefinition($this->prefix("resolverName"))
          ->setClass(\Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver::class);
        break;
      default:
        $resolver = $builder->addDefinition($this->prefix("resolverName"))
          ->setClass(\Nexendrie\Translation\Resolvers\ManualLocaleResolver::class);
        break;
    }
    $resolver->addSetup("setDefaultLang", [$config["default"]]);
  }
}
?>