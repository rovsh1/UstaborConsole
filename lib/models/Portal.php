<?php
/**
 * Раздел сайта
 */
namespace Api\Model;

use Api;

class Portal extends Api{
	
	public static $subdomains = ['www', 'auto', 'tech', 'home', 'materials'];
	
	protected function init() {
		$this->_table = 'site_portals';
		$this
			->addAttribute('name', 'string', ['required' => true, 'locale' => true, 'length' => 255])
			->addAttribute('domain', 'string', ['required' => true, 'length' => 100])
			->addAttribute('enabled', 'boolean', [])
		;
	}
	
	protected function initSettings($settings) {
		if (!$settings->hasParam('enabled'))
			$settings->filter('enabled=1');
	}
	
	public function getApi($name) {
		$cls = 'Api\Model\Portal\Api\\' . $name;
		return new $cls($this);
	}
	
}