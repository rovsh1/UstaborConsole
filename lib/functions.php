<?php
use Stdlib\DateTime;
use Translation\Util as TranslationUtil;

function lang() {
	return call_user_func_array(array('Translation', 'translate'), func_get_args());
}

function getWordDeclension($number, $variants) {
	return TranslationUtil::getWordDeclension($number, $variants);
}

function getNumberDeclension($number, $variants) {
	return TranslationUtil::getNumberDeclension($number, $variants);
}

function transliterate($string, $space = ' ') {
	return TranslationUtil::transliterate($string, $space);
}

function format($value, $format) {
	return Format::exec($value, $format);
}

function now() {
	return DateTime::now();
}

function CurrentDate() {
	return now();
}

function Year($date = null) {
	return DateTime::factory($date)->getYear();
}

function Month($date = null) {
	return DateTime::factory($date)->getMonth();
}

function Day($date = null) {
	return DateTime::factory($date)->getDay();
}

function Hour($date = null) {
	return DateTime::factory($date)->getHour();
}

function Minute($date = null) {
	return DateTime::factory($date)->getMinute();
}

function Second($date = null) {
	return DateTime::factory($date)->getSecond();
}

function DayOfYear($date = null) {
	return (int)DateTime::factory($date)->format('z');
}

function WeekOfYear($date = null) {
	return (int)DateTime::factory($date)->format('W');
}

function BegOfYear($date = null) {
	$datetime = DateTime::factory($date);
	$datetime
		->setDate($datetime->getYear(), 1, 1)
		->setTime(0, 0, 0);
	return $datetime;
}

function EndOfYear($date = null) {
	$datetime = DateTime::factory($date);
	$datetime
		->setDate($datetime->getYear(), 12, 31)
		->setTime(23, 59, 59);
	return $datetime;
}

function BegOfQuarter($date = null) {
	
}

function EndOfQuarter($date = null) {
	
}

function BegOfMonth($date = null) {
	$datetime = DateTime::factory($date);
	$datetime->modify('first day of this month');
	$datetime->setTime(0, 0, 0);
	return $datetime;
}

function EndOfMonth($date = null) {
	$datetime = DateTime::factory($date);
	$datetime->modify('last day of this month');
	$datetime->setTime(23, 59, 59);
	return $datetime;
}

function AddMonth($date = null) {
	$datetime = DateTime::factory($date);
	$datetime->modify('+1 month');
	return $datetime;
}

function WeekDay($date = null) {
	return DateTime::factory($date)->getWeekDay();
}

function BegOfWeek($date = null) {
	$datetime = DateTime::factory($date);
	$d = $datetime->getWeekDay();
	if ($d > 1) {
		$datetime->modify('-' . ($d - 1) . ' day');
	}
	$datetime->setTime(0, 0, 0);
	return $datetime;
}

function EndOfWeek($date = null) {
	$datetime = DateTime::factory($date);
	$d = $datetime->getWeekDay();
	if ($d < 7) {
		$datetime->modify('+' . (7 - $d) . ' day');
	}
	$datetime->setTime(23, 59, 59);
	return $datetime;
}

function BegOfHour($date = null) {
	
}

function EndOfHour($date = null) {
	
}

function BegOfMinute($date = null) {
	
}

function EndOfMinute($date = null) {
	
}

function BegOfDay($date = null) {
	$datetime = DateTime::factory($date);
	$datetime->setTime(0, 0, 0);
	return $datetime;
}

function EndOfDay($date = null) {
	$datetime = DateTime::factory($date);
	$datetime->setTime(23, 59, 59);
	return $datetime;
}

function getPost($key = null, $default = null) {
	if (null === $key) {
		return $_POST;
	}
	return (isset($_POST[$key])) ? $_POST[$key] : $default;
}

function getQuery($key = null, $default = null) {
	if (null === $key) {
		return $_GET;
	}
	return (isset($_GET[$key])) ? $_GET[$key] : $default;
}

function debug($var, $exit = true) {
	return AppDebug::debug($var, $exit);
}