<?php
namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\DI\CompilerExtension,
    Nette\PhpGenerator\ClassType,
    Nette\Utils\Validators,
    Nexendrie\Translation\Resolvers\ILocaleResolver,
    Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\Resolvers\FallbackLocaleResolver,
    Nexendrie\Translation\Translator,
    Nexendrie\Translation\Loaders\ILoader,
    Nexendrie\Translation\Loaders\FileLoader,
    Nexendrie\Translation\Loaders\NeonLoader,
    Nexendrie\Translation\Loaders\IniLoader,
    Nexendrie\Translation\Loaders\JsonLoader,
    Nexendrie\Translation\Loaders\YamlLoader,
    Nexendrie\Translation\Loaders\PhpLoader,
    Nexendrie\Translation\Loaders\MessagesCatalogue,
    Nexendrie\Translation\InvalidLocaleResolverException,
    Nexendrie\Translation\InvalidFolderException,
    Nexendrie\Translation\InvalidLoaderException,
    Nexendrie\Translation\NoLanguageSpecifiedException,
    Nexendrie\Translation\Bridges\Tracy\TranslationPanel,
    Nexendrie\Translation\CatalogueCompiler,
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
    "compiler" => [
      "enabled" => false,
      "languages" => [],
    ],
  ];
  
  /** @var string[] */
  protected $resolvers = [
    "environment" => EnvironmentLocaleResolver::class,
    "manual" => ManualLocaleResolver::class,
    "fallback" => FallbackLocaleResolver::class,
  ];
  
  /** @var string[] */
  protected $loaders = [
    "neon" => NeonLoader::class,
    "ini" => IniLoader::class,
    "json" => JsonLoader::class,
    "yaml" => YamlLoader::class,
    "php" => PhpLoader::class,
    "catalogue" => MessagesCatalogue::class
  ];
  
  /**
   * @return string
   * @throws InvalidLocaleResolverException
   */
  protected function resolveResolverClass() {
    $config = $this->getConfig($this->defaults);
    $resolverName = $config["localeResolver"];
    $resolver = Arrays::get($this->resolvers, strtolower($resolverName), "");
    if($resolver !== "") {
      return $resolver;
    } elseif(class_exists($resolverName) AND in_array(ILocaleResolver::class, class_implements($resolverName))) {
      return $resolverName;
    } else {
      throw new InvalidLocaleResolverException("Invalid locale resolver.");
    }
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
   * @return string[]
   * @throws InvalidFolderException
   */
  protected function getFolders() {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig($this->defaults);
    if(!count($config["folders"])) {
      $config["folders"][] = $builder->expand("%appDir%/lang");
    }
    $folders = $config["folders"];
    foreach($folders as $folder) {
      if(!is_dir($folder)) {
        throw new InvalidFolderException("Folder $folder does not exist.");
      }
    }
    return $folders;
  }
  
  /**
   * @return void
   * @throws InvalidFolderException
   * @throws InvalidLocaleResolverException
   * @throws InvalidLoaderException
   * @throws NoLanguageSpecifiedException
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
    try {
      $folders = $this->getFolders();
    } catch(InvalidFolderException $e) {
      throw $e;
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
    Validators::assertField($config["compiler"], "enabled", "bool");
    Validators::assertField($config["compiler"], "languages", "array");
    if($config["compiler"]["enabled"] AND count($config["compiler"]["languages"]) < 1) {
      throw new NoLanguageSpecifiedException("Specify at least 1 language for catalogue compiler or disable the compiler.");
    }
  }
  
  /**
   * @return void
   */
  function beforeCompile() {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig($this->defaults);
    if(!$config["compiler"]["enabled"]) {
      return;
    }
    $serviceName = $this->prefix("loader");
    $loader = $builder->getDefinition($serviceName);
    $builder->removeDefinition($serviceName);
    $folder = $builder->expand("%tempDir%/catalogues");
    $builder->addDefinition($this->prefix("originalLoader"), $loader)
      ->setFactory($loader->class, [new ManualLocaleResolver, $config["folders"]])
      ->setAutowired(false);
    $builder->addDefinition($serviceName)
      ->setClass(MessagesCatalogue::class)
      ->addSetup("setFolders", [[$folder]]);
    $builder->addDefinition($this->prefix("catalogueCompiler"))
      ->setFactory(CatalogueCompiler::class, [$loader, $config["compiler"]["languages"], $folder]);
  }
  
  /**
   * @param ClassType $class
   * @return void
   */
  function afterCompile(ClassType $class) {
    $config = $this->getConfig($this->defaults);
    if(!$config["compiler"]["enabled"]) {
      return;
    }
    $initialize = $class->methods["initialize"];
    $initialize->addBody('$this->getService(?)->compile();', [$this->prefix("catalogueCompiler")]);
  }
}
?>