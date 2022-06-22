<?php
use Api\Model\Tests\Api\Handler as TestHandler;

class ApitestsController extends InitController{

	protected $_routes = [
		['/test/:name/', [], ['id' => '\w+']]
	];
	
	public function indexAction() {
		$this->page->setTitle($this->mainmenu->lang('apitests'));
		//$this->get('menu');
	}
	
	public function testAction() {
		$handler = new TestHandler($this->getRequest()->getQuery());
		$test = $handler->getTest($this->name);
		$result = $test->run();
		jsonResponse([
			'test' => $test->toArray(),
			'result' => $result->toArray(),
			'variables' => $handler->params,
			'info' => [
				'code' => $test->response->getHttpCode(),
				'total_tile' => $test->response->total_time
			]
			
		]);
	}

}