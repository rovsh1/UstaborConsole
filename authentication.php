<?php
use Http\Util as HttpUtil;
use Http\Cookies;
use Http\Session;

if (!defined('UserRole')) {
	
	//Отключаем фильтры авторизации для поисковых роботов
	if (HttpUtil::isSearchBot()) return;
	
	Session::setDomain(DOMAIN_NAME);
	Cookies::setDomain(DOMAIN_NAME);
	
	//Авторизация пользователя и установка констант через Cookies
	Auth::factory(array(
		'storage' => 'Cookies',//(defined('isAdmin') ? 'Cookies' : 'Session'),
		'password' => array('md5', AppConfig::get('auth.passwordSalt')),
		//'oauth' => 'user_oauth',
		'enableSocial' => false
	))->authentication();
	
	if (Auth::isAuthorized()) {
		$authUser = Api::factory('User', UserId);
		define('UserRole', $authUser->role);
	}
	
}