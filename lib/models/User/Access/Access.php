<?php
namespace Api\Model\User\Access;

use Api;
use Api\Model\User;

class Access{
	
	private $user;
	private $groups;
	
	public function __construct(User $user) {
		$this->user = $user;
	}
	
	public function getGroups() {
		if (null === $this->groups) {
			$this->groups = [];
			$group = Api::factory('User\Access\Group');
			foreach ($group->select(['user_id' => $this->user->id]) as $r) {
				$this->groups[] = Api::factory('User\Access\Group', $r['id']);
			}
		}
		return $this->groups;
	}
	
	public function isAllowed($model, $action) {
		foreach ($this->getGroups() as $group) {
			if ($group->getAccess()->isAllowed($model, $action)) {
				return true;
			}
		}
		return false;
	}
	
}