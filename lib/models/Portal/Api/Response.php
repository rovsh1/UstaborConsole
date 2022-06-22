<?php

namespace Api\Model\Portal\Api;

use Exception;

class Response {

	private $httpCode;
	private $exception;
	private $content;
	private $result;
	private $data = [];

	public function __set($name, $value) {
		$this->data[$name] = $value;
	}

	public function __get($name) {
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}

	public function setHttpCode($httpCode) {
		$this->httpCode = $httpCode;
	}

	public function getHttpCode() {
		return $this->httpCode;
	}

	public function setContent($content) {
		$this->content = $content;
	}

	public function getContent() {
		return $this->content;
	}

	public function getResult() {
		if (null !== $this->result)
			return $this->result;

		$data = json_decode($this->content) ?: false;
		if (is_object($data)) {
			$status = $data->status;
			if ($status && $status->code === 'ok')
				return $this->result = $data->result;

			$this->exception = new Exception($status->message);

			return $this->result = false;
		}

		return $this->result = $data;
	}

	public function setException($exception) {
		if (is_string($exception))
			$exception = new Exception($exception);
		$this->exception = $exception;
	}

	public function getException() {
		return $this->exception;
	}

	public function hasException() {
		$this->getResult();
		return (bool)$this->exception;
	}

}