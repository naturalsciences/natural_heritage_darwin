<?php
/**
 * FuzzyDateTime
 * Contains the FuzzyDateTime Class
 *
 * PHP version 5.2
 *
 * @package    Darwin
 * @subpackage Libs
 * @author     DB team <darwin-ict@naturalsciences.be>
*/

/**
 * FuzzyDateTime is an object that will contain a date/time
 * and an applied mask to render the fuzzyness.
 *
 * @package Darwin
 * @author  DB team <darwin-ict@naturalsciences.be>
 */
class FuzzyDateTime extends DateTime
{
  /** Array of default values for each date/time part: year, month,... */
  protected static $defaultValues = array(
    'year' => '0001',
    'month' => '01',
    'day' => '01',
    'hour' => '00',
    'minute' => '00',
    'second' => '00',
  );

  /** Array of default max values for all date/time parts except year */
  protected static $defaultMaxValues = array(
    'month' => '12',
    'day' => '31',
    'hour' => '23',
    'minute' => '59',
    'second' => '59',
  );

  /** Array of values used for the mask.
   * Values telling which part is fuzzy or not. 32 for year, 16 for month,...
   */
  protected static $datePartsMask = array(
    'year' => 32,
    'month' => 16,
    'day' => 8,
    'hour' => 4,
    'minute' => 2,
    'second' => 1,
  );

  /** Flag used for date completion when some parts are fuzzy
   * if true, use the $defaultValues and if false use the $defaultMaxValues... */
  protected $start = true;

  /** Mask value
   * Sum of all date parts mask not fuzzy.
   * eg: 48 means year and month are known, the other parts are fuzzy
   */
  protected $mask = 0;

  /** Flag telling the object represent a date with time */
  protected $withTime = false;

  /** Default date part format */
  protected $dateFormat = 'd/m/Y';

  /** Default time part format */
  protected $timeFormat = 'H:i:s';

  /**
   * Configures the current date/time object.
   *
   * @param mixed   $dateTime Representation of a valid datetime (string or array)
   * @param integer $mask     Sum of each mask part not fuzzy: \
   * 32 for years, 16 for months
   * @param boolean $start    Flag used to tell if the fuzzy parts \
   * must be completed with with a Min date or Max Date
   * @param boolean $withTime Flag telling the object represent a date with time
   */
  public function __construct($dateTime = 'now', $mask = 0, $start = true, $withTime = false)
  {
    if (is_array($dateTime))
      $dateTime = self::getDateTimeStringFromArray($dateTime, $start, $withTime);
    parent::__construct($dateTime);
    $this->setMask($mask);
    $this->setStart($start);
    $this->setWithTime($withTime);
  }

  /**
   * Return an array with every date parts as key
   * and every max value as value
   *
   * @return array self::$defaultMaxValues
   */
  public static function getDefaultMaxArray()
  {
    // If static max year value is not defined,
    // take it from a config parameter in a config file
    if (!isset( self::$defaultMaxValues['year'])) {
      if(class_exists('sfConfig') && sfConfig::get('dw_yearUpperBound') != '')
        self::$defaultMaxValues['year'] = sfConfig::get('dw_yearUpperBound');
      else
        self::$defaultMaxValues['year'] = "2038";
    }
    return self::$defaultMaxValues;
  }

  /**
   * Return an array with every date parts as key
   * and every MinValues as value
   *
   * @return array self::$defaultValues
   */
  public static function getDefaultMinArray()
  {
    return self::$defaultValues;
  }

  /**
   * Check that a date part (year, month, day,...)
   * is well numeric and between the min and max values
   *
   * @param string $field Name of the date part - field to test
   * @param int    $value Value to be tested
   *
   * @return boolean
   */
  public static function validateDateField($field, $value)
  {
    $max_array = self::getDefaultMaxArray();
    $min_array = self::getDefaultMinArray();

    if (is_numeric($value) && intval($value)>= $min_array[$field] &&
        intval($value) <= $max_array[$field])
    {
      return true;
    }
    return false;
  }

  /**
   * Check that each part of a date array passed as parameter
   * correspond to what we expect :
   * a numerical date parts between min and max or nothing
   *
   * @param array $dateTime Array of date and time values to be checked
   *
   * @return string
   */
  public static function checkDateArray(array $dateTime)
  {
    // all elements must be empty or a number
    foreach (array('year', 'month', 'day', 'hour', 'minute', 'second') as $key)
    {
      if (isset($dateTime[$key]) && $dateTime[$key] !== '' &&
            !self::validateDateField($key, $dateTime[$key]) )
      {
        return 'wrong_date_part_length';
      }
    }
    return '';
  }

