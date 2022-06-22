<?php
namespace Api\Model\Portal\Api;

class Sync extends AbstractApi{
	
	protected function call($url, array $parameters = []) {
		$request = $this->getRequest('sync/' . $url, 'POST', $parameters);
		return $request->send();
	}
	
	public function syncConstants(array $parameters = []) {
		return $this->request('constants', $parameters);
	}
	
	public function syncTranslation(array $parameters = []) {
		return $this->call('translation', $parameters);
	}
	
}