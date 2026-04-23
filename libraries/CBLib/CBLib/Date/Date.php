<?php
/**
* CBLib, Community Builder Library(TM)
* @version $Id: 08.06.13 17:29 $
* @package CBLib\Database
* @copyright (C) 2004-2023 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CBLib\Date;

use CBLib\Application\Config;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use DateTime;
use DateTimeZone;
use DateInterval;

defined('CBLIB') or die();

/**
 * CBLib\Date\Date Class implementation
 */
class Date
{
	const DAY_ABBR						=	"\x021\x03";
	const DAY_NAME						=	"\x022\x03";
	const MONTH_ABBR					=	"\x023\x03";
	const MONTH_NAME					=	"\x024\x03";
	const MERIDIEM_UPPER				=	"\x025\x03";
	const MERIDIEM_LOWER				=	"\x026\x03";

	/** @var string  */
	protected static $dateFormat		=	null;
	/** @var string  */
	protected static $timeFormat		=	null;
	/** @var string  */
	protected static $dateTimeFormat	=	null;
	/** @var DateTimeZone  */
	protected static $utc;
	/** @var DateTime  */
	protected $date;
	/** @var DateTimeZone  */
	protected $tz;
	/** @var null|string  */
	protected $from;
	/** @var Config  */
	protected $config;

	/**
	 * Constructor
	 *
	 * @param null|string                  $date null: now, string: "start" for CB start time, date or datetime string as UTC, int: unix timestamp
	 * @param null|string|int|DateTimeZone $tz   null: server offset, string: timezone string (e.g. UTC), int: offset in hours, DateTimeZone: PHP timezone
	 * @param null|string                  $from Format to convert the date from
	 * @param Config                       $config
	 */
	public function __construct( $date = null, $tz = null, $from = null, Config $config = null)
	{
		if ( $config === null ) {
			$config				=	Application::Config();
		}
		$this->config			=	$config;

		$this->init();

		if ( ! $date ) {
			$date				=	'now';
		} elseif ( $date === 'start' ) {
			$date				=	'@' . (string) Application::Application()->getStartTime();
		}

		if ( ! $tz ) {
			$tz					=	Application::CBFramework()->getCfg( 'user_timezone' );
		}

		$tzCache				=	date_default_timezone_get();

		date_default_timezone_set( 'UTC' );

		if ( is_integer( $date ) ) {
			$this->date			=	new DateTime();

			$this->date->setTimestamp( $date );
		} else {
			if ( $date == 'now' ) {
				$from			=	null;
			} elseif ( is_numeric( $date ) ) {
				$date			=	date( 'c', $date );
			}

			if ( $from ) {
				$this->date		=	new DateTime();

				$dateArray		=	date_parse_from_format( $from, $date );

				$this->date->setDate( $dateArray['year'], $dateArray['month'], $dateArray['day'] );
				$this->date->setTime( $dateArray['hour'], $dateArray['minute'], $dateArray['second'] );
			} else {
				$this->date		=	new DateTime( $date );
			}
		}

		date_default_timezone_set( $tzCache );

		$this->setTimezone( $tz );

		$this->from				=	$from;
	}

	/**
	 * Initialize the static variables
	 */
	private function init()
	{
		if ( self::$dateTimeFormat ) {
			return;
		}

		$dateFormat				=	( CBTxt::T( 'UE_DATE_FORMAT', '' ) != '' ? CBTxt::T( 'UE_DATE_FORMAT', '' ) : $this->config->get( 'date_format', 'm/d/Y' ) );
		$timeFormat				=	( CBTxt::T( 'UE_TIME_FORMAT', '' ) != '' ? CBTxt::T( 'UE_TIME_FORMAT', '' ) : ' ' . $this->config->get( 'time_format', 'H:i:s' ) );

		self::$dateFormat		=	$dateFormat;
		self::$timeFormat		=	trim( $timeFormat );
		self::$dateTimeFormat	=	$dateFormat . $timeFormat;
		self::$utc				=	new DateTimeZone( 'UTC' );
	}

	/**
	 * Returns the DateTime object
	 *
	 * @return DateTime
	 */
	public function getDateTime()
	{
		return $this->date;
	}

