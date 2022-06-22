<?php
namespace Api\Model\Tests\Api\Tests;

class TestOrder extends AbstractTest{
	
	protected $apiUrl = 'user/order/';
	protected $method = 'POST';
	
	protected function init() {
		parent::init();
		$this->setData([
			'type' => 1,
			'username' => 'apitest',
			'phone' => '123456789',
			'service' => 1,
			'text' => 'test text',
			'address' => 'test address'
		]);
	}
	
	protected function tests() {
		$this->test('Check order sent', $this->result->data === true);
	}
	
}