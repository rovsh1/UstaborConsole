<?php
abstract class AppConfig{
	
	private static $data = [];

	public static function fromINI($filename) {
		if (is_file($filename)) {
			$data = parse_ini_file($filename, true, INI_SCANNER_TYPED);
			if ($data) {
				foreach ($data as $k => $v) {
					self::set($k, $v);
				}
			}
		} elseif (is_dir($filename)) {
			if ($handle = opendir($filename)) {
				while (false !== ($entry = readdir($handle))) {
					if (substr($entry, -4) === '.ini')
						self::fromINI($filename . '/' . $entry);
				}
				closedir($handle);
			}
		} else {
			
		}
	}
	
	public static function set($path, $value) {
		self::$data[$path] = $value;
	}
	
	public static function get($path, $default = null) {
		$data = self::$data;
		$dp = [];
		$paths = explode('.', $path);
		while ($paths) {
			$dp[] = array_shift($paths);
			$s = implode('.', $dp);
			if (isset($data[$s])) {
				$data = $data[$s];
				$dp = [];
			} elseif (!$paths) {
				return $default;
			}
		}
		return $data ? : $default;
	}
	
	public static function init(array $paths) {
		foreach ($paths as $path => $callable) {
			if (($value = self::get($path))) {
				call_user_func($callable, $value);
			}
		}
	}
	
}