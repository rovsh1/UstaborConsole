<?php
namespace Api\Model\Request;

class Note extends AbstractItem{
	
	protected function init() {
		$this->_table = 'request_notes';
		$this
				->addAttribute('helper_id', 'number')
				->addAttribute('text', 'string');
		parent::init();
	}
	
	protected function initSettings($settings) {
		parent::initSettings($settings);
	}
	
}
