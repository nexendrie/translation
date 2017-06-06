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

Core part of this package is Nexendrie\Translation\Translator class. It implements Nette\Localization\ITranslator interface which makes it usable in Nette applications. Its entry point is method translate which takes message id, count and additional paramters. Only message id is required.

The translate method searches for message id in list of known texts, replaces passed parameters in found text and returns the result. If nothing is found, empty string is returned. Example of message with parameters: "blah blah %param1%". 

Pluralization
-------------

Pluralization in messages is supported. You can define multiple variations for the message, the translator will choose the correct one depending on count. The variations have to be separated by pipe (|) and you have to specify interval for every variant. You can list the values explicitly ({0,5,10} or {0}) or use inclusive range ([0,5] for 0 - 5), exclusive range (]0,5] for 1 - 4) or combination of exclusive and inclusive range. It is possible to pass Inf instead of number (only positive infinite is recognized right now).

Message domain and sub-domains
------------------------------

You can divide your texts to domains, by default domain messages is used. You can also use unlimited number of subdomains. Domain and subdomains are separated by dots in message id. Example: domain.subdomain1.subdomain2.id. 

Loaders
-----------

The translator is not responsible for loading texts, this task is delegated to a loader. This library contains a good number of default loaders but you can write your own one by implementing the Nexendrie\Translation\Loaders\ILoader interface. While the interface has methods getLang and setLang, it is a good idea to let a locale resolve handle resolving/changing the language.

Resolvers
-------------

A locale resolver has to implement Nexendrie\Translation\Resolvers\ILocaleResolver interface which has just 1 method resolve. It should return a string which represents current language or NULL if it could not resolve language. It that case, default language is used.

Default loaders
---------------

This library contains loaders which are able to load texts from files of many formats: ini, json, neon, php and yaml. You just have to tell where it should look for the files. It is also possible to use compliled (to php) messages catalogues. They contains all texts for the language in one file. Be aware that some loaders may require additional packages/PHP extensions to be installed. Consult composer.json for more details.

Default locale resolvers
-----------------

There are a few locale resolvers available. The simplest one is FallbackLocaleResolvers which always returns NULL. There is also ManualLocaleResolvers which lets you select the language by yourself. Just use:

```php
$resolver = Nexendrie\Translation\Resolvers\ManualLocaleResolver;
$resolver->lang = "en";
```

. You can also use EnvironmentLocaleResolver in a similar fashion, the only difference is that the latter uses environment variable TRANSLATOR_LANGUAGE as storage.

It is possible to detect language from Accept-Language header with HeaderLocaleResolver. There is also SessionLocaleResolver which takes and stores current language in session. These 2 resolvers require package nette/http.

If you want to use multiple ways to resolve the language, use ChainLocaleResolver.

```php
$resolver = Nexendrie\Translation\Resolvers\ChainLocaleResolver;
$resolver->addResolver(some resolver);
```

It tries all added resolvers (by the order in which they were added) until one returns a string.

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
    onUntranslated: { } # custom callbacks for Translator::onUntranslated()
    compiler:
        enabled: false # should we compile messages catalogues?
        languages: { } # compile catalogues only for these languages
``` 

Param locale resolver from presenter's parameter locale.

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

You can also translate texts in Latte templates, as the extension automatically register the translator. It is possible to use count and other parameters. Examples:

```latte
{_"messages.abc"}
{_"messages.abc", 5}
{_"messages.abc", 5, ["param1" => "value1"]}
```