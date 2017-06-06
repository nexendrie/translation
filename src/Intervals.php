<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

use Nette\Utils\Strings;

/**
 * Intervals
 *
 * @author Jakub Konečný
 */
class Intervals {
  use \Nette\StaticClass;
  
  const PATTERN = '/(\{\d+(,\d+)*\})|((\[|\])(\d+|\+Inf|-Inf),(\d+|\+Inf|-Inf)(\[|\]))/';
  
  /**
   * @param string $text
   * @return string|NULL
   */
  static function findInterval(string $text): ?string {
    $found = preg_match_all(static::PATTERN, $text, $result);
    if(!$found) {
      return NULL;
    } else {
      return $result[0][0];
    }
  }
  
  static function isInInterval(int $number, string $interval): bool {
    if(is_null(static::findInterval($interval)) OR $interval !== static::findInterval($interval)) {
      return false;
    }
    if(Strings::startsWith($interval, "{")) {
      $numbers = explode(",", Strings::trim($interval, "{}"));
      return (in_array($number, $numbers));
    }
    $interval = str_replace("Inf", PHP_INT_MAX, $interval);
    $start = Strings::substring($interval, 0, 1);
    $end = Strings::substring($interval, -1, 1);
    [$limit1, $limit2] = explode(",", Strings::trim($interval, "[]"));
    if($limit1 > $limit2) {
      return false;
    } elseif($number < $limit1) {
      return false;
    } elseif($number > $limit2) {
      return false;
    } elseif($number == $limit1 AND $start === "]") {
      return false;
    } elseif($number == $limit2 AND $end === "[") {
      return false;
    } else {
      return true;
    }
  }
}
?>