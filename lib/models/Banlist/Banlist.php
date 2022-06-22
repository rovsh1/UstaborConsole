<?php
namespace Api\Model\Banlist;

require_once 'Enums.php';

use Api;

class Banlist extends Api{
	
	protected function init() {
		$this->_table = 'banlist';
		$this
			->addAttribute('type', 'enum', array('required' => true, 'enum' => 'BANLIST_TYPE'))
			->addAttribute('reason', 'enum', array('required' => true, 'enum' => 'BANLIST_REASON'))
			->addAttribute('limit', 'number', array())
			->addAttribute('value', 'string', array('required' => true))
			->addAttribute('description', 'string', array('notnull' => false))
			->addAttribute('updated')
			->addAttribute('created');
	}
	
	public function findByIp($ip) {
		return $this->findByAttributes([
			'type' => \BANLIST_TYPE::IP,
			'value' => $ip
		]);
	}
	
	public function findByEmail($email) {
		return $this->findByAttributes([
			'type' => \BANLIST_TYPE::EMAIL,
			'value' => $email
		]);
	}
	
}