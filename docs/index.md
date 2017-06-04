Translation
==============

Translate easily your application.

Links
-----

Primary repository: https://gitlab.com/nexendrie/translation
Github repository: https://github.com/nexendrie/translation
Packagist: https://packagist.org/packages/nexendrie/translation

Installation
------------
The best way to install it is via Composer. Just add **nexendrie/translation** to your dependencies.

General info
------------

Core part of this package is Nexendrie\Translation\Translator class. It implements Nette\Localization\ITranslator interface which makes it usable in Nette applications. Its entry point is method translate which takes message id, count and additional paramters. Only message id is required, count does nothing at the moment.

The translate method searches for message id in list of known texts, replaced passed parameters in found text and returns the result. If nothing is found, empty string is returned. Example of message with parameters: "blah blah %param1%". 

You can divide your texts to domains, by default domain messages is used. You can also use unlimited number of subdomains. Domain and subdomains are separated by dots in message id. Example: domain.subdomain1.subdomain2.id. 

The translator is not responsible for loading texts, this task is delegated to a loader. This library contains a good number of default loaders but you can write your own one by implementing the Nexendrie\Translation\Loaders\ILoader interface. While the interface has methods getLang and setLang, it is a good idea to let a locale resolve handle resolving/changing the language.

A locale resolver has to implement Nexendrie\Translation\Resolvers\ILocaleResolver interface which defined just 1 method resolve. It should return a string which represents current language or NULL if it could not resolve language. It that case, default language is used.

Default loaders
---------------

This library contains loaders which are able to load texts from many formats: ini, json, neon, php and yaml. You just have to tell where it should look for the files. It is also possile to used compliled (to php) messages catalogues. They contains all texts for the language in one file. Be aware that some loader require additional packages/PHP extensions to be installed. Consult composer.json for more details.

Default locale resolvers
-----------------

There are a few locale resolvers available. The simplest one is FallbackLocaleResolvers which always returns NULL. There is also ManualLocaleResolvers which lets you select the language by yourself. Just use:

```php
$resolver = Nexendrie\Translation\Resolvers\ManualLocaleResolver;
$resolver->lang = "en";
```

. You can also use EnvironmentLocaleResolver in a similar fashion, the only difference is that the latter uses environment variable TRANSLATOR_LANGUAGE as storage.

If you want to use multiple ways to resolve the language, use ChainLocaleResolver.

```php
$resolver = Nexendrie\Translation\Resolvers\ChainLocaleResolver;
$resolver->addResolver(some resolver);
```

It tries all added resolvers until one returns a string.

There are also other resolvers in the library but they are available only for certain frameworks.

Translating Nette applications
------------------------------

This library contains a good integration to Nette Framework. There is extension for DIC container, panel for Tracy and additional locale resolvers.

Start by registering the extension:

```yaml
extensions:
    translation: Nexendrie\Translation\Bridges\NetteDI\TranslationExtension
```

. As you see, the extension can work without any configuration as it has got sane default values. But of course, you can customize it to your liking. List of options with their default values follows:

```yaml
translation:
    localeResolver: manual # manual, environment, fallback, session, header or param
    folders:
        - %appDir%/lang
    default: en # default language
    debugger: %debugMode% # adds panel for Tracy if true
    loader: neon # neon, ini, json, yaml, php or catalogue
    compiler:
        enabled: false # should we compile messages catalogues?
        languages: { } # compile catalogues only for these languages
``` 

Session locale resolver takes and stores current language in session, header locale resolver takes it from Accept-Language request header, param locale resolver from presenter's paramater locale.

If you want to use ChainLocaleResolver, just specify the needed resolvers as elements of array:

```yaml
translation:
    localeResolver:
        - param
        - session
        - header
```

.

After registering and configuring the extension, you can require Nexendrie\Translation\Translator (or better Nette\Localization\ITranslator) in other services.

For now, translator is not added to templates automatically, you have to do it yourself.
