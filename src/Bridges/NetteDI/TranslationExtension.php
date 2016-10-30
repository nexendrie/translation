<?php
namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\DI\CompilerExtension,
    Nette\Utils\Validators,
    Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\Translator,
    Nexendrie\Translation\Loader,
    Nexendrie\Translation\InvalidLocaleResolverException,
    Nexendrie\Translation\InvalidFolderException;

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
   * @throws InvalidFolderException
   * @throws InvalidLocaleResolverException
   */
  function loadConfiguration() {
    $config = $this->getConfig($this->defaults);
    Validators::assertField($config, "localeResolver", "string");
    Validators::assertField($config, "folder", "string");
    $folder = $config["folder"];
    if(!is_dir($folder)) {
      throw new InvalidFolderException("Folder $folder does not exist.");
    }
    Validators::assertField($config, "default", "string");
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("translator"))
      ->setClass(Translator::class);
    $builder->addDefinition($this->prefix("loader"))
      ->setClass(Loader::class)
      ->addSetup("setFolder", [$folder])
      ->addSetup("setDefaultLang", [$config["default"]]);
    $resolverName = $config["localeResolver"];
    switch(strtolower($resolverName)) {
      case "environment":
        $resolver = EnvironmentLocaleResolver::class;
        break;
      case "manual":
        $resolver = ManualLocaleResolver::class;
        break;
      default:
        if(class_exists($resolverName)) {
          $resolver = $resolverName;
        } else {
          throw new InvalidLocaleResolverException("Invalid locale resolver.");
        }
        break;
    }
    $builder->addDefinition($this->prefix("localeResolver"))
      ->setClass($resolver);
  }
}
?>