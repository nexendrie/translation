<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

class IntervalsTest extends \Tester\TestCase {
  protected function tryInterval(string $input, ?string $result) {
    Assert::same($result, Intervals::findInterval($input));
  }
  
  function testFindInterval() {
    $this->tryInterval("abc", NULL);
    $this->tryInterval("abc{0}abc", "{0}");
    $this->tryInterval("abc{0,11,2}abc", "{0,11,2}");
    $this->tryInterval("abc[0]abc", NULL);
    $this->tryInterval("abc[0,1,0]abc", NULL);
    $this->tryInterval("abc[0,1]abc", "[0,1]");
    $this->tryInterval("abc]0,1]abc", "]0,1]");
    $this->tryInterval("abc[0,1[abc", "[0,1[");
    $this->tryInterval("abc]0,1[abc", "]0,1[");
    $this->tryInterval("abc]Inf,Inf[abc", "]Inf,Inf[");
  }
  
  function testIsInInterval() {
    Assert::true(Intervals::isInInterval(10, "{10}"));
    Assert::false(Intervals::isInInterval(10, "abc"));
    Assert::false(Intervals::isInInterval(10, "{1,2,3,9,15}"));
    Assert::true(Intervals::isInInterval(9, "{1,2,3,9,15}"));
    Assert::false(Intervals::isInInterval(2, "[9,1]"));
    Assert::false(Intervals::isInInterval(1, "[2,5]"));
    Assert::false(Intervals::isInInterval(10, "[2,5]"));
    Assert::false(Intervals::isInInterval(2, "]2,5]"));
    Assert::false(Intervals::isInInterval(5, "[2,5["));
    Assert::true(Intervals::isInInterval(2, "[2,5]"));
    Assert::true(Intervals::isInInterval(5, "[2,5]"));
    Assert::true(Intervals::isInInterval(3, "[2,5]"));
  }
}

$test = new IntervalsTest;
$test->run();
?>