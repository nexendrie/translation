<?php
namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\DI\CompilerExtension,
    Nette\Utils\Validators,
    Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\Translator,
    Nexendrie\Translation\Loaders\NeonLoader,
    Nexendrie\Translation\InvalidLocaleResolverException,
    Nexendrie\Translation\InvalidFolderException,
    Nexendrie\Translation\InvalidLoaderException,
    Nexendrie\Translation\Bridges\Tracy\TranslationPanel,
    Nexendrie\Translation\Loaders\ILoader,
    Nexendrie\Translation\Resolvers\ILocaleResolver;

/**
 * TranslationExtension for Nette DI Container
 *
 * @author Jakub Konečný
 */
class TranslationExtension extends CompilerExtension {
  /** @var array */
  protected $defaults = [
    "localeResolver" => "manual",
    "folders" => [],
    "default" => "en",
    "debugger" => "%debugMode%",
    "loader" => "neon",
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
        if(class_exists($resolverName) AND in_array(ILocaleResolver::class, class_implements($resolverName))) {
          $resolver = $resolverName;
        } else {
          throw new InvalidLocaleResolverException("Invalid locale resolver.");
        }
        break;
    }
    return $resolver;
  }
  
  /**
   * @return string
   * @throws InvalidLoaderException
   */
  protected function resolveLoaderClass() {
    $config = $this->getConfig($this->defaults);
    $loaderName = $config["loader"];
    switch(strtolower($loaderName)) {
      case "neon":
        $loader = NeonLoader::class;
        break;
      default:
        if(class_exists($loaderName) AND in_array(ILoader::class, class_implements($loaderName))) {
          $loader = $loaderName;
        } else {
          throw new InvalidLoaderException("Invalid translation loader.");
        }
        break;
    }
    return $loader;
  }
  
  /**
   * @return void
   * @throws InvalidFolderException
   * @throws InvalidLocaleResolverException
   * @throws InvalidLoaderException
   */
  function loadConfiguration() {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig($this->defaults);
    Validators::assertField($config, "localeResolver", "string");
    try {
      $resolver = $this->resolveResolverClass();
    } catch(InvalidLocaleResolverException $e) {
      throw $e;
    }
    Validators::assertField($config, "folders", "array");
    if(!count($config["folders"])) {
      $config["folders"][] = $builder->expand("%appDir%/lang");
    }
    $folders = $config["folders"];
    foreach($folders as $folder) {
      if(!is_dir($folder)) {
        throw new InvalidFolderException("Folder $folder does not exist.");
      }
    }
    Validators::assertField($config, "loader", "string");
    try {
      $loader = $this->resolveLoaderClass();
    } catch(InvalidLoaderException $e) {
      throw $e;
    }
    Validators::assertField($config, "default", "string");
    $builder->addDefinition($this->prefix("translator"))
      ->setClass(Translator::class);
    $builder->addDefinition($this->prefix("loader"))
      ->setClass($loader)
      ->addSetup("setFolders", [$folders])
      ->addSetup("setDefaultLang", [$config["default"]]);
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