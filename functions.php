<?php
use Stdlib\DateTime;
use Api\Model\Constants;

function getconst($name) {
	return Constants::get($name);
}

function formatPhone($phone, $format = 'clear') {
	switch ($format) {
		case 'html':
			return '<a href="tel:' . formatPhone($phone) . '">' . formatPhone($phone, 'format') . '</a>';
		case 'clear':
			return str_replace(array(' ', '(', ')', '-', '+'), '', $phone);
		case 'contact':
			return '+' . formatPhone($phone, 'clear');
		case 'format':
			$phone = formatPhone($phone, 'clear');
			return '+' . substr($phone, 0, 1)
					. ' (' . substr($phone, 1, 3) . ')'
					. '-' . substr($phone, 3, 5)
					. '-' . substr($phone, 5, 6)
					. '-' . substr($phone, 6, 7);
	}
	return $phone;
}

function formatUrl($url) {
	return (0 === strpos($url, 'http') ? $url : 'http://' . $url);
}

function formatDate($datetime, $format) {
	return DateTime::factory($datetime)->format($format);
}

function formatPrice($price, $type = 'default') {
	switch ($type) {
		case 'html':
			return formatPrice($price) . ' <span class="cur">&#8381;</span>';
	}
	return Format::formatNumber($price, AppConfig::get('format.price'));
}

/**
 * Форматирование ссылки youtube для вставки
 * @param type $url
 * @return type
 */
function formatYoutubeSrc($url) {
	$p = parse_url($url);
	switch (true) {
		case 0 === strpos($p['path'], '/watch'):
			parse_str($p['query'], $q);
			$guid = $q['v'];
			break;
		case $p['host'] === 'youtu.be':
			$guid = substr($p['path'], 1);
			break;
		default:return $url;
	}
	return 'https://www.youtube.com/embed/' . $guid;
}

/**
 * Html код рейтинга мастера
 * @param type $rating
 * @return string
 */
function rating($rating) {
	//$rating = round($rating);
	$html = '<div class="rating-stars rating-' . $rating . '">';
	for ($i = 0; $i < 5; $i ++) {
		if ($rating > ($i + 0.75)) $c = 'star';
		elseif ($rating > ($i + 0.25)) $c = 'half';
		else $c = 'empty';
		$html .= '<div class="' . $c . '"></div>';
	}
	$html .= '</div>';
	return $html;
}

/**
 * Ссылка на файл из БД files.guid
 * @param type $file guid файла
 * @param type $params Версия (размер для фото) файла
 * @return type
 */
function getFileUrl($file, $params = null) {
	return '/file/' . $file . '/' . ($params ? $params . '/' : '');
}

function button_back() {
	return '<a href="/chat/" class="button-back" title="Назад"></a>';
}

/**
 * Html тег фото пользователя из данных
 * @param type $data
 * @param type $alt
 * @return type
 */
function user_avatar($data, $alt = '') {
	if (is_string($data)) {
		$data = array('image' => $data);
	} elseif ($data instanceof File) {
		$data = $data->getData();
	} elseif ($data instanceof Api) {
		$data = $data->getData();
	}
	$guid = null;
	foreach (array('user_image', 'user_avatar', 'master_avatar', 'advertiser_avatar', 'image', 'guid') as $k) {
		if (isset($data[$k])) {
			$guid = $data[$k];
			break;
		}
	}
	foreach (array('master_presentation', 'customer_presentation', 'user_presentation', 'presentation') as $k) {
		if (isset($data[$k])) {
			$alt = $data[$k];
			break;
		}
	}
	if ($guid) {
		$src = getFileUrl($guid);
	} else {
		$src = '/images/icons/user.svg';
	}
	return '<img src="' . $src . '" alt="' . $alt . '" title="' . $alt . '" />';
}

function jsonResponse($data = 'ok') {
	if (is_string($data))
		$data = array('status' => $data);
	echo json_encode($data);
	exit;
}

function jsonException($message = 'unknown', $code = 0, $data = array()) {
	$data['success'] = false;
	$data['error'] = array(
		'code' => $code,
		'message' => $message
	);
	jsonResponse($data);
}

function clearOptions(&$options) {
	foreach ($options as $k => $v) {
		if (null === $v || '' === $v) {
			unset($options[$k]);
		} else {
			switch ($k) {
				case 'created':
					clearOptionsDate($options, $k);
					break;
			}
		}
	}
	return $options;
}

function clearOptionsDate(&$options, $name) {
	if (array_key_exists($name, $options)) {
		if (empty($options[$name]))
			unset($options[$name]);
		else {
			if (array_key_exists('from', $options[$name]) && empty($options[$name]['from']))
				unset($options[$name]['from']);
			if (array_key_exists('to', $options[$name]) && empty($options[$name]['to']))
				unset($options[$name]['to']);
		}
	}
}