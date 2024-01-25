<?php
include '../initialize.php';
header('Content-type: text/xml; charset="UTF-8"');
//echo Api\Model\Sitemap::getXml();
use Api\Model\Sitemap;

$site = Site::getByDomain();
if (!$site) {
	die('');
}

Site::setSiteId($site->id);
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $site->domain;
$sitemap = new Sitemap($baseUrl);

echo $sitemap;
	