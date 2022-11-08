<?php
namespace Api\Model\Chat;

use Api\Model\Chat;
use Exception;

class Support extends Chat{
	
	const USER_PARAM = 'support_chat_id';
	
	private $user;
	
	public function __get($name) {
		if ($name === 'support')
			return true;
		return parent::__get($name);
	}

	public function setUser($user) {
		$this->user = $user;
		$id = $user->getParams()->get(self::USER_PARAM);
		if ($id)
			$this->findById($id, array('user_id' => $user->id));
		else
			$this->setId('new');
	}
	
	protected function afterWrite($isNew = false) {
		if ($isNew) {
			if ($this->user)
				$this->user->getParams()->set(self::USER_PARAM, $this->id);
		}
	}

}