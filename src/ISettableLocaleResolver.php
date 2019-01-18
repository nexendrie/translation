<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

/**
 * ISettableLocaleResolver
 *
 * @author Jakub Konečný
 */
interface ISettableLocaleResolver extends ILocaleResolver {
  public function setLang(string $lang): void;
}
?>