<?php
class IndexController extends InitController{

	public function indexAction() {
		$i = $this->get('mainmenu')->getFirstItem();
		$this->redirect($this->url($i['url']));
	}
	
	public function checkdomainAction() {
		
	}
	
	public function testAction() {
		//die('fff');
		//Api\Model\User\ClickLog::logAdministrator();
		//echo ROOT_PATH . '/cron/clicks_log.php';
		//include CRON_PATH . '/daily.php';
		//$cmd = 'php /var/zpanel/hostdata/zadmin/public_html/ustabor_uz/cron/clicks_log.php';
		//$x = exec($cmd);var_dump($x);
		//Api\Model\User\ClickLog::logUsers();
		exit;
	}

}