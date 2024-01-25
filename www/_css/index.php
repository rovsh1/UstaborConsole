<?php
//include '../../initialize.php';
use Http\Content\Factory;

$out = Factory::get('css')
    ->setIncludePath(__DIR__ . DIRECTORY_SEPARATOR)
	->enableCache(__FILE__)
	->addFile(['font.css', 'reset.css'])
	->addDir('common');

//$out->addContent(file_get_contents('https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700|Google+Sans:400,500,700,900|Google+Sans+Display:400,500'));
$type = isset($_GET['v']) ? $_GET['v'] : 'admin';
switch ($type) {
	case 'admin': $out->addDir(['default', 'admin']); break;
	default: $out->addDir('default');
}
$out->addContent('body.ready:before{display:none;}');
$out->out();