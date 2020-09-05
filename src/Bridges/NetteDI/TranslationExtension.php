<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\NetteDI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\PhpGenerator\ClassType;
use Nexendrie\Translation\ILocaleResolver;
use Nexendrie\Translation\Bridges\NetteApplication\IAppRequestAwareLocaleResolver;
use Nexendrie\Translation\Resolvers\EnvironmentLocaleResolver;
use Nexendrie\Translation\Resolvers\ManualLocaleResolver;
use Nexendrie\Translation\Resolvers\FallbackLocaleResolver;
use Nexendrie\Translation\Resolvers\ChainLocaleResolver;
use Nexendrie\Translation\Resolvers\SessionLocaleResolver;
use Nexendrie\Translation\Resolvers\HeaderLocaleResolver;
use Nexendrie\Translation\Bridges\NetteApplication\ParamLocaleResolver;
use Nexendrie\Translation\Translator;
use Nexendrie\Translation\ILoader;
use Nexendrie\Translation\IFileLoader;
use Nexendrie\Translation\Loaders\NeonLoader;
use Nexendrie\Translation\Loaders\IniLoader;
use Nexendrie\Translation\Loaders\JsonLoader;
use Nexendrie\Translation\Loaders\YamlLoader;
use Nexendrie\Translation\Loaders\PhpLoader;
use Nexendrie\Translation\Loaders\MessagesCatalogue;
use Nexendrie\Translation\InvalidLocaleResolverException;
use Nexendrie\Translation\InvalidFolderException;
use Nexendrie\Translation\InvalidLoaderException;
use Nexendrie\Translation\Bridges\Tracy\TranslationPanel;
use Nexendrie\Translation\CatalogueCompiler;
use Nette\Utils\Arrays;
use Nette\Application\Application;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nexendrie\Translation\ILoaderAwareLocaleResolver;
use Nexendrie\Translation\IMessageSelector;
use Nexendrie\Translation\MessageSelector;
use Nexendrie\Translation\InvalidMessageSelectorException;
use Nette\Schema\Expect;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Helpers;

/**
 * TranslationExtension for Nette DI Container
 *
 * @author Jakub Konečný
 * @method \stdClass getConfig()
 */
final class TranslationExtension extends CompilerExtension {
  /** @internal */
  public const SERVICE_TRANSLATOR = "translator";
  /** @internal */
  public const SERVICE_LOADER = "loader";
  /** @internal */
  public const SERVICE_LOCALE_RESOLVER = "localeResolver";
  /** @internal */
  public const SERVICE_PANEL = "panel";
  /** @internal */
  public const SERVICE_CATALOGUE_COMPILER = "catalogueCompiler";
  /** @internal */
  public const SERVICE_ORIGINAL_LOADER = "originalLoader";
  /** @internal */
  public const SERVICE_MESSAGE_SELECTOR = "messageSelector";

  protected array $resolvers = [
    "environment" => EnvironmentLocaleResolver::class,
    "manual" => ManualLocaleResolver::class,
    "fallback" => FallbackLocaleResolver::class,
    "session" => SessionLocaleResolver::class,
    "header" => HeaderLocaleResolver::class,
    "param" => ParamLocaleResolver::class
  ];

  protected array $loaders = [
    "neon" => NeonLoader::class,
    "ini" => IniLoader::class,
    "json" => JsonLoader::class,
    "yaml" => YamlLoader::class,
    "php" => PhpLoader::class,
    "catalogue" => MessagesCatalogue::class,
  ];

  public function getConfigSchema(): \Nette\Schema\Schema {
    $params = $this->getContainerBuilder()->parameters;
    return Expect::structure([
      "localeResolver" => Expect::anyOf(Expect::string(), Expect::arrayOf("string"))->default(["param", "session", "header", ]),
      "default" => Expect::string("en"),
      "debugger" => Expect::bool(Helpers::expand("%debugMode%", $params)),
      "loader" => Expect::structure([
        "name" => Expect::string("neon"),
        "folders" => Expect::array()->default([Helpers::expand("%appDir%/lang", $params)]),
      ])->castTo("array"),
      "onUntranslated" => Expect::array()->default([
        ["@" . $this->prefix(static::SERVICE_TRANSLATOR), "logUntranslatedMessage"]
      ]),
      "compiler" => Expect::structure([
        "enabled" => Expect::bool(false),
        "languages" => Expect::arrayOf("string")->default([]),
      ])->castTo("array"),
      "messageSelector" => Expect::type("class")->default(MessageSelector::class),
    ]);
  }

