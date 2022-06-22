<?php
namespace Api\Model\User\Access;

use Api;
use Api\Model\User;
use Api\Model\User\Access\Group\Access;

class Group extends Api{
	
	private $access;
	
	protected function init() {
		$this->foreignKey = 'group_id';
		$this->_table = 'user_access_groups';
		$this
			->addAttribute('name', 'string', array('required' => true));
		
		$this->addTabularSection('members', 'user_access_members', 'user_id', 'model', array('model' => 'User'));
	}
	
	protected function initSettings($settings) {
		$settings->filterIf('user_id', 'EXISTS(SELECT 1 FROM user_access_members WHERE group_id=user_access_groups.id AND user_id=' . (int)$settings->user_id . ')');
	}
	
	public function getData() {
		$data = parent::getData();
		$data['members'] = $this->members->selectColumn('user_id');
		return $data;
	}
	
	public function getAccess() {
		if (null === $this->access)
			$this->access = new Access($this);
		return $this->access;
	}
	
	public function addMember(User $user) {
		$this->members
				->add($user->id)
				->write();
		return $this;
	}
	
	public function removeMember(User $user) {
		$this->members
				->delete($user->id)
				->write();
		return $this;
	}
	
}