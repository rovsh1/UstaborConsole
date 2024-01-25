<?php

namespace Api\Model\Portal\Api;

class Report extends AbstractApi {

    public function call($url, array $parameters = []) {
		//$filename = TEMP_PATH . '/' . str_replace('/', '_', trim($url, '/')) . '.csv';
		unset($parameters['portal_id']);
		$request = $this->getRequest('sync/' . $url, 'GET', $parameters);
		if ($parameters['action'] === 'finish') {
			$filename = str_replace('/', '_', trim($url, '/')) . '.csv';
			$tempnam = tempnam(TEMP_PATH, 'csv');
			$fp = fopen($tempnam, 'w+');
			$request->setOption(CURLOPT_FILE, $fp);
			$response = $request->send();
			$response->filename = $filename;
			$response->tempnam = $tempnam;
		} else {
			$response = $request->send();
		}
		$response->url = $url;
		return $response;
	}

    public function report(string $signature, array $parameters): Response
    {
        return $this->call("report/$signature", $parameters);
    }

	public function masters(array $parameters) {
		return $this->call('report/masters', $parameters);
	}

	public function customers(array $parameters) {
		return $this->call('report/customers', $parameters);
	}

	public function promotions(array $parameters) {
		return $this->call('report/master-promotions', $parameters);
	}

	public function masterContactsUnique(array $parameters) {
		return $this->call('report/master-contacts-unique', $parameters);
	}

	public function masterContactsBalance(array $parameters) {
		return $this->call('report/master-contacts-balance', $parameters);
	}

	public function masterWithoutProjects(array $parameters) {
		return $this->call('report/master-without-projects', $parameters);
	}

	public function masterStatistics(array $parameters) {
		return $this->call('report/master-statistics', $parameters);
	}

    public function masterPaymentStatistics(array $parameters) {
        return $this->call('report/master-payment-statistics', $parameters);
    }

	public function customerStatistics(array $parameters) {
		return $this->call('report/customer-statistics', $parameters);
	}

	public function request1(array $parameters) {
		return $this->call('report/request-1', $parameters);
	}

	public function request2(array $parameters) {
		return $this->call('report/request-2', $parameters);
	}

	public function request3(array $parameters) {
		return $this->call('report/request-3', $parameters);
	}

	public function request4(array $parameters) {
		return $this->call('report/request-4', $parameters);
	}

	public function request5(array $parameters) {
		return $this->call('report/request-5', $parameters);
	}

	public function request6(array $parameters) {
		return $this->call('report/request-6', $parameters);
	}

	public function request7(array $parameters) {
		return $this->call('report/request-7', $parameters);
	}

	public function request8(array $parameters) {
		return $this->call('report/request-8', $parameters);
	}

	public function categoryPromotions(array $parameters) {
		return $this->call('report/category-promotions', $parameters);
	}

	public function categoryMasters(array $parameters) {
		return $this->call('report/category-masters', $parameters);
	}

	public function categoryMarketing(array $parameters) {
		return $this->call('report/category-marketing', $parameters);
	}

	public function categoryRequests(array $parameters) {
		return $this->call('report/category-requests', $parameters);
	}

}