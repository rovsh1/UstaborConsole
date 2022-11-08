<?php
/**
 * Раздел сайта
 */
namespace Api\Model;

use Api;

class Site extends Api{
	
	protected function init() {
		$this->_table = 'sites';
		$this
			->addAttribute('name', 'string', array('required' => true, 'locale' => true, 'length' => 255))
			->addAttribute('menu_name', 'string', array('required' => true, 'locale' => true, 'length' => 100))
			//->addAttribute('domain', 'string', array('notnull' => false))
			->addAttribute('project_type', 'number', array())
		;
	}
	
	protected function initSettings($settings) {
	}
	
}