	/**
	 * Set the TimeZone associated with the DateTime
	 *
	 * @param string|int|DateTimeZone $tz string: timezone string (e.g. UTC), int: offset in hours, DateTimeZone: PHP timezone
	 * @return Date
	 */
	public function setTimezone( $tz )
	{
		if ( is_integer( $tz ) ) {
			// Handle integer based offsets:
			$tz		=	timezone_name_from_abbr( null, ( $tz * 3600 ), date( 'I' ) );
		}

		if ( ! ( $tz instanceof DateTimeZone ) ) {
			$tz		=	new DateTimeZone( $tz );
		}

		$this->tz	=	$tz;

		$this->date->setTimezone( $tz );

		return $this;
	}

	/**
	 * Returns the timezone relative to given Date
	 *
	 * @return DateTimeZone
	 */
    public function getTimezone()
	{
		return $this->tz;
	}

	/**
	 * Returns the timezone offset
	 *
	 * @return int
	 */
	public function getOffset()
	{
		return $this->date->getOffset();
	}

	/**
	 * Sets the date and time based on a Unix timestamp
	 *
	 * @param int $timestamp
	 * @return Date
	 */
	public function setTimestamp( $timestamp )
	{
		$this->date->setTimestamp( $timestamp );

		return $this;
	}

	/**
	 * Gets the Unix timestamp
	 *
	 * @return int
	 */
	public function getTimestamp()
	{
		return $this->date->getTimestamp();
	}

	/**
	 * Translates day
	 *
	 * @param int  $day  The day of the week to translate
	 * @param bool $abbr True: translate to abbreviated, False: full day string translated
	 * @return string
	 */
	public function weekDayToString( $day, $abbr = false )
	{
		switch ( (int) $day ) {
			case 0:
				return ( $abbr ? CBTxt::T( 'UE_WEEKDAYS_SHORT_1', 'Sun' ) : CBTxt::T( 'UE_WEEKDAYS_1', 'Sunday' ) );
				break;
			case 1:
				return ( $abbr ? CBTxt::T( 'UE_WEEKDAYS_SHORT_2', 'Mon' ) : CBTxt::T( 'UE_WEEKDAYS_2', 'Monday' ) );
				break;
			case 2:
				return ( $abbr ? CBTxt::T( 'UE_WEEKDAYS_SHORT_3', 'Tue' ) : CBTxt::T( 'UE_WEEKDAYS_3', 'Tuesday' ) );
				break;
			case 3:
				return ( $abbr ? CBTxt::T( 'UE_WEEKDAYS_SHORT_4', 'Wed' ) : CBTxt::T( 'UE_WEEKDAYS_4', 'Wednesday' ) );
				break;
			case 4:
				return ( $abbr ? CBTxt::T( 'UE_WEEKDAYS_SHORT_5', 'Thu' ) : CBTxt::T( 'UE_WEEKDAYS_5', 'Thursday' ) );
				break;
			case 5:
				return ( $abbr ? CBTxt::T( 'UE_WEEKDAYS_SHORT_6', 'Fri' ) : CBTxt::T( 'UE_WEEKDAYS_6', 'Friday' ) );
				break;
			case 6:
				return ( $abbr ? CBTxt::T( 'UE_WEEKDAYS_SHORT_7', 'Sat' ) : CBTxt::T( 'UE_WEEKDAYS_7', 'Saturday' ) );
				break;
		}

		return '';
	}

