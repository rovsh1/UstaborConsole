<?php
/**
 * Пользователь базовый
 */
namespace Api\Model;

require_once 'Request/Enums.php';

use Api;
use Db;
use Exception;

class Request extends Api{

	protected function init() {
		$this->_table = 'requests';
		$this
			->addAttribute('number', 'string', array())
			->addAttribute('user_id', 'number', array('required' => true))
			->addAttribute('category_id', 'number', array('notnull' => false))
			->addAttribute('helper_id', 'number', array('notnull' => false))
			->addAttribute('chat_id', 'number', array())
			->addAttribute('status', 'enum', array('enum' => 'REQUEST_STATUS'))
			->addAttribute('updated')
			->addAttribute('created');
	}
	
	protected function initSettings($settings) {
		$settings
				->joinInner('chat', 'chat.id=requests.chat_id', array('name'))
				->joinInner('users', 'users.id=requests.user_id', array('presentation as user_presentation'))
				->joinLeft('users as helpers', 'helpers.id=requests.helper_id', array('presentation as helper_presentation'))
				->joinLeft('categories', 'categories.id=requests.category_id', array('name as category_name'));
		$settings->columns
				->add('(SELECT guid FROM files WHERE parent_id=users.id AND type=' . \FILE_TYPE::USER_IMAGE . ') as user_image');
		$settings->enableQuicksearch('number', 'name', 'user_presentation');
	}
	
	protected function beforeWrite() {
		if ($this->isNew()) {
			$this->_set('number', self::createNumber());
		}
	}
	
	public function findByChat($chat) {
		return $this->findByAttribute('chat_id', $chat->id);
	}
	
	public function getChat() {
		$chat = self::factory('Chat');
		$chat->findById($this->chat_id);
		return $chat;
	}
	
	public function setHelper($userId) {
		$user = self::factory('User');
		if (!$user->findById($userId) || $user->id == $this->user_id)
			return false;
		if ($this->helper_id == $user->id) {
			return;
		}
		$this->write(array('helper_id' => $user->id, 'status' => \REQUEST_STATUS::PROCESSING));
		$this->getChat()->addUser($user->id, \CHAT_USER_ROLE::HELPER);
		return true;
	}
	
	public function getResponse($helperId) {
		$response = self::factory('Request\Response');
		$response->setRequest($this);
		if (!$response->findByAttribute('helper_id', $helperId)) {
			$response->setId('new');
		}
		return $response;
	}
	
	public function getNote($helperId) {
		$note = self::factory('Request\Note');
		$note->setRequest($this);
		if (!$note->findByAttribute('helper_id', $helperId)) {
			$note->setId('new');
		}
		return $note;
	}
	
	public function create($data) {
		$chat = self::factory('Chat');
		$chat->write(array(
			'id' => 'new',
			'user_id' => $data['user_id'],
			'name' => $data['text']
		));
		$this->write(array(
			'id' => 'new',
			'user_id' => $data['user_id'],
			'chat_id' => $chat->id,
			'status' => 0,
			'text' => '1'
		));
		return $chat->send($data['user_id'], $data['text']);
	}
	
	public function updateStatus($status) {
		return $this->write(array('status' => $status));
	}
	
	public function hasHelper() {
		return (bool)$this->helper_id;
	}
	
	public function isComplete() {
		return !$this->isEmpty() && in_array($this->status, array(\REQUEST_STATUS::PROCESSED, \REQUEST_STATUS::COMPLETED, \REQUEST_STATUS::ARCHIVE));
	}
	
	public function isProcessing() {
		return !$this->isEmpty() && in_array($this->status, array(\REQUEST_STATUS::PROCESSING));
	}
	
	public static function createNumber() {
		do {
			$number = 'ZR-' . rand(10000, 99999);
		} while (Db::query('SELECT 1 FROM requests WHERE number="' . $number . '"')->fetchColumn());
		return $number;
	}

}