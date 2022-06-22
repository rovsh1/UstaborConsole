<?php
namespace Api\Model\Portal\Api;

class Cron extends AbstractApi{
	
	protected function call($url, array $parameters = []) {
		$request = $this->getRequest('cron/' . $url, 'GET', $parameters);
		$response = $request->send();
		return $response->getResult();
	}
	
	public function run() {
		return $this->call('run');
	}
}