<?php
namespace Api\Model\Banlist;

require_once 'Enums.php';

use Db;
use Http\Util as HttpUtil;

abstract class Log{
	
	const BAN_IP_COUNT = 3;
	
	private static $table = 'banlist_log';
	
	public static function add($reason) {
		$request = array(
			'host' => $_SERVER['HTTP_HOST'],
			'headers' => HttpUtil::getHeaders(),
			'cookie' => $_COOKIE,
			'data' => $_REQUEST
		);
		$ip = HttpUtil::getClientIp();
		Db::insert(self::$table, [
			'reason' => $reason,
			'uri' => $_SERVER['REQUEST_URI'],
			'method' => $_SERVER['REQUEST_METHOD'],
			'ip' => $ip,
			'user_agent' => HttpUtil::getUserAgent(),
			'user_id' => (defined('UserId') ? UserId : null),
			'request' => print_r($request, true),
			'created' => 'CURRENT_TIMESTAMP'
		]);
		$ban = new Banlist();
		if (!$ban->findByIp($ip)) {
			$count = Db::from(self::$table, 'count(*)')
				->where('ip=?', $ip)
				->query()->fetchColumn();
			if ($count > self::BAN_IP_COUNT) {
				$ban->write([
					'id' => 'new',
					'reason' => \BANLIST_REASON::SPAM,
					'type' => \BANLIST_TYPE::IP,
					'value' => $ip
				]);
			}
		}
	}
	
}