	/**
	 * Translates month
	 *
	 * @param int  $month The month of the year to translate
	 * @param bool $abbr  True: translate to abbreviated, False: full month string translated
	 * @return string
	 */
	public function monthToString( $month, $abbr = false )
	{
		switch ( (int) $month ) {
			case 1:
				return ( $abbr ? CBTxt::T( 'UE_MONTHS_SHORT_1', 'Jan' ) : CBTxt::T( 'UE_MONTHS_1', 'January' ) );
				break;
			case 2:
				return ( $abbr ? CBTxt::T( 'UE_MONTHS_SHORT_2', 'Feb' ) : CBTxt::T( 'UE_MONTHS_2', 'February' ) );
				break;
			case 3:
				return ( $abbr ? CBTxt::T( 'UE_MONTHS_SHORT_3', 'Mar' ) : CBTxt::T( 'UE_MONTHS_3', 'March' ) );
				break;
			case 4:
				return ( $abbr ? CBTxt::T( 'UE_MONTHS_SHORT_4', 'Apr' ) : CBTxt::T( 'UE_MONTHS_4', 'April' ) );
				break;
			case 5:
				return ( $abbr ? CBTxt::T( 'UE_MONTHS_SHORT_5', 'May' ) : CBTxt::T( 'UE_MONTHS_5', 'May' ) );
				break;
			case 6:
				return ( $abbr ? CBTxt::T( 'UE_MONTHS_SHORT_6', 'Jun' ) : CBTxt::T( 'UE_MONTHS_6', 'June' ) );
				break;
			case 7:
				return ( $abbr ? CBTxt::T( 'UE_MONTHS_SHORT_7', 'Jul' ) : CBTxt::T( 'UE_MONTHS_7', 'July' ) );
				break;
			case 8:
				return ( $abbr ? CBTxt::T( 'UE_MONTHS_SHORT_8', 'Aug' ) : CBTxt::T( 'UE_MONTHS_8', 'August' ) );
				break;
			case 9:
				return ( $abbr ? CBTxt::T( 'UE_MONTHS_SHORT_9', 'Sep' ) : CBTxt::T( 'UE_MONTHS_9', 'September' ) );
				break;
			case 10:
				return ( $abbr ? CBTxt::T( 'UE_MONTHS_SHORT_10', 'Oct' ) : CBTxt::T( 'UE_MONTHS_10', 'October' ) );
				break;
			case 11:
				return ( $abbr ? CBTxt::T( 'UE_MONTHS_SHORT_11', 'Nov' ) : CBTxt::T( 'UE_MONTHS_11', 'November' ) );
				break;
			case 12:
				return ( $abbr ? CBTxt::T( 'UE_MONTHS_SHORT_12', 'Dec' ) : CBTxt::T( 'UE_MONTHS_12', 'December' ) );
				break;
		}

		return '';
	}

	/**
	 * Translates meridiem
	 *
	 * @param string $meridiem The meridiem (am/pm) to translate
	 * @return string
	 */
	private function meridiemToString( $meridiem )
	{
		switch ( $meridiem ) {
			case 'am':
				return cbutf8_strtolower( CBTxt::T( 'UE_HALF_DAY_AM', 'AM' ) );
				break;
			case 'AM':
				return CBTxt::T( 'UE_HALF_DAY_AM', 'AM' );
				break;
			case 'pm':
				return cbutf8_strtolower( CBTxt::T( 'UE_HALF_DAY_PM', 'PM' ) );
				break;
			case 'PM':
				return CBTxt::T( 'UE_HALF_DAY_PM', 'PM' );
				break;
		}

		return '';
	}

	/**
	 * Formats the date
	 *
	 * @param null|string $format    The format to output as supposed by date()
	 * @param bool        $local     True: offset the datetime to local, False: force to UTC
	 * @param bool        $translate True: translate formatted date values (e.g. Tuesday, October, PM), False: skip translation
	 * @return string
	 */
	public function format( $format = null, $local = true, $translate = true )
	{
		if ( ( ! $format ) || ( $format == 'datetime' ) ) {
			$format			=	self::$dateTimeFormat;
		} elseif ( $format == 'date' ) {
			$format			=	self::$dateFormat;
		} elseif ( $format == 'time' ) {
			$format			=	self::$timeFormat;
		}

		if ( $translate ) {
			// Replace the string output with a substitution so it can be translated later:
			$format			=	preg_replace( '/(^|[^\\\])D/', "\\1" . self::DAY_ABBR, $format );
			$format			=	preg_replace( '/(^|[^\\\])l/', "\\1" . self::DAY_NAME, $format );
			$format			=	preg_replace( '/(^|[^\\\])M/', "\\1" . self::MONTH_ABBR, $format );
			$format			=	preg_replace( '/(^|[^\\\])F/', "\\1" . self::MONTH_NAME, $format );
			$format			=	preg_replace( '/(^|[^\\\])a/', "\\1" . self::MERIDIEM_LOWER, $format );
			$format			=	preg_replace( '/(^|[^\\\])A/', "\\1" . self::MERIDIEM_UPPER, $format );
		}

		if ( ! $local ) {
			$this->date->setTimezone( self::$utc );
		}

		// Format the date
		$return				=	$this->date->format( $format );

		if ( $translate ) {
			// Replace the substitutions with translated text:
			if ( strpos( $return, self::DAY_ABBR ) !== false ) {
				$return		=	str_replace( self::DAY_ABBR, $this->weekDayToString( $this->date->format( 'w' ), true ), $return );
			}

			if ( strpos( $return, self::DAY_NAME ) !== false ) {
				$return		=	str_replace( self::DAY_NAME, $this->weekDayToString( $this->date->format( 'w' ) ), $return );
			}

			if ( strpos( $return, self::MONTH_ABBR ) !== false ) {
				$return		=	str_replace( self::MONTH_ABBR, $this->monthToString( $this->date->format( 'n' ), true ), $return );
			}

			if ( strpos( $return, self::MONTH_NAME ) !== false ) {
				$return		=	str_replace( self::MONTH_NAME, $this->monthToString( $this->date->format( 'n' ) ), $return );
			}

			if ( strpos( $return, self::MERIDIEM_LOWER ) !== false ) {
				$return		=	str_replace( self::MERIDIEM_LOWER, $this->meridiemToString( $this->date->format( 'a' ) ), $return );
			}

			if ( strpos( $return, self::MERIDIEM_UPPER ) !== false ) {
				$return		=	str_replace( self::MERIDIEM_UPPER, $this->meridiemToString( $this->date->format( 'A' ) ), $return );
			}
		}

		if ( ! $local ) {
			$this->date->setTimezone( $this->tz );
		}

		return $return;
	}

