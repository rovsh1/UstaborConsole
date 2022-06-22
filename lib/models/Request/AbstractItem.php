<?php
namespace Api\Model\Request;

use Api;

abstract class AbstractItem extends Api{
	
	protected $request;
	
	protected function init() {
		$this->addAttribute('request_id', 'number');
	}
	
	protected function initSettings($settings) {
		if ($this->hasRequest()) {
			$settings->filter($this->table . '.request_id=' . (int)$this->request->id);
		}
	}
	
	protected function beforeWrite() {
		if ($this->isNew() && $this->hasRequest()) {
			$this->_set('request_id', $this->request->id);
		}
	}
	
	public function setRequest($request) {
		$this->request = $request;
		return $this;
	}
	
	public function hasRequest() {
		return $this->request && !$this->request->isEmpty();
	}
	
}