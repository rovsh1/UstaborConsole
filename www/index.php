<?php

include '../initialize.php';

if ($_SERVER['REQUEST_URI'] === '/css/admin/') {
    include __DIR__ . '/_css/index.php';
    exit;
} elseif ($_SERVER['REQUEST_URI'] === '/js/admin/') {
    include __DIR__ . '/_js/index.php';
    exit;
}

include ROOT_PATH . '/functions.php';
include ROOT_PATH . '/authentication.php';

Loader::loadClass('Controller_Front');

Translation::setDataSource('Db', 'admin');
Controller_Front::run(APPLICATION_PATH . '/controllers');