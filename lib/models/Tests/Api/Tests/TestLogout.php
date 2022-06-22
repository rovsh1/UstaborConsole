<?php
namespace Api\Model\Tests\Api\Tests;

class TestLogout extends AbstractTest{
	
	protected $apiUrl = 'logout/';
	
	protected function init() {
		parent::init();
		$this->authentication();
	}
	
	protected function tests() {
		$this->test('Check result', $this->result->data === true);
	}
	
}