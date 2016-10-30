<?php
namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\DI\CompilerExtension,
  Nette\Utils\Validators,
  Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver,
  Nexendrie\Translation\Resolvers\ManualLocaleResolver,
  Nexendrie\Translation\Translator,
  Nexendrie\Translation\Loader,
  Nexendrie\Translation\InvalidLocaleResolverException,
  Nexendrie\Translation\InvalidFolderException,
  Nexendrie\Translation\Bridges\Tracy\TranslationPanel;

/**
 * TranslationExtension for Nette DI Container
 *
 * @author Jakub Konečný
 */
class TranslationExtension extends CompilerExtension {
  /** @var array */
  protected $defaults = [
    "localeResolver" => "manual",
    "folders" => ["%appDir%/lang"],
    "default" => "en",
    "debugger" => "%debugMode%",
  ];
  
  /**
   * @return string
   * @throws InvalidLocaleResolverException
   */
  protected function resolveResolverClass() {
    $config = $this->getConfig($this->defaults);
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
    return $resolver;
  }
  
  /**
   * @return void
   * @throws \InvalidArgumentException
   * @throws InvalidFolderException
   * @throws InvalidLocaleResolverException
   */
  function loadConfiguration() {
    $config = $this->getConfig($this->defaults);
    Validators::assertField($config, "localeResolver", "string");
    Validators::assertField($config, "folders");
    if(is_string($config["folders"])) {
      $folders = [$config["folders"]];
    } elseif(is_array($config["folders"])) {
      $folders = $config["folders"];
    } else {
      throw new \InvalidArgumentException("$this->name.folders has to be string or array of strings.");
    }
    foreach($folders as $folder) {
      if(!is_dir($folder)) {
        throw new InvalidFolderException("Folder $folder does not exist.");
      }
    }
    Validators::assertField($config, "default", "string");
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("translator"))
      ->setClass(Translator::class);
    $builder->addDefinition($this->prefix("loader"))
      ->setClass(Loader::class)
      ->addSetup("setFolders", [$folders])
      ->addSetup("setDefaultLang", [$config["default"]]);
    try {
      $resolver = $this->resolveResolverClass();
    } catch(InvalidLocaleResolverException $e) {
      throw $e;
    }
    $builder->addDefinition($this->prefix("localeResolver"))
      ->setClass($resolver);
    if($config["debugger"] AND interface_exists('Tracy\IBarPanel')) {
      $builder->addDefinition($this->prefix("panel"))
        ->setClass(TranslationPanel::class);
      $builder->getDefinition("tracy.bar")
        ->addSetup("addPanel", ["@" . $this->prefix("panel"), "translation"]);
    }
  }
}
?>