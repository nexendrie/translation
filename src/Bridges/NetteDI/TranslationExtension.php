<?php
namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\DI\CompilerExtension,
    Nette\Utils\Validators,
    Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\Translator,
    Nexendrie\Translation\Loader,
    Nexendrie\Translation\InvalidLocaleResolverException;

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
   * @throws \Exception
   */
  function loadConfiguration() {
    $config = $this->getConfig($this->defaults);
    Validators::assertField($config, "localeResolver", "string");
    Validators::assertField($config, "folder", "string");
    Validators::assertField($config, "default", "string");
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("translator"))
      ->setClass(Translator::class);
    $builder->addDefinition($this->prefix("loader"))
      ->setClass(Loader::class)
      ->addSetup("setFolder", [$config["folder"]])
      ->addSetup("setDefaultLang", [$config["default"]]);
    $resolverName = $config["localeResolver"];
    switch(strtolower($resolverName)) {
      case "environment":
        $builder->addDefinition($this->prefix("resolverName"))
          ->setClass(EnvironmentLocaleResolver::class);
        break;
      case "manual":
        $builder->addDefinition($this->prefix("resolverName"))
          ->setClass(ManualLocaleResolver::class);
        break;
      default:
        if(class_exists($resolverName)) {
          $builder->addDefinition($this->prefix("resolverName"))
            ->setClass($resolverName);
        } else {
          throw new InvalidLocaleResolverException("Invalid locale resolver.");
        }
        break;
    }
  }
}
?>