	/**
	 * Outputs a human-readable form of a time-interval.
	 * "Y years, M months, D days, H hours, I minutes and S seconds"
	 *
	 * @param  int    $y         Years
	 * @param  int    $m         Months
	 * @param  int    $d         Days
	 * @param  int    $h         Hours
	 * @param  int    $i         Minutes
	 * @param  int    $s         Seconds
	 * @param  bool   $calendar  Calendar period, e.g. 3 calendar months, years or days
	 * @param  string $relToNow  'neutral': just a time period, 'ago': conjugates and adds "... ago", 'in': conjugates and adds "in ..."
	 * @return string
	 */
	private static function getTimeInterval( $y, $m = 0, $d = 0, $h = 0, $i = 0, $s = 0, $calendar = false, $relToNow = 'neutral' )
	{
		$parts			=	array();

		switch ( $relToNow ) {
			case 'neutral':
				if ( ! $calendar ) {
					if ( $y ) {
						$parts[]	=	CBTxt::T( 'X_YEARS', "one year|[X] years", array( '[X]' => $y ) );
					}
					if ( $m ) {
						$parts[]	=	CBTxt::T( 'X_MONTHS', "one month|[X] months", array( '[X]' => $m ) );
					}
					if ( $d ) {
						$parts[]	=	CBTxt::T( 'X_DAYS', "one day|[X] days", array( '[X]' => $d ) );
					}
				} else {
					if ( $y ) {
						$parts[]	=	CBTxt::T( 'X_CALENDAR_YEARS', "one calendar year|[X] calendar years", array( '[X]' => $y ) );
					}
					if ( $m ) {
						$parts[]	=	CBTxt::T( 'X_CALENDAR_MONTHS', "one calendar month|[X] calendar months", array( '[X]' => $m ) );
					}
					if ( $d ) {
						$parts[]	=	CBTxt::T( 'X_CALENDAR_DAYS', "one calendar day|[X] calendar days", array( '[X]' => $d ) );
					}
				}
				if ( $h ) {
					$parts[]		=	CBTxt::T( 'X_HOURS', "one hour|[X] hours", array( '[X]' => $h ) );
				}
				if ( $i ) {
					$parts[]		=	CBTxt::T( 'X_MINUTES', "one minute|[X] minutes", array( '[X]' => $i ) );
				}
				if ( $s || ( count( $parts ) == 0 ) ) {
					$parts[]		=	CBTxt::T( 'X_SECONDS', "one second|[X] seconds", array( '[X]' => $s ) );
				}
				break;
			case 'ago':
				if ( $y ) {
					$parts[]		=	CBTxt::T( 'X_YEARS_PAST', "one year|[X] years", array( '[X]' => $y ) );
				}
				if ( $m ) {
					$parts[]		=	CBTxt::T( 'X_MONTHS_PAST', "one month|[X] months", array( '[X]' => $m ) );
				}
				if ( $d ) {
					$parts[]		=	CBTxt::T( 'X_DAYS_PAST', "one day|[X] days", array( '[X]' => $d ) );
				}
				if ( $h ) {
					$parts[]		=	CBTxt::T( 'X_HOURS_PAST', "one hour|[X] hours", array( '[X]' => $h ) );
				}
				if ( $i ) {
					$parts[]		=	CBTxt::T( 'X_MINUTES_PAST', "one minute|[X] minutes", array( '[X]' => $i ) );
				}
				if ( $s || ( count( $parts ) == 0 ) ) {
					$parts[]		=	CBTxt::T( 'X_SECONDS_PAST', "one second|[X] seconds", array( '[X]' => $s ) );
				}
				break;
			case 'in':
				if ( $y ) {
					$parts[]		=	CBTxt::T( 'X_YEARS_FUTURE', "one year|[X] years", array( '[X]' => $y ) );
				}
				if ( $m ) {
					$parts[]		=	CBTxt::T( 'X_MONTHS_FUTURE', "one month|[X] months", array( '[X]' => $m ) );
				}
				if ( $d ) {
					$parts[]		=	CBTxt::T( 'X_DAYS_FUTURE', "one day|[X] days", array( '[X]' => $d ) );
				}
				if ( $h ) {
					$parts[]		=	CBTxt::T( 'X_HOURS_FUTURE', "one hour|[X] hours", array( '[X]' => $h ) );
				}
				if ( $i ) {
					$parts[]		=	CBTxt::T( 'X_MINUTES_FUTURE', "one minute|[X] minutes", array( '[X]' => $i ) );
				}
				if ( $s || ( count( $parts ) == 0 ) ) {
					$parts[]		=	CBTxt::T( 'X_SECONDS_FUTURE', "one second|[X] seconds", array( '[X]' => $s ) );
				}
				break;
		}

		if ( count( $parts ) > 2 ) {
			$endText	=	array_pop( $parts );
			$beginText	=	implode( CBTxt::T( 'CB_FIRST_COMMA_OF_YEARS_COMMA_MONTHS_AND_DAYS', ", " ), $parts );
			return $beginText . CBTxt::T( 'CB_LAST_AND_OF_YEARS_COMMA_MONTHS_AND_DAYS', " and " ) . $endText;
		}
		$periodText					=	implode( CBTxt::T( 'CB_ONLY_AND_OF_YEARS_AND_MONTHS', " and " ), $parts );

		switch ( $relToNow ) {
			case 'ago':
				return CBTxt::T( 'CB_TIMEPERIOD_AGO_EXPRESSION', "[TIMEPERIOD] ago", array( '[TIMEPERIOD]' => $periodText ) );
			case 'in':
				return CBTxt::T( 'CB_IN_TIMEPERIOD_EXPRESSION', "in [TIMEPERIOD]", array( '[TIMEPERIOD]' => $periodText ) );
		}
		return $periodText;
	}

