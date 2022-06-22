<?php
namespace Api\Model\User;

abstract class Util{
	
	const PASSWORD_REGEXP = '/^[a-z0-9_\-:;~!@#$%^&*+=]{6,}$/i';
	
	public static function generatePassword($length = 6) {
		$password = '';
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
		$l = strlen($chars) - 1;
		for ($i = 0; $i < $length; $i++) {
			$password .= substr($chars, mt_rand(0, $l), 1);
		}
		return $password;
	}
	
	public static function checkPassword($password) {
		return preg_match(self::PASSWORD_REGEXP, $password);
	}

	public static function checkPresentation($user) {
		if (!$user->presentation || !preg_match('/[a-zа-я \.]+/iu', $user->presentation)) {
			$x = array();
			foreach (array('surname', 'name', 'patronymic') as $k) {
				if ($user->$k) {
					$x[] = $user->$k;
				}
			}
			if (empty($x)) {
				if ($user->login) {
					$user->presentation = (false === ($p = strpos($user->login, '@')) ? $user->login : substr($user->login, 0, $p));
				} else {
					$user->presentation = $user->email;
				}
			} else {
				for ($i = 1; $i < count($x); $i++) {
					$x[$i] = mb_substr($x[$i], 0, 1, 'utf8') . '.';
				}
				$user->presentation = join(' ', $x);
			}
		}
		return $user;
	}
	
}