  /**
   * Transform a date/time array in a date/time string
   *
   * @param array   $dateTime Array of date and time values
   * @param boolean $start    Tells if date/time is a start or end date/time
   * @param boolean $withTime Return a date with or without time
   *
   * @return string
   */
  public static function getDateTimeStringFromArray(array $dateTime, $start=true, $withTime=false)
  {
    $min_array = self::getDefaultMinArray();
    $max_array = self::getDefaultMaxArray();

    // Check each date/time array parts are ok
    if (!self::checkDateArray($dateTime) == '')
    {
      // If not takes eather default min or max value
      if ($start)
        $dateTime = $min_array;
      else
        $dateTime = $max_array;
    }
    // Makes the replacement of empty parts by default values depending of it's a start or an end date/time
    foreach (array('year', 'month', 'day', 'hour', 'minute', 'second') as $key)
    {
      if (!isset($dateTime[$key]) || $dateTime[$key]==='' || $dateTime[$key] === null)
      {
        if ($start)
        {
          $$key = $min_array[$key];
        }
        else
        {
          if ($key == 'day')
          {
            // Compute the max days in month
            $maxDaysInMonth = new DateTime("$year/$month/01");
            $$key = $maxDaysInMonth->format('t');
          }
          else
          {
            $$key = $max_array[$key];
          }
        }
      }
      else
      {
          $$key = $dateTime[$key];
      }
    }
    // Format the string to be produced
    $dateTime = sprintf('%04d/%02d/%02d' .($withTime?' %02d:%02d:%02d':''), $year, $month, $day, $hour, $minute, $second);
    return $dateTime;
  }

  /**
   * Sets the object mask
   *
   * @param int $mask Mask value
   */
  public function setMask($mask=0)
  {
    $this->mask = $mask;
  }

  /**
   * Sets the object start value: true if a start date/time, false if an end date/time
   *
   * @param boolean $start Start value
   */
  public function setStart($start=true)
  {
    $this->start = $start;
  }

  /**
   * Sets the object withTime value: true if the object stores date and time and false if only a date
   *
   * @param boolean $withTime With time value
   */
  public function setWithTime($withTime=false)
  {
    $this->withTime = $withTime;
  }

  /**
   * Sets the object date format for the date stored in object display
   *
   * @param string $dateFormat Date format value
   */
  public function setDateFormat($dateFormat='d/m/Y')
  {
    $this->dateFormat = $dateFormat;
  }

  /**
   * Sets the object time format for the time stored in object display
   *
   * @param string $timeFormat Time format value
   */
  public function setTimeFormat($timeFormat='H:i:s')
  {
    $this->timeFormat = $timeFormat;
  }

  /**
   * Sets the object mask from a date time array passed as parameter
   *
   * @param array $dateTime The array to be parsed to determine the mask to store in object
   */
  public function setMaskFromDate(array $dateTime)
  {
    $this->mask = self::getMaskFromDate($dateTime);
  }

  public function getMask()
  {
    return $this->mask;
  }

  public function getStart()
  {
    return $this->start;
  }

  public function getWithTime()
  {
    return $this->withTime;
  }

  public function getDateFormat()
  {
    return $this->dateFormat;
  }

  public function getTimeFormat()
  {
    return $this->timeFormat;
  }

 /**
   * Returns the mask value corresponding to a given date/time part
   *
   * @param  string  $key   Date/Time part
   * @return int
   *
   */
  public static function getMaskFor($key)
  {
    return self::$datePartsMask[$key];
  }

 /**
   * Returns the name of the empty or invalid date/time part followed by _missing
   *
   * @param  array      $dateTime     Array of a date/time
   * @return string
   *
   */
  public static function checkDateTimeStructure (array $dateTime)
  {
      $checkDate = self::checkDateArray($dateTime);
      $has_an_empty = null;
      $items = array('year', 'month', 'day', 'hour', 'minute', 'second');
      foreach ($items as $i => $key)
      {
        if ( isset($dateTime[$key]) && self::validateDateField($key, $dateTime[$key]) )
        {
          if ($has_an_empty === null)
            continue; //untill there is a filled value
          return $items[$has_an_empty].'_missing';
        }
        else
        {
          if ($has_an_empty === null) // we got an empty... if no value after that, it's ok
            $has_an_empty = $i;
        }
      }
      return '';
  }

