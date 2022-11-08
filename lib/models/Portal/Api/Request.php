<?php

namespace Api\Model\Portal\Api;

class Request {

	private $url;
	private $method = 'GET';
	private $parameters = [];
	private $headers = [];
	private $options = [];

	public function __construct() {
		$this->setOptions([
			CURLOPT_USERAGENT => 'ConsoleSyncBot',
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,

			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 300
		]);
	}

	public function setUrl($url) {
		$this->url = $url;
	}

	public function setMethod($method) {
		$this->method = $method;
	}

	public function setParameters(array $parameters) {
		$this->parameters = $parameters;
	}

	public function setHeaders(array $headers) {
		foreach ($headers as $k => $v)
			$this->headers[$k] = $v;
	}

	public function setOptions(array $options) {
		foreach ($options as $k => $v)
			$this->setOption($k, $v);
	}

	public function setOption($name, $value) {
		$this->options[$name] = $value;
	}

	public function send() {
		$requestUrl = $this->url;
		$options = $this->options;
		switch ($this->method) {
			case 'GET':
				if ($this->parameters)
					$requestUrl .= '?' . http_build_query($this->parameters);
				break;
			case 'POST':
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_POSTFIELDS] = json_encode($this->parameters);
				break;
			default:
				throw new Exception('Method invalid');
		}

		//var_dump($requestUrl);exit;
		$headers = $this->headers;
		$headers[] = 'Api-Access-Key: PEtQOkwmJyY5NiVgIn18eXYrJmdUY3JMX3B';
		$headers[] = 'Accept-Version: 2.0.0';
		$options[CURLOPT_HTTPHEADER] = $headers;
		$ch = curl_init($requestUrl);
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		//var_dump($requestUrl, $result);exit;
		$response = new Response();
		if (empty($result)) {
			$errno = curl_errno($ch);
			$error = curl_error($ch);
			curl_close($ch);
			$response->setException("Curl returned error $errno: $error\n"
				. var_export($requestUrl, true));
		} else {
			$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$response->setHttpCode($httpCode);
			curl_close($ch);
			if ($httpCode >= 500) {
				// do not wat to DDOS server if something goes wrong
				sleep(10);
			} else if ($httpCode !== 200) {
				$response->setContent($result);
				if ($httpCode === 401) {
					$response->setException('Invalid access token provided');
				} else {
					$response->setException('Request has failed with error:'
						. var_export($requestUrl, true)
						. var_export($response, true));
				}
			} else {
				$response->setContent($result);
			}
		}
		return $response;
	}

}