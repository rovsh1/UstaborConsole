<?php
namespace Translation;

abstract class Calendar{
	
	private static $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	private static $monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	private static $weekDays = ['Mondey', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
	private static $weekDaysShort = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'St', 'Su'];
	private static $cache = array();

	public static function getMonth($index) {
		$months = self::getMonths();
		return $months[$index - 1];
	}

	public static function getMonths() {
		return self::_get('months', self::$months);
	}

	public static function getMonthsShort() {
		return self::_get('months', self::$monthsShort);
	}

	public static function getWeekDay($day) {
		$days = self::getWeekDays();
		return $days[$day - 1];
	}

	public static function getWeekDays() {
		return self::_get('weekdays', self::$weekDays);
	}

	public static function getWeekDayShort($index) {
		$days = self::getWeekDaysShort();
		return $days[$index - 1];
	}

	public static function getWeekDaysShort() {
		return self::_get('weekdays', self::$weekDaysShort);
	}
	
	private static function _get($cache, $array, $path = '') {
		if (!isset(self::$cache[$cache])) {
			self::$cache[$cache] = array();
			foreach ($array as $n) {
				self::$cache[$cache][] = lang($path . $n);
			}
		}
		return self::$cache[$cache];
	}
	
}