<?php
/**
 * Utility functions for timestamp and time/date/time zone representations.
 *
 * @copyright Copyright (c) 2012 bne1.
 * @author Andrew Udvare [au] <andrew@bne1.com>
 * @license http://www.opensource.org/licenses/mit-license.php
 *
 * @package Sutra
 * @link http://www.sutralib.com/
 *
 * @version 1.2
 */
class sTimestamp extends fTimestamp {
  const formatTimezoneNumber = 'sTimestamp::formatTimezoneNumber';

  /**
   * Loose regular expression to match timestamp as defined by W3C for
   *   HTML 5 date/datetime fields. The main difference is allowing for
   *   a space between the date and the literal 'T'.
   *
   * @var string
   */
  const DATETIME_RFC3339_REGEX = '/^([1-2][0-9]{3})\-([0-1][0-9])\-([0-3][0-9])(?:\s+)?T([0-2][0-9])\:([0-5][0-9])\:([0-5][0-9](?:\.\d+)?)(?:Z|(?:[\-\+]([0-1][0-9])\:00))?$/';

  /**
   * Type specified when the date/time string is RFC3339 standard.
   *
   * @var integer
   */
  const DATETIME_TYPE_RFC3339 = 1;

  /**
   * Overrides __construct() to accept a third argument $type, which specifies
   *   the type of date/time string this is.
   *
   * @param fTimestamp|object|string|integer $str  The date/time to represent,
   *   NULL is interpreted as now.
   * @param string $timezone The timezone for the date/time. This causes the
   *   date/time to be interpretted as being in the specified timezone. If not
   *   specified, will default to timezone set by ::setDefaultTimezone().
   * @param integer $type One of the DATETIME_TYPE_* constants.
   * @return sTimestamp Timestamp object.
   */
  public function __construct($str, $timezone = NULL, $type = NULL) {
    switch ($type) {
      case self::DATETIME_TYPE_RFC3339:
        $str = self::convertFromRFC3339($str);
        break;
    }

    parent::__construct($str, $timezone);
  }

  /**
   * Get a formatted timezone string such as +08:00 from 8 or -12:00 from -12.
   *
   * @param float $value The timezone, can be decimal.
   * @return string String, such as +08:00.
   */
  public static function formatTimezoneNumber($value) {
    $ret = '';
    $value = (float)$value;

    if ($value < 0) {
      if (abs($value) >= 10) {
        $ret = $value.':00';
      }
      else {
        $ret = '-0'.abs($value).':00';
      }
    }
    else {
      if ($value >= 10) {
        $ret = '+'.$value.':00';
      }
      else {
        $ret = '+0'.$value.':00';
      }
    }

    return $ret;
  }

  /**
   * Convert an RFC3339 (HTML 5 version) timestamp to UNIX. Timezone is
   *   ignored.
   *
   * HTML 5 mandates 2 extra constraints:
   * - the literal letters T and Z in the date/time syntax must always be uppercase
   * - the date-fullyear production is instead defined as four or more digits
   *     representing a number greater than 0
   *
   * @throws fValidationException If $throw is set to TRUE and the timestamp
   *   is invalid.
   *
   * @param string $rfc The RFC value, like: 1990-12-31T23:59:60Z or
   *   1996-12-19T16:39:57-08:00.
   * @return integer|boolean UNIX timestamp or boolean FALSE. If FALSE is
   *   returned, the string was invalid.
   */
  private static function convertFromRFC3339($rfc) {
    $matches = array();

    if (preg_match(self::DATETIME_RFC3339_REGEX, $rfc, $matches)) {
      $year = abs((int)$matches[1]);
      $month = abs((int)$matches[2]);
      $day = abs((int)$matches[3]);
      $hour = abs((int)$matches[4]);
      $minute = abs((int)$matches[5]);
      $second = abs($matches[6]);
      $datetime = $year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':'.$second;
      $timestamp = new self($datetime);
    }
    else {
      throw new fValidationException('The value specified could not be validated as a RFC3339 timestamp.');
    }

    return (int)$timestamp->format('U');
  }
}

/**
 * Copyright (c) 2012 Andrew Udvare <andrew@bne1.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
