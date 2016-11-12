<?php
namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\DI\CompilerExtension,
    Nette\Utils\Validators,
    Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\Translator,
    Nexendrie\Translation\Loaders\ILoader,
    Nexendrie\Translation\Loaders\FileLoader,
    Nexendrie\Translation\Loaders\NeonLoader,
    Nexendrie\Translation\Loaders\IniLoader,
    Nexendrie\Translation\Loaders\JsonLoader,
    Nexendrie\Translation\InvalidLocaleResolverException,
    Nexendrie\Translation\InvalidFolderException,
    Nexendrie\Translation\InvalidLoaderException,
    Nexendrie\Translation\Bridges\Tracy\TranslationPanel,
    Nexendrie\Translation\Resolvers\ILocaleResolver,
    Nette\Utils\Arrays;

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
  
  /** @var string[] */
  protected $loaders = [
    "neon" => NeonLoader::class,
    "ini" => IniLoader::class,
    "json" => JsonLoader::class,
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
    $loader = Arrays::get($this->loaders, strtolower($loaderName), "");
    if($loader !== "") {
      return $loader;
    } elseif(class_exists($loaderName) AND in_array(ILoader::class, class_implements($loaderName))) {
      return $loaderName;
    } else {
      throw new InvalidLoaderException("Invalid translation loader.");
    }
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
    $loader = $builder->addDefinition($this->prefix("loader"))
      ->setClass($loader)
      ->addSetup("setDefaultLang", [$config["default"]]);
    if(in_array(FileLoader::class, class_parents($loader->class))) {
      $loader->addSetup("setFolders", [$folders]);
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