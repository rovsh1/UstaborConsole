<?php
namespace Api\Model\User;

use Api;
use Db;
use Stdlib\DateTime;

class Params{
	
	private static $table = 'user_params';
	
	private $user;
	private $params = array();
	
	public function __construct($user) {
		$this->user = $user;
	}
	
	public function findUser($param, $value, $options) {
		$userId = Db::from(self::$table, 'user_id')
				->where('name=?', $param)
				->where('value=?', $value)
				->query()->fetchColumn();
		return $this->user->findById($userId, $options);
	}

	public function findByHash($hash, $type, $action = null) {
		if ($this->user->isEmpty()) {
			$user = $this->user;
		} else {
			$user = Api::factory('User');
		}
		if (!$user->findByParam('hash_' . $type, $hash)) {
			return false;
		}
		if ($this->user->id != $user->id) {
			return false;
		}
		$hashTime = $this->get('hash_' . $type . '_time');
		if ($hashTime > DateTime::serverDatetime()) {
			return true;
		} else {
			switch ($action) {
				case 'send':
					$user->sendUserConfirmation();
					break;
				case 'delete':
					$user->delete();
					break;
			}
		}
		return false;
	}
	
	public function setHash($type) {
		$hash = md5($this->user->login . uniqid());
		$datetime = now();
		$datetime->modify('+1 day');
		$this
			->set('hash_' . $type, $hash)
			->set('hash_' . $type . '_time', DateTime::serverDatetime($datetime));
		return $hash;
	}
	
	public function clearHash($type) {
		$this
			->set('hash_' . $type, null)
			->set('hash_' . $type . '_time', null);
		return $this;
	}
	
	/**
	 * Времменый код авторизации
	 * @return type
	 */
	public function getAuthHash() {
		$hash = $this->getParam('hash_auth');
		$hashTime = $this->getParam('hash_auth_time');
		if (!$hash || !$hashTime || $hashTime < Dater::serverDatetime()) {
			$hash = $this->setHash('auth');
		}
		return $hash;
	}
	
	public function set($name, $value) {
		$this->params[$name] = $value;
		Db::delete(self::$table, array(
			'user_id' => $this->user->id,
			'name' => $name
		));
		if (null !== $value) {
			Db::insert(self::$table, array(
				'user_id' => $this->user->id,
				'name' => $name,
				'value' => $value
			));
		}
		return $this;
	}
	
	public function get($name = null) {
		if (null === $name)
			return Db::from(self::$table, array('name', 'value'))
				->where('user_id=' . $this->user->id)
				->query()->fetchAll();
		if (!isset($this->params[$name])) {
			$this->params[$name] = Db::from(self::$table, 'value')
				->where('user_id=' . $this->user->id)
				->where('name=?', $name)
				->query()->fetchColumn();
		}
		return $this->params[$name];
	}
	
}