	/**
	 * Outputs a date interval (from $this to $toDate) in human-readable translated form:
	 * - "x days" (if 3 to 90 days), or
	 * - "X years, y months and z days", or
	 * - "x days and y hours", or
	 * - "x hours and y minutes", or
	 * - "x minutes and y seconds", or
	 * - "x seconds"
	 *
	 * @param  Date    $toDate    End Epoch time UTC
	 * @param  bool    $calendar  Calendar period, e.g. 3 calendar months, years or days
	 * @param  string  $relToNow  'neutral': just a time period, 'ago': conjugates and adds "... ago", 'in': conjugates and adds "in ..."
	 * @return string             Human-readable form
	 */
	private function timeIntervalToNaturalText( $toDate, $calendar, $relToNow )
	{
		$interval	=	$this->diff( $toDate );

		$days		=	$interval->format( 'a' );
		if ( ( $days > 2 ) && ( $days <= 90 ) ) {
			return $this::getTimeInterval( 0, 0, $days, 0, 0, 0, $calendar, $relToNow );
		}

		if ( $interval->y || $interval->m ) {
			return $this::getTimeInterval( $interval->y, $interval->m, $interval->d, 0, 0, 0, $calendar, $relToNow );
		}

		if ( $interval->d ) {
			return $this::getTimeInterval( 0, 0, $interval->d, $interval->h, 0, 0, $calendar, $relToNow );
		}

		return $this::getTimeInterval( 0, 0, 0, $interval->h, $interval->i, ( $interval->h ? 0 : $interval->s ), $calendar, $relToNow );
	}

