<?php
abstract class Autoload{
	
	private static $pathDefault = [];
	private static $paths = [];
	
	public static function setIncludePath($path) {
		set_include_path($path);
	}
	
	public static function addPath($path, $class, $replace = null) {
		self::$paths[] = [$path, $class, $replace];
	}
	
	public static function setDefaultPath($path) {
		self::$pathDefault = $path;
	}
	
	public static function init($includePath = null) {
		if ($includePath)
			self::setIncludePath($includePath);
		spl_autoload_register(function($class) {
			foreach (Autoload::$paths as $path) {
				if ($path[1] && 0 !== strpos($class, $path[1]))
						continue;
				$cls = $class;
				if ($path[2] !== null)
					$cls = str_replace($path[1], $path[2], $cls);
				return Autoload::include($cls, $path[0], true);
			}
			return Autoload::include($class, Autoload::$pathDefault, false);
		});
	}
	
	public static function icludePath($path) {
		if (($dh = opendir($path))) {
			while (($file = readdir($dh)) !== false) {
				if (false !== strpos($file, '.php'))
					include $path . DIRECTORY_SEPARATOR . $file;
			}
			closedir($dh);
		}
	}
	
	private static function include($class, $path, $exception = false) {
		//$parts = explode('\\', $class);
		$filename = $path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
		if (!file_exists($filename)) {
			if (!$exception)
				return false;
			//var_dump($path, $class, 0 !== strpos($class, $path[1]));
			throw new Exception('Class (' . $class . ') not found');
		}
		require $filename;
		return true;
	}
	
}