<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

/**
 * SettableLocaleResolver
 *
 * @author Jakub Konečný
 */
interface SettableLocaleResolver extends LocaleResolver {
  public function setLang(string $lang): void;
}
?>