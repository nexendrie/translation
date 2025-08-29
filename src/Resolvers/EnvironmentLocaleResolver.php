<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Resolvers;

use Nexendrie\Translation\SettableLocaleResolver;

/**
 * EnvironmentResolver
 * Reads current language from an environment variable
 *
 * @author Jakub Konečný
 * @property string|null $lang
 */
final class EnvironmentLocaleResolver implements SettableLocaleResolver
{
    use \Nette\SmartObject;

    public string $varName = "TRANSLATOR_LANGUAGE";

    /**
     * @deprecated Access the property directly
     */
    public function getLang(): ?string
    {
        $lang = getenv($this->varName);
        if (is_string($lang)) {
            return $lang;
        }
        return null;
    }

    /**
     * @deprecated Access the property directly
     */
    public function setLang(?string $lang): void
    {
        if ($lang === null) {
            putenv($this->varName);
        } else {
            putenv($this->varName . "=$lang");
        }
    }

    /**
     * @deprecated Access the property directly
     */
    public function getVarName(): string
    {
        return $this->varName;
    }

    /**
     * @deprecated Access the property directly
     */
    public function setVarName(string $varName): void
    {
        $this->varName = $varName;
    }

    public function resolve(): ?string
    {
        return $this->getLang();
    }
}
