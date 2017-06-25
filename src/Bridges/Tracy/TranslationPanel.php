<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\Tracy;

use Nexendrie\Translation\Loaders\ILoader,
    Nexendrie\Translation\Translator,
    Tracy\IBarPanel;

/**
 * Debugger panel for Tracy
 *
 * @author Jakub Konečný
 */
class TranslationPanel implements IBarPanel {
  /** @var Translator */
  protected $translator;
  /** @var ILoader */
  protected $loader;
  
  function __construct(Translator $translator, ILoader $loader) {
    $this->translator = $translator;
    $this->loader = $loader;
  }
  
  function getTab(): string {
    $lang = $this->loader->getLang();
    $tab = file_get_contents(__DIR__ . "/TranslationPanel.tab.html");
    return str_replace("%lang%", $lang, $tab);
  }
  
  function getPanel(): string {
    $loader = $this->loader;
    $translator = $this->translator;
    $resourcesCount = count($loader->getResources(), COUNT_RECURSIVE) - count($loader->getResources());
    ob_start();
    require __DIR__ . "/TranslationPanel.panel.phtml";
    return ob_get_clean();
  }
}
?>