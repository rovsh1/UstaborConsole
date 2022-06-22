<?php
namespace Console\Command;

use Console\Command\Monitor\Request;
use Mail\Sender as MailClient;
use Db;

class Monitor extends AbstractCommand{
	
	public static $mailConfig = [
		'CharSet' => 'utf-8',
		'From' => 'noreply@fixinglist.com',
		'FromName' => 'Server monitor',
		'Mailer' => 'smtp',
		'SMTPAuth' => 'true',
		'Host' => 'smtp.1and1.com',
		'Port' => '587',
		'SMTPSecure' => 'tls',
		'Username' => 'noreply@fixinglist.com',
		'Password' => '$YEIQ*nFVT!L9Y1'
	];
	
	private $address = [
		'ustabor@gmail.com',
		's16121986@yandex.ru'
	];
	private $log = [];
	private $debugMode = false;
	private $flagReport = false;
	
	protected function init() {
		$this->debugMode = $this->hasFlag('debug', 'd');
	}
	
	public function main() {
		switch ($this->getArgument()) {
			case 'check':
				$this->check();
				break;
			default:
				return $this->help();
		}
	}
	
	public function help() {
		$this->out('monitor:check [-d|--debugg]', 'green');
	}
	
	public function check() {
		$isTest = $this->debugMode ? 1 : 0;
		$request = new Request();
		$q = Db::query('SELECT id,url,status FROM monitor_url WHERE test_flag=' . $isTest);
		while ($r = $q->fetch()) {
			$result = $request->check($r['id'], $r['url']);
			//var_dump($result);
			$result->status_changed = $result->url_status !== $r['status'];
			if ($isTest || $result->status_changed) {
				Db::update('monitor_url', ['status' => $result->url_status], $r['id']);
				$this->flagReport = true;
			}
			$this->log($result);
		}
		$this->report();
	}
	
	public function report() {
		if (empty($this->log)) {
			echo 'log empty' . "\n";
			return;
		}
		foreach ($this->log as $result)
			echo $result->url . ' -> '
				. $result->response_code . ' ' . $result->url_status
				. ($result->error ? ' (' . $result->error . ')' : '')
				. "\n";
		if ($this->flagReport) {
			$this->flagReport = false;
			$sender = new MailClient();
			//$sender->SMTPDebug = true;
			$sender->SingleTo = true;
			foreach (self::$mailConfig as $k => $v) {
				$sender->$k = $v;
			}
			foreach ($this->address as $address) {
				$sender->AddAddress($address);
			}
			//$sender->AddAddress('s16121986@yandex.ru');
			$sender->Subject = 'Server monitor report' . ($this->debugMode ? ' (testing mode)' : '');
			$msg = now()->format('datetime') . '<br /><br />';
			$msg .= '<table border="1" cellpadding="2" cellspacing="0">';
			$msg .= '<tr>'
					. '<th>Status</th>'
					. '<th>Url</th>'
					. '<th>Changed</th>'
					. '<th>Error</th>'
					. '</tr>';
			foreach ($this->log as $result) {
				$msg .= '<tr>'
						. '<td style="color:' . ($result->ok ? '#390': '#c00') . ';">' . $result->response_code  . '</td>'
						. '<td>' . $result->url . '</td>'
						. '<td>' . ($result->status_changed ? '&bull;' : '&nbsp;') . '</td>'
						. '<td>' . ($result->error ?: '&nbsp;') . '</td>'
						. '</tr>';
			}
			$msg .= '</table>';
			$sender->MsgHTML($msg);
			$x = $sender->Send();
			echo 'mail status: ' . ($x ? 'OK' : 'FAILED') . "\n";
		}
		//var_dump($x);
	}
	
	protected function log($result) {
		$this->log[] = $result;
	}
	
}