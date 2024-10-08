Version 2.4.0-dev
- raised minimal version of PHP to 8.0
- added support for symfony/yaml 6

Version 2.3.2
- fixed compatibility with nette/php-generator 4

Version 2.3.1
- fixed compatibility with PHP 8.0

Version 2.3.0
- raised minimal version of PHP to 7.4
- used typed properties (possible BC break)
- deprecated some getters and setters

Version 2.2.0
- raised minimal version of PHP to 7.3

Version 2.1.0
- count can be passed together with other parameters to Translator::translate()
- symfony/yaml 5 is supported, dropped support for 3
- re-added support for nette/finder 2.5
- allowed resetting language in default settable locale resolvers

Version 2.0.1
- fixed overriding default folders in Nette DIC extension

Version 2.0.0
- removed old interfaces for loaders and locale resolvers
- dropped support for Nette 2.4
- marked almost all classes as final
- removed method ChainLocaleResolver::addResolver()
- added IFileLoader interface
- changed meaning of result of FileLoader::getLanguageFilenameMask()
- MessagesCatalogue no longer extends PhpLoader

Version 1.2.0
- raised minimal version of PHP to 7.2
- deprecated method ChainLocaleResolver::addResolver()
- moved interfaces for loaders and locale resolvers to Nexendrie\Translation namespace, deprecated old versions

Version 1.1.2
- improved handling of non-file resources in Tracy panel
- fixed scrolling in Tracy panel if list of resources is too long
- fixed columns in table Loaded resources in Tracy panel

Version 1.1.1
- CatalogueCompiler now compiles catalogues only when source files has changed
- fixed numeric subdomains being renamed (to start with 0)

Version 1.1.0
- raised minimal required version of nette/di to 2.4.10
- added ISettableLocaleResolver interface
- added event onCompile to CatalogueCompiler
- added message selector
- symfony/yaml 4 is supported
- added events onLanguageChange, onFoldersChange and onLoad to FileLoader

Version 1.0.0
- removed support for old format of defining folders for Nette DI extension
- ChainLocaleResolver now extends Collection from nexendrie/utils
- replaced constant EnvironmentLocaleResolver::VAR_NAME with property $varName
- added return type hint for FileLoader::loadDomain() and ILoaderAwareLocaleResolver::setLoader()

Version 1.0.0-rc3
- fixed detection of resolver in FileLoader
- added link to loaded resource in Tracy panel
- renamed constant EnvironmentLocaleResolver::VARNAME to VAR_NAME
- variable for SessionLocaleResolver can be changed now

Version 1.0.0-rc2
- folders are now ignored by Nette DI extension if no file loader is used
- Nette DI container is now regenerated whenever translation files change
- moved InvalidLoaderException and InvalidLocaleResolver back to namespace Nexendrie\Translation
- parameter for ParamLocaleResolver can be changed now
- changed format for defining folders for Nette DI extension, deprecated the old one

Version 1.0.0-rc1
- add actual domains to used resources in manually created messages catalogues
- translator is now registered to Latte when available in Nette DIC container
- moved IAppRequestAwareLocaleResolver to namespace Nexendrie\Translation\Bridges\NetteApplication
- made it possible to add custom callbacks to Translator::onUntranslated() from Nette DI extension
- moved InvalidLoaderException and InvalidLocaleResolver to namespace Nexendrie\Translation\Bridges\NetteDI
- added support for negative numbers/infinite in intervals
- made validation of config values (arrays) in TranslationExtension stricter
- %appDir%/lang is always among used folders with Nette DI extension
- fixed appending loader to multiple ILoaderAwareLocaleResolver in Nette DI extension
- changed default locale resolver to param, session, header in Nette DI extension
- separated Intervals to package nexendrie/utils
- added dependency on nexendrie/utils

Version 0.5.0
- added pluralization

Version 0.4.0
- raised minimal version of PHP to 7.1
- added chain locale resolver
- other Nette DI extensions can add folders for translations now
- added session and header locale resolvers
- added param locale resolver for Nette
- added virtual property EnvironmentLocaleResolver::$lang

Version 0.3.0
- nette/neon is now optional dependency
- loaders have to define available languages
- catalogue compiler now uses loader's languages by default
- switched order of parameters $folder and $languages in CatalogueCompiler's constructor
- added event onUntranslated for Translator

Version 0.2.1
- simplified using php loader and standalone messages catalogue with Nette DI extension
- texts from all folders are now used in MessagesCatalogue
- original resources are now stored in MessagesCatalogue
- simplified using fallback locale resolver with Nette DI extension

Version 0.2.0
- added JsonLoader, YamlLoader and PhpLoader
- Nette DI extension can now compile catalogues from messages

Version 0.1.0
- first version
