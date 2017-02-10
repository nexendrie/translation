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
  
  /**
   * @return string
   */
  function getTab(): string {
    $loader = $this->loader;
    ob_start();
    require __DIR__ . "/TranslationPanel.tab.phtml";
    return ob_get_clean();
  }
  
  /**
   * @return string
   */
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