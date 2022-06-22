<?php

namespace Api\Model\Tests\Api;

use Exception;

class Handler {

	protected $params = [
		'url' => 'http://api.bildrlist.com',
		'version' => 'v2',
		'sid' => 1,
		'lang' => 'ru',
		'customer_login' => 'apitest@ustabor.uz',
		'customer_password' => '123456'
	];
	
	public function __construct(array $params = []) {
		foreach ($params as $k => $v)
			$this->$k = $v;
	}

	public function __get($name) {
		if ($name === 'params')
			return $this->params;
		return isset($this->params[$name]) ? $this->params[$name] : null;
	}

	public function __set($name, $value) {
		$this->params[$name] = $value;
	}

	public function run($test) {
		return self::getTest($test)->run();
	}
	
	public function getTest($name) {
		$cls = __NAMESPACE__ . '\Tests\Test' . ucfirst($name);
		if (!class_exists($cls, true)) {
			throw new Exception('Test "' . $name . '" not found');
		}
		return new $cls($this);
	}

	public function getTests() {
		$tests = [];
		$path = __DIR__ . '/Tests';
		if (($handle = opendir($path))) {
			while (false !== ($entry = readdir($handle))) {
				if (strpos($entry, 'Test') !== 0)
					continue;
				$name = str_replace(['Test', '.php'], '', $entry);
				$tests[] = $this->getTest($name);
			}
			closedir($handle);
		}
		return $tests;
	}

}
