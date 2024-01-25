<?php
require_once 'Library/Controller/Response/Http.php';


/**
 * Zend_Controller_Response_Http
 *
 * HTTP response for controllers
 *
 * @uses Zend_Controller_Response_Abstract
 * @package Zend_Controller
 * @subpackage Response
 */
class Controller_Response_Default extends Controller_Response_Http{

	protected $HTTP_ACCEPT_ENCODING = true;

	public function sendResponse() {
        $this->sendHeaders();
        if ($this->isException() && $this->renderExceptions()) {
            $exceptions = '';
            foreach ($this->getException() as $e) {
                $exceptions .= $e->__toString() . "\n";
            }
            echo $exceptions;
            return;
        }

        $this->outputBody();
    }

	protected static function prepareContent($content) {
		$content = preg_replace_callback('/{{([a-z0-9_]+):([^}]+)}}/i', function($matches) {
			return call_user_func($matches[1], $matches[2]);
		}, $content);
		return $content;

		//preg_match_all('/{lang:(.*)}/U', $content, $m, PREG_SET_ORDER);
		//var_dump($m);exit;
	}

	public function outputBody() {
		$body = self::prepareContent(implode('', $this->_body));
		$length = strlen($body);
		$this->setRawHeader("Content-Length: " . $length);
		$range = $this->request->getHeader('Range'); //bytes=100-
		if ($range && preg_match('/(\w+)=(\d+)-(\d*)/', $range, $m)) {
			$this->setRawHeader('Accept-Ranges: ' . $m[1]);
			$this->setRawHeader('Content-Range: ' . $m[1] . ' ' . $m[2] . '-' . ($m[3] ? $m[3] : ($length - 1)) . '/' . $length);
		}
		$this->sendHeaders();
        echo $body;
    }
}