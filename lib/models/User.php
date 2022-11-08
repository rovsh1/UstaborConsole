<?php
/**
 * Пользователь базовый
 */
namespace Api\Model;

require_once 'User/Enums.php';

use Api;
use Api\Model\User\Util;
use Api\Model\User\Params;
use Api\Model\Mail\Template as MailTemplate;

class User extends Api{
	
	protected $_role;
	private $params = null;
	
	public function __set($name, $value) {
		parent::__set($name, $value);
		switch ($name) {
			case 'image':
				if ($this->image && $this->image->isNew()) {
					$this->imageUpdated = true;
				}
				break;
			case 'password':
				$this->passwordTemp = $value;
				break;
		}
	}
	
	/**
	 * Очистить модель
	 * @return type
	 */
	public function reset() {
		$this->params = null;
		return parent::reset();
	}

	protected function init() {
		$this->_table = 'users';
		$this->foreignKey = 'user_id';
		$this
			->addAttribute('presentation', 'string', array('length' => 255))
			->addAttribute('login', 'string', array('length' => 50))
			->addAttribute('password', 'password', array('regexp' => Util::PASSWORD_REGEXP))
			->addAttribute('email', 'string', array('length' => 50))
			->addAttribute('phone', 'string', array('length' => 20))
			->addAttribute('status', 'enum', array('enum' => 'USER_STATUS'))
			->addAttribute('created')
			->addAttribute('updated');
	}
	
	protected function initSettings($settings) {
		$settings->enableQuickSearch('id', 'presentation', 'login');
		$settings->order->setDefault('presentation');
	}
	
	public function getParams() {
		if (null === $this->params)
			$this->params = new Params($this);
		return $this->params;
	}
	
	public function findByParam($param, $value, $options = array()) {
		return $this->getParams()->findUser($param, $value, $options);
	}

	public function sendMail($template, $data = array()) {
		$address = (isset($data['email']) ? $data['email'] : $this->email);
		$mail = new MailTemplate();
		if ($address) {
			return $mail->find($template, array('auto' => true))
						->addAddress($address, $this->presentation, array_merge($this->getData(), $data))
						->send();
		}
		return false;
						
	}

}