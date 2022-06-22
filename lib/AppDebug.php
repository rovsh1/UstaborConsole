<?php
use Http\Util as HttpUtil;

abstract class AppDebug{
	
	public static function phpinfo() {
		phpinfo();
	}

	public static function debug($var, $exit = true) {
		if (!(isset($_GET['test']) || isset($_GET['debug']))) return;
		var_dump($var);
		if ($exit) exit;
	}

	public static function getsize($var) {
		if (is_string($var)) {
			return strlen($var);
		} elseif (is_array($var)) {
			$size = 0;
			foreach ($var as $v) {
				$size += self::getsize($v);
			}
			return $size;
		} else {
			return 1;
		}
	}
	
	public static function log($data = null) {
		if (null === $data) {
			$data = [
				'host' => $_SERVER['HTTP_HOST'],
				'request' => $_REQUEST,
				'cookie' => $_COOKIE,
				'files' => $_FILES
			];
			if (defined('UserId')) {
				$data['user_id'] = UserId;
			}
			$data['headers'] = HttpUtil::getHeaders();
		}
		Db::insert('api_log', [
			'method' => $_SERVER['REQUEST_METHOD'],
			'uri' => $_SERVER['REQUEST_URI'],
			'request' => print_r($data, true),
			//'response' => $responseSize,
			'ip' => HttpUtil::getClientIp(),
			'user_agent' => HttpUtil::getUserAgent()
		]);
	}

}