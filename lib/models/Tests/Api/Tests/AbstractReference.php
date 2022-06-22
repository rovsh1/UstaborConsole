<?php
namespace Api\Model\Tests\Api\Tests;

abstract class AbstractReference extends AbstractTest{
	
	protected function init() {
		parent::init();
		$this->setParameter('limit', 5);
	}
	
	protected function tests() {
		$this->test('Check data format', $this->result->data && is_array($this->result->data));
	}
	
}