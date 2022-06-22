<?php
namespace Api\Model\Request;

class Response extends AbstractItem{
	
	protected function init() {
		$this->_table = 'response';
		$this
				->addAttribute('helper_id', 'number')
				->addAttribute('status', 'number')
				->addAttribute('created');
		parent::init();
	}
	
}