 /**
   * Return the mask (summed) value for a date/time array passed as parameter
   *
   * @param  array   $dateTime   The date/time array to be parsed to determine the associated mask (sum of each mask value defined for each parts encoded)
   * @return int     $mask
   *
   */
  public static function getMaskFromDate(array $dateTime)
  {
    $mask = 0;
    foreach (array('year', 'month', 'day', 'hour', 'minute', 'second') as $key)
    {
      // For each part found, and if part is valid, increment the mask value with the mask associated to the date part analyzed
      if (isset($dateTime[$key]) && self::validateDateField($key, $dateTime[$key]))
      {
        $mask += self::getMaskFor($key);
      }
      else
      {
        // Stop at the time a part's missing
        break;
      }
    }
    return $mask;
  }

 /**
   * Returns the date/time stored in object formated
   *
   * @param  boolean   $withTime   Should the string returned contains a time
   * @param  string    $dateFormat Date format to be applied
   * @param  string    $timeFormat Time format to be applied
   * @return string    Date/Time formated
   *
   */
  public function getDateTime($withTime=null, $dateFormat = null, $timeFormat=null)
  {
    $withTime = (is_null($withTime)) ? $this->getWithTime() : $withTime;
    $dateFormat = (is_null($dateFormat)) ? $this->getDateFormat() : $dateFormat;
    $timeFormat = (is_null($timeFormat)) ? $this->getTimeFormat() : $timeFormat;
    return $this->format($dateFormat.(($withTime) ? ' '.$timeFormat : ''));
  }

 /**
   * Returns the time stored in object formated
   *
   * @param  string    $timeFormat Time format to be applied
   * @return string    Time formated
   *
   */
  public function getTime($timeFormat=null)
  {
    $timeFormat = (is_null($timeFormat)) ? $this->getTimeFormat() : $timeFormat;
    return $this->format($timeFormat);
  }

 /**
   * Returns the date/time stored in object as an array
   *
   * @return array
   *
   */
  public function getDateTimeAsArray()
  {
    return array(
      'year' => intval($this->format('Y')),
      'month' => intval($this->format('m')),
      'day' => intval($this->format('d')),
      'hour' => intval($this->format('H')),
      'minute' => intval($this->format('i')),
      'second' => intval($this->format('s'))
    );
  }

 /**
   * Returns the date/time stored in object with mask applied as a formated string
   *
   * @return array
   *
   */
  public function getDateTimeMaskedAsArray()
  {
    $date = $this->getDateTimeAsArray();
    foreach (array('year', 'month', 'day', 'hour', 'minute', 'second') as $key)
    {
        // If no mask found for the key parsed, remove the key value from array to be returned
        if (! (self::getMaskFor($key) & $this->getMask()) )
          $date[$key]='';
    }
    return $date;
  }

 /**
   * Returns the date/time stored in object as a string formated with a tag for each parts that should be masked (missing parts at encoding moment)
   *
   * @param  string  $tag         The tag that should surround the date/time part to be masked
   * @return array
   *
   */
  public function getDateMasked($tag='em', $format=null)
  {
    if ($format === null )
    {
      $format = $this->dateFormat;
      if ($this->getWithTime())
        $format .= ' '.$this->timeFormat;
    }

    $patterns = array(
      'year' => array('Y','y','o'),
      'month' => array('n','F','m','M','t'),
      'day' => array('d','D','j','l','N','w','z'),
      'hour' => array('g','G','h','H','a','A'),
      'minute' => array('i') ,
      'second' => array('s','U')
    );

    foreach(self::$datePartsMask as $date_part => $associated_mask)
    {
      if ( ! ($associated_mask & $this->getMask()) )
      {
        foreach($patterns[$date_part] as $pattern)
        {
          $format = preg_replace("/($pattern)/", '§$1£',$format);
        }
      }
    }
    $result = $this->format($format);
    $result = preg_replace('|£([\-\/\\\ \:])§|','$1',$result); //Replace if tag are joined replace 2 tag by one big
    $result = preg_replace(array('/§/','/£/'), array("<$tag>","</$tag>"),$result);
    return $result;
  }

 /**
   * Returns the date/time stored in object as a formated string
   *
   * @return string
   *
   */
  public function __ToString()
  {
    return $this->getDateTime($this->getWithTime(), $this->getDateFormat(), $this->getTimeFormat());
  }

