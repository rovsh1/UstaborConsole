<?php
namespace Api\Model\Util;

abstract class Util{
	
	public static function urlname($url) {
		$url = (string) $url; // преобразуем в строковое значение
		$url = strip_tags($url); // убираем HTML-теги
		$url = str_replace(array("\n", "\r"), " ", $url); // убираем перевод каретки
		$url = preg_replace("/\s+/", ' ', $url); // удаляем повторяющие пробелы
		$url = trim($url); // убираем пробелы в начале и конце строки
		$url = function_exists('mb_strtolower') ? mb_strtolower($url, 'utf8') : strtolower($url); // переводим строку в нижний регистр (иногда надо задать локаль)
		$url = strtr($url, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => ''));
		$url = preg_replace("/[^0-9a-z-_ ]/i", "", $url); // очищаем строку от недопустимых символов
		$url = str_replace(" ", "_", $url); // заменяем пробелы знаком минус
		return $url;
	}
	
}