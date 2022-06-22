<?php
namespace Console\Command\Monitor;

use Db;
use stdClass;

class Request{
	
	const URL_OK = 'ok';
	const URL_DOWN = 'down';
	const REQUEST_OK = 'ok';
	const REQUEST_FAILED = 'failed';

	protected function request($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		// домен, на который осуществляется отправка
		// тестового запроса, работает через https
		// поэтому нужно добавить флаги для работы с ssl
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

		// отправка запроса
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		curl_setopt($ch, CURLOPT_USERAGENT, 'ConsoleMonitorBot');
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Range: bytes=0-600',
			//'Accept-Encoding: gzip,deflate'
		]);
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
		} elseif (!$response)
			$result->status = self::REQUEST_FAILED;
		else
			$result->status = in_array($result->response_code, [200, 206]) ? self::REQUEST_OK : self::REQUEST_FAILED;
		$result->ok = $result->status === self::REQUEST_OK;
		$result->url_status = $result->ok ? self::URL_OK : self::URL_DOWN;
		//$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		curl_close($ch);
		return $result;//array(substr($response, $header_size), substr($response, 0, $header_size));
	}

	public function check($id, $url) {
		$result = $this->request($url);
		Db::insert('monitor_log', [
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

}