	/**
	 * Outputs a date interval in human-readable translated form:
	 * - "x days" (if 3 to 90 days), or
	 * - "X years, y months and z days", or
	 * - "x days and y hours", or
	 * - "x hours and y minutes", or
	 * - "x minutes and y seconds", or
	 * - "x seconds"
	 * and with $calendar=true:
	 * - "x calendar days" (if 3 to 90 days), or
	 * - "X calendar years, y months and z days", or
	 * - "x calendar days and y hours", or
	 *
	 * use it for a time period NOT starting or ending now
	 * (otherwise @see getTimeAgo for a time ending now, and
	 * @see getFutureTime for a time period starting now)
	 *
	 * @param  bool  $calendar  Calendar period, e.g. 3 calendar months, years or days
	 * @param  Date  $toDate    End date (later than $this)
	 * @return string           Human-readable form
	 */
	public function getTimePeriod( $toDate, $calendar = false )
	{
		return $this->timeIntervalToNaturalText( $toDate, $calendar, 'neutral' );
	}

	/**
	 * Outputs a past date interval from $this to now in human-readable translated form:
	 * - "x days ago" (if 3 to 90 days), or
	 * - "X years, y months and z days ago", or
	 * - "x days and y hours ago", or
	 * - "x hours and y minutes ago", or
	 * - "x minutes and y seconds ago", or
	 * - "x seconds ago", or
	 * - "now" if no time difference
	 *
	 * use it for a time period ending now
	 * (otherwise @see getTimePeriod)
	 *
	 * @return string            Human-readable form
	 */
	public function getTimeAgo( )
	{
		$now	=	new self( 'start', $this->tz, null, $this->config );

		if ( $this->getDateTime()->getTimestamp() === $now->getDateTime()->getTimestamp() ) {
			return CBTxt::T( 'CB_TIMEAGO_NOW', "now" );
		}

		if ( $this->getDateTime() > $now->getDateTime() ) {
			$relToNow	=	'in';
		} else {
			$relToNow	=	'ago';
		}
		return $this->timeIntervalToNaturalText( $now, false, $relToNow );
	}

	/**
	 * Alter the timestamp of a Date object by incrementing or decrementing in a format accepted by strtotime()
	 *
	 * @param string $modify
	 * @return Date
	 */
	public function modify( $modify )
	{
		$this->date->modify( $modify );

		return $this;
	}

	/**
	 * Adds an amount of days, months, years, hours, minutes and seconds
	 *
	 * @param string|DateInterval $interval
	 * @return Date
	 */
	public function add( $interval )
	{
		if ( ! ( $interval instanceof DateInterval ) ) {
			if ( substr( $interval, 0, 1 ) != 'P' ) {
				$interval	=	DateInterval::createFromDateString( $interval );
			} else {
				$interval	=	new DateInterval( $interval );
			}
		}

		$this->date->add( $interval );

		return $this;
	}

	/**
	 * Subtracts an amount of days, months, years, hours, minutes and seconds
	 *
	 * @param string|DateInterval $interval
	 * @return Date
	 */
	public function sub( $interval )
	{
		if ( ! ( $interval instanceof DateInterval ) ) {
			if ( substr( $interval, 0, 1 ) != 'P' ) {
				$interval	=	DateInterval::createFromDateString( $interval );
			} else {
				$interval	=	new DateInterval( $interval );
			}
		}

		$this->date->sub( $interval );

		return $this;
	}

	/**
	 * Returns the difference between two dates
	 *
	 * @param null|string|Date|DateTime $date
	 * @param bool                      $absolute [optional] Whether to return absolute difference
	 * @return bool|DateInterval                  The DateInterval object representing the difference between the two dates or FALSE on failure
	 */
	public function diff( $date, $absolute = false )
	{
		if ( ( ! ( $date instanceof Date ) ) && ( ! ( $date instanceof DateTime ) ) ) {
			$diffDate	=	new self( $date, $this->tz, ( $date && ( $date != 'now' ) ? $this->from : null ), $this->config );

			$date		=	$diffDate->getDateTime();
		} elseif ( $date instanceof Date ) {
			$date		=	$date->getDateTime();
		}

		return $this->date->diff( $date, $absolute );
	}

	/**
	 * Returns the date age in years (UTC)
	 *
	 * @return string
	 */
	public function toAgeInYears()
	{
		$this->date->setTimezone( self::$utc );

		$age	=	$this->diff( 'now' )->y;

		$this->date->setTimezone( $this->tz );

		return (int) $age;
	}
}
