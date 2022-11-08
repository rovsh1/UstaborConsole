<?php
include '../initialize.php';

use Api\File as ApiFile;
use Http\Content\Factory;

if (isset($_GET['guid']) && ($guid = $_GET['guid'])) {
	$file = ApiFile::getByGuid($guid); //Поиск фала по guid
	if ($file && $file->exists()) {
		
		 //Версия фала (размер фото)
		$index = (isset($_GET['index']) ? $_GET['index'] : null);
		if ($index) {
			$file = $file->getPart($_GET['index']);
		}
		if ($file) {
			switch ($file->type) {
				default:
					Factory::fromFile($file)
						->out();
			}
		}
	}
}

Factory::notFound();