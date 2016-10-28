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
    "folder" => "%appDir%/lang"
  ];
  
  /**
   * @return void
   */
  function loadConfiguration() {
    $config = $this->getConfig($this->defaults);
    Validators::assertField($config, "localeResolver", "string");
    Validators::assertField($config, "folder", "string");
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("translator"))
      ->setClass(\Nexendrie\Translation\Translator::class);
    $builder->addDefinition($this->prefix("loader"))
      ->setClass(\Nexendrie\Translation\Loader::class)
      ->addSetup("setFolder", [$config["folder"]]);
    $resolver = strtolower($config["localeResolver"]);
    switch($resolver) {
      case "environment":
        $builder->addDefinition($this->prefix("resolver"))
          ->setClass(\Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver::class);
        break;
      default:
        $builder->addDefinition($this->prefix("resolver"))
          ->setClass(\Nexendrie\Translation\Resolvers\ManualLocaleResolver::class);
        break;
    }
  }
}
?>