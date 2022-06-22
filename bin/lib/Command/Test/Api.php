<?php
namespace Console\Command\Test;

class Api{
	
	const TEST_LOGIN = 'apitest@test.com';
	const TEST_PASSWORD = '123456';
	
	public function login() {
		$response = $this->post('login/', [
			'login' => self::TEST_LOGIN,
			'password' => self::TEST_PASSWORD
		]);
	}
	
}