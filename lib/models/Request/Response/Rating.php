<?php
namespace Api\Model\Request\Response;

class Rating extends AbstractItem{
	
	protected $table = 'response_ratings';
	
	protected function init() {
		$this->identifiers = array('type');
	}
	
	public function set($types, $value = null) {
		if (is_array($types)) {
			foreach ($types as $type => $value) {
				$this->set($type, $value);
			}
		} else {
			$this->_write(array('value' => $value), array('type' => $types));
		}
	}
	
	public function get($type) {
		return $this->_get(array('type' => $type), 'value');
	}
	
}