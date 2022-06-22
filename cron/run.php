<?php
include realpath(__DIR__ . '/../') . '/initialize.php';

function run($script) {
	include CRON_PATH . '/scripts/' . $script . '.php';
}

run($argv[1]);