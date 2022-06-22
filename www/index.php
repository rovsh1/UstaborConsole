<?php
include '../initialize.php';
include ROOT_PATH . '/functions.php';
include ROOT_PATH . '/authentication.php';

Loader::loadClass('Controller_Front');

Translation::setDataSource('Db', 'admin');
Controller_Front::run(APPLICATION_PATH . '/controllers');