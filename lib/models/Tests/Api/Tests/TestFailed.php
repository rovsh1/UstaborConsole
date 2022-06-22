<?php
namespace Api\Model\Tests\Api\Tests;

class TestFailed extends AbstractTest{
	
	protected $apiUrl = 'notdefined/';
	
	protected function tests() {
		$this->test('Check data format', $this->data && is_array($this->data));
	}
	
}