<?php
namespace Api\Model;

use Api;
use Auth\Provider;

class Oauth extends Api{
	
	private static $providerReady = false;
	
	protected function init() {
		$this->_table = 'oauth_providers';
		$this
			->addAttribute('key', 'string', array())
			->addAttribute('name', 'string', array('locale' => false))
			->addAttribute('auth_id', 'string', array('default' => ''))
			->addAttribute('auth_secret', 'string', array('default' => ''))
			->addAttribute('auth_params', 'string', array('default' => ''))
			->addAttribute('enabled', 'boolean', array('default' => true))
		;
	}
	
	protected function initSettings($settings) {
		$settings->enableQuicksearch('name', 'key');
	}
	
	public static function initProvider($provider = null) {
		if (!self::$providerReady) {
			self::$providerReady = true;
			$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
			$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : DOMAIN_NAME;
			$config = array(
				'base_url' => $scheme . '://' . $host . '/auth/endpoint/',
				'endpoint' => $scheme . '://' . $host . '/auth/endpoint/',
				'providers' => array()
			);
			$api = new self();
			foreach ($api->select() as $r) {
				if ($r['auth_id'] && $r['auth_secret']) {
					$config['providers'][$r['key']] = array(
						'id' => $r['auth_id'],
						'secret' => $r['auth_secret']
					);
				}
			}
			Provider::setConfig($config);
		}
		if ($provider) {
			$api = new self();
			return $api->findByAttribute('key', $provider) && $api->enabled;
		}
		return true;
	}
	
	public static function getHtml($url, $userProviders = null) {
		$html = '<nav class="menu-social">';
		$providers = Api::factory('Oauth')->select(array('enabled' => true));
		foreach ($providers as $provider) {
			$html .= '<a href="' . $url . $provider['key'] . '/" class="' . $provider['key'] . ($userProviders && $userProviders->find($provider['id']) ? ' subscribed' : '') . '"></a>';
		}
		$html .= '</nav>';
		return $html;
	}
	
}
