<?php
namespace Nexendrie\Translation\Bridges\Tracy;

use Tester\Assert;

require __DIR__ . "/../../../../bootstrap.php";

class PanelTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  /** @var TranslationPanel */
  protected $panel;
  
  function setUp() {
    $this->panel = $this->getService(TranslationPanel::class);
  }
  
  function testGetTab() {
    $result = $this->panel->getTab();
    Assert::type("string", $result);
    $r = new \SimpleXMLElement($result);
    Assert::same("Translation", (string) $r["title"]);
    Assert::contains("en", (string) $r);
  }
  
  function testGetPanel() {
    $result = $this->panel->getPanel();
    Assert::type("string", $result);
    $r = new \SimpleXMLElement("<root>$result</root>");
    Assert::same(5, $r->count());
    Assert::same("Untranslated message: 0, loaded resources 0", (string) $r->p);
    Assert::same("Resolved language", (string) $r->div->h1);
    foreach($r->div->table->tr->children() as $i => $td) {
      if($i === 0) Assert::same("ManualLocaleResolver", (string) $td);
      elseif($i === 1) Assert::same("en", (string) $td);
    }
    foreach($r->children() as $i1 => $div) {
      if($i1 === 1) {
        Assert::same("Loaded resources", (string) $div->h1);
        foreach($div->table->th as $i2 => $td) {
          if($i2 === 0) Assert::same("Domain", (string) $td);
          elseif($i2 === 1) Assert::same("Filename", (string) $td);
        }
      } elseif($i1 === 2) {
        Assert::count(0, $div->children());
      }
    }
  }
}

$test = new PanelTest;
$test->run();
?>