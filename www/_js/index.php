<?php
include ROOT_PATH . '/authentication.php';
use Http\Content\Factory;

$out = Factory::get('js')
    ->setIncludePath(__DIR__ . DIRECTORY_SEPARATOR)
	//->enableCache(__FILE__)
	->addFile(['Application.js', 'jquery.js', 'functions.js', 'jquery-ui.js'])
	->addDir(['plugins', 'main', 'admin', 'report']);

$out->addContent('$(document.body).addClass("ready");');
$out->out();
	
			
				
				
			