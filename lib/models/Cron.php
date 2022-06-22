<?php
namespace Api\Model;

use Api;

class Cron extends Api{
	
	protected function init() {
		$this->_table = 'cron';
		$this
			->addAttribute('portal_id', 'number', ['notnull' => false])
			->addAttribute('exec', 'string', ['required' => true])
			->addAttribute('name', 'string', ['required' => true])
			->addAttribute('period', 'string', ['notnull' => false])
			->addAttribute('enabled', 'boolean', ['default' => true])
		;
	}
	
}