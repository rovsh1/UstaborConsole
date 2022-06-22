<?php
use Api\Model\Backup\Manager;

$manager = new Manager();
/*$x = $manager->getDropbox()
		->create_folder('/bildrlist.com');
var_dump($x);*/
try {
	$manager->upload();
} catch (Exception $ex) {
	echo $ex->getMessage(), $ex->getTraceAsString();
}