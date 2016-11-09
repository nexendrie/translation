<?php
namespace Nexendrie\Translation\Bridges\Tracy;

use Nexendrie\Translation\ILoader,
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
  function getTab() {
    $loader = $this->loader;
    ob_start();
    require __DIR__ . "/TranslationPanel.tab.phtml";
    return ob_get_clean();
  }
  
  /**
   * @return string
   */
  function getPanel() {
    $loader = $this->loader;
    $translator = $this->translator;
    $resourcesCount = count($loader->resources, COUNT_RECURSIVE) - count($loader->resources);
    ob_start();
    require __DIR__ . "/TranslationPanel.panel.phtml";
    return ob_get_clean();
  }
}
?>