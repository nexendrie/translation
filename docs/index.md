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

Core part of this package is Nexendrie\Translation\Translator class. It implements Nette\Localization\ITranslator interface which makes it usable in Nette applications. Its entry point is method translate which takes message id, count and additional parameters. Only message id is required.

The translate method searches for message id in list of known texts, replaces passed parameters in found text and returns the result. If the message is not found, empty string is returned. Example of message with parameters: "blah blah %param1%". Count can be also used in the message itself as it is added to parameters (as %count%).

The translator contains variable $onUntranslated which is an array of callbacks that are called when an unknown message id is encountered. By default, it is empty but some integrations may fill it with their own or user-defined callbacks.

Pluralization
-------------

Pluralization in messages is supported. You can define multiple variations for the message, the translator (with help of message selector) will choose the correct one depending on count. For the default message selector, the variations have to be separated by pipe (|) and you have to specify interval for every variant. You can list the values explicitly ({0,5,10} or {0}) or use inclusive range ([0,5] for 0 - 5), exclusive range (]0,5] for 1 - 4) or combination of exclusive and inclusive range. It is possible to pass -Inf/+Inf instead of number.

Package nexendrie/utils is used for handling intervals, see its documentation for details: https://nexendrie.gitlab.io/utils/. 

You can use a different message selector, it just has to implement Nexendrie\Translation\IMessageSelector interface.

Message domain and sub-domains
------------------------------

You can divide your texts to domains, if you do not specify any, "messages" is assumed. You can also use unlimited number of subdomains. Domain and subdomains are separated by dots in message id. Example: domain.subdomain1.subdomain2.id. 

Loaders
-----------

The translator is not responsible for loading texts, this task is delegated to a loader. This library contains a good number of default loaders but you can write your own one by implementing the Nexendrie\Translation\Loaders\ILoader interface. While the interface has methods getLang and setLang, it is a good idea to let a locale resolve handle resolving/changing the language.

Resolvers
-------------

A locale resolver has to implement Nexendrie\Translation\Resolvers\ILocaleResolver interface which has just 1 method resolve. It should return a string which represents current language or NULL if it could not resolve language. It that case, default language is used.

Default loaders
---------------

This library contains loaders which are able to load texts from files of many formats: ini, json, neon, php and yaml. You just have to tell where it should look for the files. It is also possible to use compiled (to php) messages catalogues. They contains all texts for the language in one file. Be aware that some loaders may require additional packages/PHP extensions to be installed. Consult composer.json for more details.

Names of files have to follow certain pattern. In general, it is domain.language.format, for messages catalogues it is catalogue.language.php.

Default locale resolvers
-----------------

There are a few locale resolvers available. The simplest one is FallbackLocaleResolvers which always returns NULL. There is also ManualLocaleResolvers which lets you select the language by yourself. Just use:

```php
<?php
$resolver = new Nexendrie\Translation\Resolvers\ManualLocaleResolver();
$resolver->lang = "en";
?>
```

. You can also use EnvironmentLocaleResolver in a similar fashion, the only difference is that the latter uses an environment variable as storage (it is stored in property $varName, default value is TRANSLATOR_LANGUAGE).

It is possible to detect language from Accept-Language header with HeaderLocaleResolver. There is also SessionLocaleResolver which takes and stores current language in session. These 2 resolvers require package nette/http.

If you want to use multiple ways to resolve the language, use ChainLocaleResolver.

```php
<?php
$resolver = new Nexendrie\Translation\Resolvers\ChainLocaleResolver();
$resolver->addResolver(new Nexendrie\Translation\Resolvers\ManualLocaleResolver()); //or
$resolver[] = new Nexendrie\Translation\Resolvers\ManualLocaleResolver();
?>
```

It tries all added resolvers (by the order in which they were added) until one returns a string.

There are also other resolvers in the library but they are available only for certain frameworks.

Translating Nette applications
------------------------------

This library contains a good integration to Nette Framework. There is extension for DI container, panel for Tracy and additional locale resolvers.

Start by registering the extension:

```yaml
extensions:
    translation: Nexendrie\Translation\Bridges\NetteDI\TranslationExtension
```

. The extension can work without any configuration as it has got sane default values. But of course, you can customize it to your liking. List of options with their default values follows:

```yaml
translation:
    localeResolver: # manual, environment, fallback, session, header, param or name of class implementing Nexendrie\Translation\Resolvers\ILocaleResolver
        - param
        - session
        - header
    default: en # default language
    debugger: %debugMode% # adds panel for Tracy if true
    messageSelector: Nexendrie\Translation\MessageSelector # class for message selector, has to implement Nexendrie\Translation\IMessageSelector
    loader:
        name: neon # neon, ini, json, yaml, php, catalogue or name of class implementing Nexendrie\Translation\Loaders\ILoader
        folders: # folders where files with translations are located
            - %appDir%/lang # this is always present unless overwritten with !
    onUntranslated: # custom callbacks for Translator::onUntranslated()
        - ["@translator", "logUntranslatedMessage"] # this is always present unless overwritten with !
    compiler:
        enabled: false # should we compile messages catalogues?
        languages: { } # compile catalogues only for these languages. if you do not specify any language, catalogues will compiled for ALL languages
``` 

Param locale resolver takes language from presenter's parameter locale, the parameter's name is stored in property $param which can be changed at will.

If you want to use ChainLocaleResolver, just specify the needed resolvers as elements of array:

```yaml
translation:
    localeResolver:
        - param
        - session
        - header
```

. To use just 1 locale resolver, specify it as a string:

```yaml
translation:
    localeResolver: header
```

.

After registering and configuring the extension, you can require Nexendrie\Translation\Translator (or better Nette\Localization\ITranslator) in other services.

You can also translate texts in Latte templates, as the extension automatically register the translator. It is possible to use count and other parameters. Examples:

```latte
{_"messages.abc"}
{_"messages.abc", 5}
{_"messages.abc", 5, ["param1" => "value1"]}
```
