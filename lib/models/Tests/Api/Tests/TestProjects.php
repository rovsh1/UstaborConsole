<?php
namespace Api\Model\Tests\Api\Tests;

class TestProjects extends AbstractReference{
	
	protected $apiUrl = 'project/search/';
	
	protected function tests() {
		parent::tests();
		$testProject = null;
		if (is_array($this->result->data))
			foreach ($this->result->data as $project) {
				if (isset($project->ad))
					continue;
				$testProject = $project;
				break;
			}
		if ($this->test('Get first project id', $testProject)) {
			$this->variable('project_id', $testProject->id);
		}
	}
	
}