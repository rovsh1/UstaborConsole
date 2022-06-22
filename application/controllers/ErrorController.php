<?php

class ErrorController extends InitController {

	protected function initAuthUser() {
		if (!parent::initAuthUser()) {
			$this->layout = 'layout-auth';
		}
		return true;
	}

	public function indexAction() {
		return $this->errorAction();
	}

	public function errorAction() {
		$this->getResponse()->renderExceptions(false);
		$this->errors = $this->_getParam('error_handler');
		switch ($this->errors->type) {
			case Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				$code = 404;
				break;
			default:
				$code = $this->errors->exception->getCode();
				break;
		}

		if (in_array($code, [403, 404])) {
			$code = 404;
			$this->code = $code;
			//$this->page->findByAttribute('dir', $code);
			$this->getResponse()->setHttpResponseCode($code);
			$this->getHelper('ViewRenderer')->setScriptAction($code);
		} else {
			throw $this->errors->exception;
		}
		$this->contentClass = 'error';
		$this->page->setTitle('Страница не найдена');
		$this->page->getHead()->addStyle('error.css');
	}

}