  /**
   * @return string[]
   * @throws InvalidLocaleResolverException
   */
  protected function resolveResolverClass(): array {
    $config = $this->getConfig();
    $return = [];
    $resolvers = $config->localeResolver;
    if(!is_array($resolvers)) {
      $resolvers = [$resolvers];
    }
    foreach($resolvers as $resolverName) {
      $resolver = Arrays::get($this->resolvers, strtolower($resolverName), "");
      if($resolver !== "") {
        $return[] = $resolver;
      } elseif(class_exists($resolverName) && is_subclass_of($resolverName, ILocaleResolver::class)) {
        $return[] = $resolverName;
      } else {
        throw new InvalidLocaleResolverException("Invalid locale resolver $resolverName.");
      }
    }
    return $return;
  }
  
  /**
   * @throws InvalidLoaderException
   */
  protected function resolveLoaderClass(): string {
    $config = $this->getConfig();
    /** @var string $loaderName */
    $loaderName = $config->loader["name"];
    $loader = Arrays::get($this->loaders, strtolower($loaderName), "");
    if($loader !== "") {
      return $loader;
    } elseif(class_exists($loaderName) && is_subclass_of($loaderName, ILoader::class)) {
      return $loaderName;
    }
    throw new InvalidLoaderException("Invalid translation loader $loaderName.");
  }
  
