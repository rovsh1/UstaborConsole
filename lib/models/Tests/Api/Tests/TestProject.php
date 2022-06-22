<?php
namespace Api\Model\Tests\Api\Tests;

use Exception;

class TestProject extends AbstractTest{
	
	protected $apiUrl = 'project/:id/';
	
	protected function init() {
		$projectId = $this->variable('project_id');
		if (!$projectId)
			throw new Exception('project Id undefined');
		$this->apiUrl = str_replace(':id', $projectId, $this->apiUrl);
		parent::init();
	}
	
	protected function tests() {
		$this->test('Check data format', $this->result->data && isset($this->result->data->id));
	}
	
}