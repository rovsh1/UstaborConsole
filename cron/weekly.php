<?php
include realpath(__DIR__ . '/../') . '/initialize.php';

use Mail\Sender as MailSender;

$email = 's16121986@yandex.ru';

$portal = Api::factory('Portal');
$portal->findById(4);
$response = $portal->getApi('Report')->masterWithoutProjects([]);

$mail = new MailSender();
$mail->AddAddress('s16121986@yandex.ru', 'administrator');
$mail->Subject = 'report';
$mail->MsgHTML('report');
$mail->AddAttachment($response->tempnam, $response->filename);
$flag = $mail->Send();
var_dump($flag);

die('ok');

$cron = Api::factory('Cron');
$portals = $portal->select();
foreach ($cron->select(['period' => 'weekly']) as $task) {
	switch ($task['exec']) {
		case '':
			
			break;
		default:
			continue;
	}
	if ($task['portal_id']) {
		
	} else {
		
	}
	$response = $portal->getApi('Report')->$action($data);
	
	
}