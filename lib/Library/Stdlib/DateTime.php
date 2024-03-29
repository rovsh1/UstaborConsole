<?php
namespace Stdlib;

use DateTime as BaseDateTime;
use DateTimeZone as BaseDateTimeZone;
use Stdlib\DateTime\Format;
use Exception;

class DateTime extends BaseDateTime{
	
	private static $formats = [];
	
	public static function init($serverTimezone = null, $clientTimezone = null) {
		DateTimezone::setServer($serverTimezone ? : date_default_timezone_get());
		if ($clientTimezone)
			DateTimezone::setClient($clientTimezone);
		Format::init();
	}
	
	public static function factory($date, $timezone = null) {
		if ($timezone && is_string($timezone))
			$timezone = DateTimeZone::get($timezone);
		if ($date instanceof self) {
			$date = clone $date;
			if ($timezone)
				$date->setTimezone($timezone);
			return $date;
		} elseif (is_int($date)) {
			$timestamp = $date;
			$date = now();
			$date->setTimestamp($timestamp);
			return $date;
		}
		return new self($date, $timezone);
	}
	
	public static function now() {
		return new self();
	}

	public static function setFormat($alias, $format) {
		$regexp = '/' . str_replace(array('.', '?', '|'), array('\\.', '\\?', '\\|'), $alias) . '/';
		self::$formats[$alias] = [$regexp, $format];
	}
	
	public static function getFormat($format) {
		if (isset(self::$formats[$format]))
			return self::$formats[$format][1];
		return $format;
	}
	
	public static function serverDate($datetime = null) {
		return self::factory($datetime, DateTimezone::getServer())->format('server.date');
	}
	
	public static function serverTime($datetime = null) {
		return self::factory($datetime, DateTimezone::getServer())->format('server.time');
	}
	
	public static function serverDatetime($datetime = null) {
		return self::factory($datetime, DateTimezone::getServer())->format('server.datetime');
	}
	
	public function __construct($time = 'now', BaseDateTimeZone $timezone = null) {
		if (null === $timezone)
			$timezone = DateTimezone::getClient();
		parent::__construct($time, $timezone);
	}
	
	public function format($format):string {
		$datetime = $this;
		foreach (self::$formats as $name => $f) {
			$callback = $f[1];
			if (is_callable($callback)) {
				$format = preg_replace_callback($f[0], function ($matches) use ($name, $datetime, $callback) {
					return call_user_func($callback, $datetime);
				}, $format);
			} else {
				$format = preg_replace($f[0], $callback, $format);
			}
		}
		return parent::format($format);
	}
	
	public function formatTime() {
		return parent::format('time');
	}
	
	public function formatDate() {
		return parent::format('date');
	}
	
	public function formatDatetime() {
		return parent::format('datetime');
	}
	
	public function getYear() {
		return (int)$this->format('Y');
	}
	
	public function getMonth() {
		return (int)$this->format('n');
	}
	
	public function getDay() {
		return (int)$this->format('j');
	}
	
	public function getWeekDay() {
		return (int)$this->format('N');
	}
	
	public function getHour() {
		return (int)$this->format('H');
	}
	
	public function getMinute() {
		return (int)$this->format('i');
	}
	
	public function getSecond() {
		return (int)$this->format('s');
	}
	
}