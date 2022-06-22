<?php
namespace Api\Model\Request\Response;

use Api;
use Db;

abstract class AbstractItem{
	
	protected $response;
	protected $identifiers = array();
	protected $data = array();
	
	public function __construct($response) {
		$this->response = $response;
	}
	
	protected function _get(array $identifiers, $column = null) {
		$q = Db::from($this->table, $column)
				->where('response_id=' . $this->response->id);
		foreach ($identifiers as $k => $v) {
			$q->where($k . '=?' . $v);
		}
		$q = $q->query();
		return $column ? $q->fetchColumn() : $q->fetchRow();
	}
	
	protected function _write(array $data, array $identifiers) {
		if (false === $this->get($identifiers))
			Db::update($this->table, $data, array_merge($identifiers, array('response_id' => $this->response->id)));
		else
			Db::insert($this->table, array_merge($data, $identifiers, array('response_id' => $this->response->id)));
	}
	
}