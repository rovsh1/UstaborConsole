<?php
namespace Console;

use Exception;

class Handler{
	
	public static function run(array $argv = null) {
		if (null === $argv) {
			$argv = $_SERVER['argv'];
		}
		
		// strip the application name
		array_shift($argv);
		
		$tokens = $argv;
		
		$command = array_shift($tokens);
		
		try {
			self::command($command, $tokens);
		} catch (Exception $ex) {
			self::out($ex->getMessage());
			exit;
		}
	}
	
	public static function toCamelCase($name) {
		$array = [];
		$args = explode('-', $name);
		$array[] = array_shift($args);
		foreach ($args as $arg) {
			$array[] = ucfirst($arg);
		}
		return implode('', $array);
	}
	
	public static function command($token, $tokens) {
		//action name
		$args = explode(':', $token);
		if (count($args) === 1)
			$action = 'main';
		else
			$action = self::toCamelCase(array_pop($args));
		//called class
		$cls = [];
		foreach ($args as $arg)
			$cls[] = ucfirst($arg);
		$commandClass = 'Console\\Command\\' . implode('\\', $cls);
		if (!class_exists($commandClass, true)) {
			throw new Exception('command "' . $clsPath . '" not exists');
		}
		$api = new $commandClass($tokens);
		
		if (!method_exists($api, $action))
			throw new Exception('action not exists');
		$result = $api->$action();
	}
	
	public static function out($msg) {
		echo $msg . "\n";
	}
	
}