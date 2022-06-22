<?php
namespace Api\Model\Tests\Api;

use Exception;

class Result{
	
	private $data;
	protected $exception;
	protected $status = 'FAILED';
	protected $tests = [];
	
	public function __get($name) {
		switch ($name) {
			case 'data': return $this->data;
		}
		return $this->has($name) ? $this->data->$name : null;
	}
	
	public function setStatus($status) {
		$this->status = $status;
	}
	
	public function setData($data) {
		$this->data = $data;
	}
	
	public function setException(Exception $e) {
		$this->exception = $e;
	}
	
	public function has($name) {
		return isset($this->data->$name) && !empty($this->data->$name);
	}
	
	public function test($name, $flag) {
		$this->tests[$name] = $flag;
		return $flag;
	}
	
	public function isValid() {
		return !$this->exception;
	}
	
	public function toArray() {
		return [
			'status' => $this->status,
			'data' => $this->data,
			'tests' => $this->tests,
			'error' => $this->exception ? $this->exception->getMessage() : null
		];
	}
	
}