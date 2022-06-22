<?php

namespace Api\Model\Portal\Api;

abstract class AbstractApi {

	protected $apiUrl;

	public function __construct($portal) {
		if ($portal->domain === 'ustabor.uz')
			$this->apiUrl = 'https://sync.' . $portal->domain . '/';
		else
			$this->apiUrl = 'https://api.' . $portal->domain . '/';
	}

	protected function getRequest($url, $method = 'GET', array $parameters = []) {
		$request = new Request();
		$request->setUrl($this->apiUrl . $url);
		$request->setMethod($method);
		$request->setParameters($parameters);
		return $request;
	}

}
