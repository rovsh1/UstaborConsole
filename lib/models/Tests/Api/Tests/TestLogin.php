<?php
namespace Api\Model\Tests\Api\Tests;

use Exception;

class TestLogin extends AbstractTest{
	
	protected $apiUrl = 'login/';
	protected $method = 'POST';
	
	protected function init() {
		parent::init();
		$this->setData([
			'login' => $this->variable('customer_login'),
			'password' => $this->variable('customer_password')
		]);
	}
	
	protected function tests() {
		if ($this->test('Check auth token', $this->result->has('token'))) {
			$this->variable('auth_token', $this->result->token);
		} else {
			throw new Exception('Auth token undefined');
		}
	}
	
}