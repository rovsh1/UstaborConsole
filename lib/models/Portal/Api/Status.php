<?php

namespace Api\Model\Portal\Api;

class Status extends AbstractApi {

	protected function call($url, array $parameters = []) {
		$request = $this->getRequest($url, 'GET', $parameters);
		$response = $request->send();
		return $response->getResult();
	}

	protected function checkDomain() {
		$request = $this->getRequest();
		$request->setOptions([
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_CONNECTTIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER => 1
		]);
	}

	protected function request($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		// домен, на который осуществляется отправка
		// тестового запроса, работает через https
		// поэтому нужно добавить флаги для работы с ssl
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		// отправка запроса
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$response = curl_exec($ch);
		$result = new stdClass();
		$result->url = $url;
		$result->response_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
		$result->http_connectcode = curl_getinfo($ch, CURLINFO_HTTP_CONNECTCODE);
		$result->time_total = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
		$result->time_connect = curl_getinfo($ch, CURLINFO_CONNECT_TIME);
		$result->error = null;
		//$result->http_code = curl_getinfo($ch, CURLINFO_SSL_VERIFYRESULT);
		if (curl_errno($ch)) {
			$result->status = self::REQUEST_FAILED;
			$result->error = curl_errno($ch) . ':' . curl_error($ch);
		} else if (!$response)
			$result->status = self::REQUEST_FAILED;
		else
			$result->status = self::REQUEST_OK;
		//$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		curl_close($ch);
		return $result;//array(substr($response, $header_size), substr($response, 0, $header_size));
	}

	public function check($id, $url) {
		$result = $this->request($url);
		Db::insert('request_log', [
			'url_id' => $id,
			'status' => $result->status,
			'error' => $result->error,
			'response_code' => $result->response_code,
			'http_connectcode' => $result->http_connectcode,
			'time_total' => $result->time_total,
			'time_connect' => $result->time_connect
		]);
		return $result;
	}

	public function status() {
		return $this->call('system/status/');
	}

	public function backupLog() {
		return $this->call('system/backup/log/');
	}

	public function errorLog() {
		return $this->call('system/error/log/');
	}

	public function errorClear() {
		return $this->call('system/error/clear/');
	}

	public function backupList() {
		return $this->call('system/backup/list/');
	}

	public function robots() {
		return $this->call('system/robots/');
	}

	public function cronLog() {
		return $this->call('system/cron/log/');
	}

	public function cronTasks() {
		return $this->call('system/cron/tasks/');
	}

	public function configs() {
		return $this->call('system/configs/');
	}

}