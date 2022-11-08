<?php
include __DIR__ . '/config/define.php';
include LIB_PATH . '/initialize.php';

Translation::addLanguage('ru', array('name' => 'Русский', 'default' => true, 'hreflang' => 'ru', 'locale' => 'ru_RU.utf8'));
Translation::addLanguage('en', array('name' => 'English', 'hreflang' => 'en', 'locale' => 'en_EN'));
Translation::addLanguage('uz', array('name' => 'O’zbekcha', 'hreflang' => 'uz', 'locale' => 'uz_UZ'));
Translation::addLanguage('kz', array('name' => 'Казахский', 'hreflang' => 'kk', 'locale' => 'kz_KZ'));
Translation::addLanguage('kg', array('name' => 'Киргизский', 'hreflang' => 'kg', 'locale' => 'ky_KG'));
Translation::setDataSource('Db');
Translation::setAutoEnabled(false);

Stdlib\DateTimezone::setClient('+0300');//Europe\Moscow