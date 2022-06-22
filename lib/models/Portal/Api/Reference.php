<?php

namespace Api\Model\Portal\Api;

class Reference extends AbstractApi {

	protected function call($url, array $parameters = []) {
		$request = $this->getRequest('sync/' . $url, 'GET', $parameters);
		$response = $request->send();
		return $response->getResult();
	}

	public function getData() {
		return $this->call('reference/data');
	}

	public function getSites() {
		return $this->call('reference/sites');
	}

	public function getCountries() {
		return $this->call('reference/countries');
	}

	public function getCategories(array $parameters = []) {
		return $this->call('reference/categories', $parameters);
	}

	public function getTranslation(array $parameters = []) {
		return $this->call('reference/translation', $parameters);
	}

	public function getMasters(array $parameters = []) {
		return $this->call('reference/categories', $parameters);
	}

}