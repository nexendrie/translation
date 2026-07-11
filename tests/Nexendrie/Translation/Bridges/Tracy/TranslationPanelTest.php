<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Bridges\Tracy;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Nexendrie\Translation\Translator;

#[TestSuite("TranslationPanel")]
#[Group("nette")]
final class TranslationPanelTest extends \MyTester\TestCase
{
    use \MyTester\Bridges\NetteDI\TCompiledContainer;

    protected TranslationPanel $panel;

    #[BeforeTest]
    public function setUp(): void
    {
        $this->panel = $this->getService(TranslationPanel::class);
    }

    public function testGetTab(): void
    {
        $result = $this->panel->getTab();
        $this->assertType("string", $result);
        $r = new \SimpleXMLElement($result);
        $this->assertSame("Translation", (string) $r["title"]);
        $this->assertContains("en", (string) $r);
    }

    public function testGetPanel(): void
    {
        $result = $this->panel->getPanel();
        $this->assertType("string", $result);
        $r = new \SimpleXMLElement("<root>$result</root>");
        $this->assertSame(2, $r->count());
        $text = (string) $r->p;
        $this->assertSame("Untranslated messages: 0, loaded resources 0", $text);
        $this->assertSame("Resolved language", (string) $r->div->div->h1);
        foreach ($r->div->div->table->tr->children() as $i => $td) {
            if ($i === 0) {
                $this->assertSame("ManualLocaleResolver", (string) $td);
            } elseif ($i === 1) {
                $this->assertSame("en", (string) $td);
            }
        }
        foreach ($r->children() as $i1 => $div) {
            if ($i1 === 1) {
                $this->assertSame("Loaded resources", (string) $div->h1);
                foreach ($div->table->th as $i2 => $td) {
                    if ($i2 === 0) {
                        $this->assertSame("Domain", (string) $td);
                    } elseif ($i2 === 1) {
                        $this->assertSame("Filename", (string) $td);
                    }
                }
            } elseif ($i1 === 2) {
                $this->assertCount(0, $div->children());
            }
        }
    }

    public function testGetPanelResources(): void
    {
        $translator = $this->getService(Translator::class);
        $translator->translate("xyz");
        $result = $this->panel->getPanel();
        $this->assertType("string", $result);
        $r = new \SimpleXMLElement("<root>$result</root>");
        $text = (string) $r->p;
        $this->assertSame("Untranslated messages: 0, loaded resources 2", $text);
    }

    public function testGetPanelWithMessages(): void
    {
        $translator = $this->getService(Translator::class);
        $translator->translate("abcd");
        $result = $this->panel->getPanel();
        $this->assertType("string", $result);
        $r = new \SimpleXMLElement("<root>$result</root>");
        $text = (string) $r->p;
        $this->assertSame("Untranslated messages: 1, loaded resources 2", $text);
    }
}
