<?php
$portal = Api::factory('Portal');
foreach ($portal->select(['enabled' => true]) as $r) {
	$portal->findById($r['id']);
	$api = $portal->getApi('Cron');
	$api->run();
	$api->masterNotifications();
}