  public function addTime($time) {
    $ntime = DateTime::createFromFormat('H:i:s', $time);
    if($ntime) {
      $this->setTime($ntime->format('H'), $ntime->format('i'), $ntime->format('s'));
      $mask = $this->getMask();
      if( ! (self::getMaskFor('hour') & $mask) ) $mask += self::getMaskFor('hour');
      if( ! (self::getMaskFor('minute') & $mask) ) $mask += self::getMaskFor('minute');
      if( ! (self::getMaskFor('second') & $mask) ) $mask += self::getMaskFor('second');
      $this->setWithTime(true);
      $this->setMask($mask);
    }
  }
 /**
   * Returns a valid date from a string
   *
   * This function is used in import
   *
   * @return date('Y-m-d')
   *
   */
  public static function getValidDate($date)
  {
    $pattern = '/^(\d\d\d\d)(\-(0[1-9]|1[012])(\-((0[1-9])|1\d|2\d|3[01])(T(0\d|1\d|2[0-3])(:([0-5]\d)(:([0-5]\d))?))?)?)?|\-\-(0[1-9]|1[012])(\-(0[1-9]|1\d|2\d|3[01]))?|\-\-\-(0[1-9]|1\d|2\d|3[01])/';

    if(preg_match($pattern, $date, $matches)) {

      $date_part = array('year'=>$matches[1], 'month'=>'', 'day'=>'', 'hour'=>'', 'minute'=>'', 'second'=>'');
      if(isset($matches[3]))
        $date_part['month'] = $matches[3];
      if(isset($matches[5]))
        $date_part['day'] = $matches[5];
      if(isset($matches[8]))
        $date_part['hour'] = $matches[8];
      if(isset($matches[10]))
        $date_part['minute'] = $matches[10];
      if(isset($matches[12]))
        $date_part['second'] = $matches[12];
      $fuzzy =   new FuzzyDateTime($date_part,56, true, $date_part['hour']=='');
      $fuzzy->setMaskFromDate($date_part);
      return $fuzzy;
    }
    $tmp_date = $date = str_replace('-','/', $date);

    if( ($dt = DateTime::createFromFormat('y', $tmp_date)) || ($dt=DateTime::createFromFormat('Y', $tmp_date))){
      ($dt = DateTime::createFromFormat('y/m/d', $tmp_date.'/01/01')) || ($dt=DateTime::createFromFormat('Y/m/d', $tmp_date.'/01/01'));
      $tmp_date = $dt->format('Y-m-d');
    }
    if(DateTime::createFromFormat('m/y', $tmp_date) || DateTime::createFromFormat('n/y', $tmp_date)) $tmp_date = '01/'.$tmp_date;
    if(DateTime::createFromFormat('m/Y', $tmp_date) || DateTime::createFromFormat('n/Y', $tmp_date)) $tmp_date = '01/'.$tmp_date;

     try{
      $fuzzy =   new FuzzyDateTime($tmp_date);
      // try to find the mask
      $ctn_sep = substr_count($date, '/');
      if($ctn_sep == 0)
        $fuzzy->setMask(FuzzyDateTime::getMaskFor('year'));
      elseif($ctn_sep == 1)
        $fuzzy->setMask(FuzzyDateTime::getMaskFor('year') + FuzzyDateTime::getMaskFor('month'));
      elseif($ctn_sep == 2)
        $fuzzy->setMask(FuzzyDateTime::getMaskFor('year') + FuzzyDateTime::getMaskFor('month') + FuzzyDateTime::getMaskFor('day'));
      elseif($ctn_sep == 3)
        $fuzzy->setMask(
          FuzzyDateTime::getMaskFor('year') + FuzzyDateTime::getMaskFor('month') + FuzzyDateTime::getMaskFor('day') +
          FuzzyDateTime::getMaskFor('hour')
        );
      elseif($ctn_sep == 4)
        $fuzzy->setMask(
          FuzzyDateTime::getMaskFor('year') + FuzzyDateTime::getMaskFor('month') + FuzzyDateTime::getMaskFor('day') +
          FuzzyDateTime::getMaskFor('hour') + FuzzyDateTime::getMaskFor('minute')
        );
      elseif($ctn_sep == 5)
        $fuzzy->setMask(
          FuzzyDateTime::getMaskFor('year') + FuzzyDateTime::getMaskFor('month') + FuzzyDateTime::getMaskFor('day') +
          FuzzyDateTime::getMaskFor('hour') + FuzzyDateTime::getMaskFor('minute') + FuzzyDateTime::getMaskFor('second')
        );

      return $fuzzy;
     } catch(Exception $e) {}
     return null;
  }

}
