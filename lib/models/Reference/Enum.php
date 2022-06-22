<?php
namespace Api\Model\Reference;

use Api;

class Enum extends Api{
	
	private $currentGroup;

	protected function init() {
		$this->_table = 'ref_enums';
		$this
			->addAttribute('group', 'string', array('required' => true))
			->addAttribute('name', 'string', array('required' => true))
			->addAttribute('description', 'string', array('notnull' => false));
	}
	
	public function setCurrentGroup($group) {
		$this->currentGroup = $group;
	}
	
	protected function initSettings($settings) {
		if ($this->currentGroup)
			$settings->group = $this->currentGroup;
		$settings->enableQuicksearch('name', 'description', 'group');
	}
	
	protected function beforeWrite() {
		if ($this->currentGroup)
			$this->group = $this->currentGroup;
	}
	
	public function getGroups() {
		return ['mail' => 'mail'];
	}

}