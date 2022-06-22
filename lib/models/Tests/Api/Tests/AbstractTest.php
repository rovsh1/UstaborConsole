<?php
namespace Api\Model\Tests\Api\Tests;

use Api\Model\Tests\Api\Result;
use Http\Curl;
use Http\Response;
use Exception;

abstract class AbstractTest extends Curl{
	
	protected $apiUrl = '';
	protected $handler;
	protected $response;
	
	public function __construct($handler) {
		$this->handler = $handler;
		$this->result = new Result();
		$this->response = new Response();
		parent::__construct();
	}
	
	public function __get($name) {
		switch ($name) {
			case 'url':
			case 'method':
			case 'response':
				return $this->$name;
			case 'name':
				return str_replace(__NAMESPACE__ . '\Test', '', get_class($this));
		}
		return isset($this->result->data->$name) ? $this->result->data->$name : null;
	}
	
	public function variable($name, $value = null) {
		if (null == $value)
			return $this->handler->$name;
		$this->handler->$name = $value;
	}
	
	public function isValid() {
		return $this->result->isValid();
	}
	
	public function run() {
		try{
			$this->init();
			$this->response = $this->send();
			if ($this->response->hasException()) {
				throw $this->response->getException();
			}
			if ($this->response->getHttpCode() === 200) {
				$content = $this->response->getContent('json');
				if (!$content) {
					throw new Exception('Invalid response format');
					//$this->result->setContent($response->getContent('json'));
					//var_dump($result->result);
				} elseif ($content->status->code === 'ok') {
					$this->result->setStatus('OK');
					$this->result->setData($content->result);
					$this->tests();
				} else {
					throw new Exception($content->status->message);
				}
			} else {
				throw new Exception('Response filed', $this->response->getHttpCode());
			}
		} catch (Exception $e) {
			$this->result->setException($e);
		}
		return $this->result;
	}
	
	public function toArray() {
		return [
			'name' => $this->name,
			'method' => $this->method,
			'url' => $this->url
		];
	}
	
	protected function authentication() {
		$authCode = $this->variable('auth_token');
		if (!$authCode)
			throw new Exception('auth code undefined');
		$this->setHeader('Authorization', 'auth=' . $authCode);
	}
	
	protected function init() {
		$this->setUrl($this->variable('url') . '/' . $this->variable('version') . '/' . $this->apiUrl);
		$this->setParameter('sid', $this->variable('sid'));
		$this->setParameter('lang', $this->variable('lang'));
	}
	
	protected function test($name, $flag) {
		return $this->result->test($name, $flag);
	}
	
	abstract protected function tests();
	
}