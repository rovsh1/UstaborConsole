<?php
namespace Api\Model\User\Access\Group;

use Api;

class Rule{
	
	protected $id = null;
	protected $model = null;
	protected $action = null;
	protected $flag = false;
	protected $alias = [];
	
	public function __construct($model, $action, $flag = false) {
		if ($model instanceof Api) {
			$model = strtolower($model->getModelName());
		}
		$this->id = $model . '_' . $action;
		$this->model = $model;
		$this->action = $action;
		$this->flag = (bool)$flag;
	}
	
	public function __get($name) {
		return (isset($this->$name) ? $this->$name : null);
	}
	
	public function match($model, $action) {
		if ($model instanceof Api) {
			$model = strtolower($model->getModelName());
		}
		return $model === $this->model && $action === $this->action;
	}
	
	public function setFlag($flag) {
		$this->flag = $flag;
	}
	
}