<?php
namespace Api\Model\Chat;

use Api;
use Exception;

class Message extends Api{

	protected function init() {
		$this->_table = 'chat_messages';
		$this
			->addAttribute('chat_id', 'number', array())
			->addAttribute('user_id', 'number', array())
			//->addAttribute('to_id', 'number', array())
			->addAttribute('text', 'string', array())
			->addAttribute('created');
	}
	
	protected function initSettings($settings) {
		$settings->joinInner('users', 'users.id=chat_messages.user_id', array('presentation as user_presentation'));
		if ($settings->user) {
			$settings->columns->add('(SELECT 1 FROM chat_message_status WHERE message_id=chat_messages.id AND user_id=' . $settings->user->id . ') as status');
		}
		$settings->columns
				->add('(SELECT guid FROM files WHERE parent_id=users.id AND type=' . \FILE_TYPE::USER_IMAGE . ') as user_image');
		$settings->order->setDefault('created ASC');
	}

}