<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\Tracy;

use Tester\Assert;
use Nexendrie\Translation\Translator;

require __DIR__ . "/../../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class TranslationPanelTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;

  protected TranslationPanel $panel;
  
  protected function setUp(): void {
    $this->panel = $this->getService(TranslationPanel::class); // @phpstan-ignore assign.propertyType
  }
  
  public function testGetTab(): void {
    $result = $this->panel->getTab();
    Assert::type("string", $result);
    $r = new \SimpleXMLElement($result);
    Assert::same("Translation", (string) $r["title"]);
    Assert::contains("en", (string) $r);
  }
  
  public function testGetPanel(): void {
    $result = $this->panel->getPanel();
    Assert::type("string", $result);
    $r = new \SimpleXMLElement("<root>$result</root>");
    Assert::same(2, $r->count());
    $text = (string) $r->p;
    Assert::same("Untranslated messages: 0, loaded resources 0", $text);
    Assert::same("Resolved language", (string) $r->div->div->h1);
    foreach($r->div->div->table->tr->children() as $i => $td) {
      if($i === 0) {
        Assert::same("ManualLocaleResolver", (string) $td);
      } elseif($i === 1) {
        Assert::same("en", (string) $td);
      }
    }
    foreach($r->children() as $i1 => $div) {
      if($i1 === 1) {
        Assert::same("Loaded resources", (string) $div->h1);
        foreach($div->table->th as $i2 => $td) {
          if($i2 === 0) {
            Assert::same("Domain", (string) $td);
          } elseif($i2 === 1) {
            Assert::same("Filename", (string) $td);
          }
        }
      } elseif($i1 === 2) {
        Assert::count(0, $div->children());
      }
    }
  }
  
  public function testGetPanelResources(): void {
    /** @var Translator $translator */
    $translator = $this->getService(Translator::class);
    $translator->translate("xyz");
    $result = $this->panel->getPanel();
    Assert::type("string", $result);
    $r = new \SimpleXMLElement("<root>$result</root>");
    $text = (string) $r->p;
    Assert::same("Untranslated messages: 0, loaded resources 2", $text);
  }
  
  public function testGetPanelWithMessages(): void {
    /** @var Translator $translator */
    $translator = $this->getService(Translator::class);
    $translator->translate("abcd");
    $result = $this->panel->getPanel();
    Assert::type("string", $result);
    $r = new \SimpleXMLElement("<root>$result</root>");
    $text = (string) $r->p;
    Assert::same("Untranslated messages: 1, loaded resources 2", $text);
  }
}

$test = new TranslationPanelTest();
$test->run();
?>