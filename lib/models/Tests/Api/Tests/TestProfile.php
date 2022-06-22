<?php
namespace Api\Model\Tests\Api\Tests;

class TestProfile extends AbstractTest{
	
	protected $apiUrl = 'profile/';


	protected function init() {
		parent::init();
		$this->authentication();
	}
	
	protected function tests() {
		$this->test('Check data', isset($this->result->data->id));
	}
	
}