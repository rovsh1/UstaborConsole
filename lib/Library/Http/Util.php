<?php
namespace Http;

abstract class Util {

	/* Эта функция будет проверять, является ли посетитель роботом поисковой системы */
	public static function isSearchBot($botname = '') {
		if (!isset($_SERVER['HTTP_USER_AGENT']))
			return false;
		$bots = array(
			'bot',
			'slurp',
			'crawler',
			'spider',
			'curl',
			'facebook',
			'fetch',
		);
		foreach ($bots as $bot) {
			if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
				//$botname = $bot;
				return true;
			}
		}
		return false;
	}
	
	public static function getHeader($name) {

        // Try to get it from the $_SERVER array first
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (isset($_SERVER[$temp]) && $_SERVER[$temp])
            return $_SERVER[$temp];
		elseif (isset($_SERVER['REDIRECT_' . $temp]) && $_SERVER['REDIRECT_' . $temp])
			return $_SERVER['REDIRECT_' . $temp];

        // This seems to be the only way to get the Authorization header on
        // Apache
		$headers = self::getHeaders();
		$name = strtolower($name);
		foreach ($headers as $k => $v) {
			if (strtolower($k) == $name)
				return $v;
		}
        return false;
    }
	
	public static function getHeaders() {
		 if (function_exists('apache_request_headers'))
            return apache_request_headers();
        elseif (function_exists('getallheaders'))
			return getallheaders();
		return [];
	}

	public static function getUserAgent() {
		if (!isset($_SERVER['HTTP_USER_AGENT'])) return '';
		return $_SERVER['HTTP_USER_AGENT'];
	}

	public static function getClientIp($checkProxy = true) {
		$ip = null;
		if ($checkProxy && isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != null) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if ($checkProxy && isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != null) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

}
