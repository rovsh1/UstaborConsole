<?php
namespace Api\Model\Banlist;

use Form as BaseForm;
use Api\Model\User\Util as UserUtil;

class Form extends BaseForm{
	
	const SESSION_PRAM_NAME = 'form_hash';
	
	public function __construct($options = null) {
		parent::__construct($options);
		$this->id = 'form_' . $this->name;
		$this
			->addElement('hash', 'hidden', ['render' => false])
			->addElement('captcha', 'hidden');
	}
	
	public function submit($options = []) {
		if (!parent::submit($options))
			return false;
		$hash = $this->getElement('hash')->getValue();
		$captcha = $this->getElement('captcha')->getValue();
		if (!$hash || $hash !== self::getHash() || !empty($captcha)) {
			//die('ban');
			Log::add(\BANLIST_LOG::FORM_TRAP);
			return false;
		}
		return true;
	}
	
	public function getData() {
		$data = parent::getData();
		unset($data['captcha'], $data['hash']);
		return $data;
	}
	
	public static function getHash($set = false) {
		if ($set) {
			$hash = UserUtil::generatePassword(32);
			$_SESSION[self::SESSION_PRAM_NAME] = $hash;
			return $hash;
		} else if (isset($_SESSION[self::SESSION_PRAM_NAME])) {
			return $_SESSION[self::SESSION_PRAM_NAME];
		}
		return null;
	}
	
}