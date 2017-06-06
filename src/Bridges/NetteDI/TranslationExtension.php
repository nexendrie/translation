<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\DI\CompilerExtension,
    Nette\PhpGenerator\ClassType,
    Nette\Utils\Validators,
    Nexendrie\Translation\Resolvers\ILocaleResolver,
    Nexendrie\Translation\Resolvers\IAppRequestAwareLocaleResolver,
    Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver,
    Nexendrie\Translation\Resolvers\ManualLocaleResolver,
    Nexendrie\Translation\Resolvers\FallbackLocaleResolver,
    Nexendrie\Translation\Resolvers\ChainLocaleResolver,
    Nexendrie\Translation\Resolvers\SessionLocaleResolver,
    Nexendrie\Translation\Resolvers\HeaderLocaleResolver,
    Nexendrie\Translation\Bridges\NetteApplication\ParamLocaleResolver,
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
    Nexendrie\Translation\Bridges\Tracy\TranslationPanel,
    Nexendrie\Translation\CatalogueCompiler,
    Nette\Utils\Arrays,
    Nette\Application\Application,
    Nette\Bridges\ApplicationLatte\ILatteFactory;

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
    "session" => SessionLocaleResolver::class,
    "header" => HeaderLocaleResolver::class,
    "param" => ParamLocaleResolver::class
  ];
  
  /** @var string[] */
  protected $loaders = [
    "neon" => NeonLoader::class,
    "ini" => IniLoader::class,
    "json" => JsonLoader::class,
    "yaml" => YamlLoader::class,
    "php" => PhpLoader::class,
    "catalogue" => MessagesCatalogue::class,
  ];
  
  /**
   * @return string[]
   * @throws InvalidLocaleResolverException
   */
  protected function resolveResolverClass(): array {
    $config = $this->getConfig($this->defaults);
    $return = [];
    $resolvers = $config["localeResolver"];
    if(!is_array($resolvers)) {
      $resolvers = [$resolvers];
    }
    foreach($resolvers as $resolverName) {
      $resolver = Arrays::get($this->resolvers, strtolower($resolverName), "");
      if($resolver !== "") {
        $return[] = $resolver;
      } elseif(class_exists($resolverName) AND is_subclass_of($resolverName, ILocaleResolver::class)) {
        $return[] = $resolverName;
      } else {
        throw new InvalidLocaleResolverException("Invalid locale resolver $resolverName.");
      }
    }
    return $return;
  }
  
  /**
   * @return string
   * @throws InvalidLoaderException
   */
  protected function resolveLoaderClass(): string {
    $config = $this->getConfig($this->defaults);
    $loaderName = $config["loader"];
    $loader = Arrays::get($this->loaders, strtolower($loaderName), "");
    if($loader !== "") {
      return $loader;
    } elseif(class_exists($loaderName) AND is_subclass_of($loaderName, ILoader::class)) {
      return $loaderName;
    } else {
      throw new InvalidLoaderException("Invalid translation loader.");
    }
  }
  
  /**
   * @return string[]
   * @throws InvalidFolderException
   */
  protected function getFolders(): array {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig($this->defaults);
    if(!count($config["folders"])) {
      $config["folders"][] = $builder->expand("%appDir%/lang");
    }
    $folders = $config["folders"];
    /** @var ITranslationProvider $extension */
    foreach($this->compiler->getExtensions(ITranslationProvider::class) as $extension) {
      $folders = array_merge($folders, array_values($extension->getTranslationResources()));
    }
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
   */
  function loadConfiguration(): void {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig($this->defaults);
    Validators::assertField($config, "localeResolver", "string|array");
    try {
      $resolvers = $this->resolveResolverClass();
    } catch(InvalidLocaleResolverException $e) {
      throw $e;
    }
    Validators::assertField($config, "folders", "array");
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
      ->addSetup("setDefaultLang", [$config["default"]]);
    if(count($resolvers) === 1) {
      $builder->addDefinition($this->prefix("localeResolver"))
        ->setClass($resolvers[0]);
    } else {
      $chainResolver = $builder->addDefinition($this->prefix("localeResolver"))
        ->setClass(ChainLocaleResolver::class);
      foreach($resolvers as $index => $resolver) {
        $resolverService = $builder->addDefinition($this->prefix("resolver.$index"))
          ->setClass($resolver)
          ->setAutowired(false);
        $chainResolver->addSetup("addResolver", [$resolverService]);
      }
    }
    if($config["debugger"] AND interface_exists('Tracy\IBarPanel')) {
      $builder->addDefinition($this->prefix("panel"))
        ->setClass(TranslationPanel::class);
      $builder->getDefinition("tracy.bar")
        ->addSetup("addPanel", ["@" . $this->prefix("panel"), "translation"]);
    }
    Validators::assertField($config["compiler"], "enabled", "bool");
    Validators::assertField($config["compiler"], "languages", "array");
  }
  
  /**
   * @return void
   */
  function beforeCompile(): void {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig($this->defaults);
    try {
      $folders = $this->getFolders();
    } catch(InvalidFolderException $e) {
      throw $e;
    }
    $loader = $builder->getDefinition($this->prefix("loader"));
    if(in_array(FileLoader::class, class_parents($loader->class))) {
      $loader->addSetup("setFolders", [$folders]);
    }
    $resolver = $builder->getDefinition($this->prefix("localeResolver"));
    if(in_array(IAppRequestAwareLocaleResolver::class, class_implements($resolver->class))) {
      $applicationService = $builder->getByType(Application::class) ?? "application";
      if($builder->hasDefinition($applicationService)) {
        $builder->getDefinition($applicationService)
          ->addSetup('$service->onRequest[] = ?', [[$resolver, "onRequest"]]);
      }
    }
    if($config["compiler"]["enabled"]) {
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
        ->setFactory(CatalogueCompiler::class, [$loader, $folder, $config["compiler"]["languages"]]);
    }
    $latteFactoryService = $builder->getByType(ILatteFactory::class) ?? "latte.latteFactory";
    if($builder->hasDefinition($latteFactoryService)) {
      $latteFactory = $builder->getDefinition($latteFactoryService);
      $latteFactory->addSetup("addFilter", ["translate", ["@" . $this->prefix("translator"), "translate"]]);
    }
  }
  
  /**
   * @param ClassType $class
   * @return void
   */
  function afterCompile(ClassType $class): void {
    $config = $this->getConfig($this->defaults);
    $initialize = $class->methods["initialize"];
    $initialize->addBody('$resolver = $this->getService(?);
if($resolver instanceof Nexendrie\Translation\Resolvers\ILoaderAwareLocaleResolver) $resolver->setLoader($this->getService(?));', [$this->prefix("localeResolver"), $this->prefix("loader")]);
    if($config["compiler"]["enabled"]) {
      $initialize->addBody('$this->getService(?)->compile();', [$this->prefix("catalogueCompiler")]);
    }
  }
}
?>