  /**
   * @return string[]
   * @throws InvalidFolderException
   */
  protected function getFolders(): array {
    $config = $this->getConfig();
    $folders = $config->loader["folders"];
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
   * @throws InvalidMessageSelectorException
   */
  protected function resolveMessageSelector(): string {
    $config = $this->getConfig();
    /** @var string $messageSelector */
    $messageSelector = $config->messageSelector;
    if(class_exists($messageSelector) && is_subclass_of($messageSelector, IMessageSelector::class)) {
      return $messageSelector;
    }
    throw new InvalidMessageSelectorException("Invalid message selector $messageSelector.");
  }
  
  /**
   * @throws InvalidLocaleResolverException
   * @throws InvalidLoaderException
   * @throws InvalidMessageSelectorException
   */
  public function loadConfiguration(): void {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig();
    $resolvers = $this->resolveResolverClass();
    $loader = $this->resolveLoaderClass();
    $builder->addDefinition($this->prefix(static::SERVICE_TRANSLATOR))
      ->setType(Translator::class);
    $builder->addDefinition($this->prefix(static::SERVICE_LOADER))
      ->setType($loader)
      ->addSetup("setDefaultLang", [$config->default]);
    $messageSelector = $this->resolveMessageSelector();
    $builder->addDefinition($this->prefix(static::SERVICE_MESSAGE_SELECTOR))
      ->setType($messageSelector);
    if(count($resolvers) === 1) {
      $builder->addDefinition($this->prefix(static::SERVICE_LOCALE_RESOLVER))
        ->setType($resolvers[0]);
    } else {
      $chainResolver = $builder->addDefinition($this->prefix(static::SERVICE_LOCALE_RESOLVER))
        ->setType(ChainLocaleResolver::class);
      foreach($resolvers as $index => $resolver) {
        $resolverService = $builder->addDefinition($this->prefix("resolver.$index"))
          ->setType($resolver)
          ->setAutowired(false);
        $chainResolver->addSetup('$service[] = ?', [$resolverService]);
      }
    }
    if($config->debugger && interface_exists(\Tracy\IBarPanel::class)) {
      $builder->addDefinition($this->prefix(static::SERVICE_PANEL))
        ->setType(TranslationPanel::class);
      /** @var ServiceDefinition $tracy */
      $tracy = $builder->getDefinition("tracy.bar");
      $tracy->addSetup("addPanel", ["@" . $this->prefix(static::SERVICE_PANEL), "translation"]);
    }
  }
  
  /**
   * @throws InvalidFolderException
   */
  public function beforeCompile(): void {
    $builder = $this->getContainerBuilder();
    $config = $this->getConfig();
    /** @var ServiceDefinition $loader */
    $loader = $builder->getDefinition($this->prefix(static::SERVICE_LOADER));
    if(in_array(IFileLoader::class, class_implements((string) $loader->class), true)) {
      $folders = $this->getFolders();
      $loader->addSetup("setFolders", [$folders]);
      foreach($folders as $folder) {
        $builder->addDependency($folder);
      }
    }
    /** @var ServiceDefinition $resolver */
    $resolver = $builder->getDefinition($this->prefix(static::SERVICE_LOCALE_RESOLVER));
    if(in_array(IAppRequestAwareLocaleResolver::class, class_implements((string) $resolver->class), true)) {
      $applicationService = $builder->getByType(Application::class) ?? "application";
      if($builder->hasDefinition($applicationService)) {
        /** @var ServiceDefinition $application */
        $application = $builder->getDefinition($applicationService);
        $application->addSetup('$service->onRequest[] = ?', [[$resolver, "onRequest"]]);
      }
    }
    if($config->compiler["enabled"]) {
      $serviceName = $this->prefix(static::SERVICE_LOADER);
      /** @var ServiceDefinition $loader */
      $loader = $builder->getDefinition($serviceName);
      $builder->addDefinition($this->prefix(static::SERVICE_ORIGINAL_LOADER))
        ->setFactory((string) $loader->class, [new ManualLocaleResolver(), $config->loader["folders"]])
        ->addSetup("setDefaultLang", [$config->default])
        ->setAutowired(false);
      $folder = Helpers::expand("%tempDir%/catalogues", $builder->parameters);
      $loader->setFactory(MessagesCatalogue::class);
      $loader->setType(MessagesCatalogue::class);
      $loader->addSetup("setFolders", [[$folder]]);
      $builder->addDefinition($this->prefix(static::SERVICE_CATALOGUE_COMPILER))
        ->setFactory(CatalogueCompiler::class, [$loader, $folder, $config->compiler["languages"]]);
    }
    $latteFactoryService = $builder->getByType(ILatteFactory::class) ?? "latte.latteFactory";
    if($builder->hasDefinition($latteFactoryService)) {
      /** @var FactoryDefinition $latteFactory */
      $latteFactory = $builder->getDefinition($latteFactoryService);
      $latteFactory->getResultDefinition()->addSetup("addFilter", ["translate", ["@" . $this->prefix(static::SERVICE_TRANSLATOR), "translate"]]);
      $latteFactory->getResultDefinition()->addSetup("addProvider", ["translator", "@" . $this->prefix(static::SERVICE_TRANSLATOR)]);
    }
  }
  
  public function afterCompile(ClassType $class): void {
    $config = $this->getConfig();
    $initialize = $class->methods["initialize"];
    $initialize->addBody('$translator = $this->getService(?);', [$this->prefix(static::SERVICE_TRANSLATOR)]);
    foreach($config->onUntranslated as &$task) {
      if(!is_array($task)) {
        $task = explode("::", $task);
      } elseif(substr($task[0], 0, 1) === "@") {
        $initialize->addBody('$translator->onUntranslated[] = [$this->getService(?), ?];', [substr($task[0], 1), $task[1]]);
        continue;
      }
      $initialize->addBody('$translator->onUntranslated[] = [?, ?];', [$task[0], $task[1]]);
    }
    $initialize->addBody('$resolvers = $this->findByType(?);
foreach($resolvers as $resolver) $this->getService($resolver)->setLoader($this->getService(?));', [ILoaderAwareLocaleResolver::class, $this->prefix(static::SERVICE_LOADER)]);
    if($config->compiler["enabled"]) {
      $initialize->addBody('$this->getService(?)->compile();', [$this->prefix(static::SERVICE_CATALOGUE_COMPILER)]);
    }
  }
}
?>