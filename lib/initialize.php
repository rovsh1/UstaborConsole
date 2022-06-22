<?php

if (!defined('LIB_PATH'))       die('LIB_PATH undefined');
if (!defined('CONFIG_PATH'))    die('CONFIG_PATH undefined');
if (!defined('INCLUDE_PATH'))	define('INCLUDE_PATH', __DIR__);
if (!defined('MODELS_PATH'))	define('MODELS_PATH', LIB_PATH . '/models');
if (!defined('FILES_PATH'))		define('FILES_PATH', realpath(LIB_PATH . '/../files'));

include __DIR__ . '/Autoload.php';

Autoload::addPath(MODELS_PATH, 'Api\Model\\', '');
Autoload::addPath(INCLUDE_PATH, 'Api\\');
Autoload::setDefaultPath(INCLUDE_PATH . '/Library');
Autoload::init(INCLUDE_PATH);

include 'functions.php';
include 'Api/Api.php';
include 'AppConfig.php';
include 'AppDebug.php';

if (file_exists(LIB_PATH . '/enums.php'))
	include LIB_PATH . '/enums.php';

AppConfig::fromINI(CONFIG_PATH . '/config.ini');

if (AppConfig::get('debug.handler')) {
	Exception\Handler::$debug = AppConfig::get('debug.debugger');
	//Exception\Handler::$scream = true;
	Exception\Handler::setupEnvironment();
	Exception\Handler::setupHandlers();
	Exception\Handler::$exceptionLog = new Exception\Log\Email(AppConfig::get('debug.email'));
}

AppConfig::init([
	'db' => 'Db::init',
	'form.elements' => 'Form\Fieldset::setDefaults',
	'mail.sender' => 'Mail\Sender::setDefaults',
	'format' => 'Format::setDefaults'
]);
Stdlib\DateTime::init(AppConfig::get('timezone.default', 'UTC'));

setlocale(LC_NUMERIC, 'C');