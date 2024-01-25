<?php
namespace Api\Util\Settings;

use Translation\Util as TranslationUtil;

class Quicksearch{
	
	const BOUND_LEFT = 'left';
	const BOUND_RIGHT = 'right';
	const BOUND_BOTH = 'both';
	
	protected $paramName = 'quicksearch';
	protected $bounds = self::BOUND_BOTH;
	protected $columns = array();
	protected $enabled = false;
	protected $value = null;
	
	public function enable() {
		$this->enabled = true;
		return $this;
	}
	
	public function isEnabled() {
		return $this->enabled && !$this->isEmpty() && !empty($this->columns);
	}
	
	public function isEmpty() {
		return empty($this->value);
	}
	
	public function setParamName($name) {
		$this->paramName = $name;
		return $this;
	}
	
	public function getParamName() {
		return $this->paramName;
	}
	
	public function setBounds($bounds) {
		$this->bounds = $bounds;
		return $this;
	}
	
	public function getBounds() {
		return $this->bounds;
	}
	
	public function setColumns($columns) {
		$this->columns = $columns;
		return $this;
	}
	
	public function getColumns() {
		return $this->columns;
	}
	
	public function setValue($value) {
		$this->value = $value;
		return $this;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function getValues() {
		return TranslationUtil::getSpellVariants($this->value);
	}
	
	public function __toString() {
		return (string)$this->value;
	}
	
}