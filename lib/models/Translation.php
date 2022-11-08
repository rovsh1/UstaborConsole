<?php

namespace Api\Model;

use Api;
use Api\Util\Translation as BaseTranslation;

class Translation extends Api {

	public static function getColumns() {
		$columns = [];
		foreach (BaseTranslation::getLanguages() as $language) {
			$columns[$language->code] = 'value_' . $language->code;
		}
		return $columns;
	}

	protected function init() {
		$this->_table = 'translation_items';
		$this
			->addAttribute('path', 'string', ['notnull' => false, 'length' => 100])
			->addAttribute('name', 'string', ['required' => true, 'length' => 100])
			->addAttribute('description', 'string', ['notnull' => false, 'length' => 100])
			->addAttribute('deletion_mark', 'boolean', ['default' => false])
			->addAttribute('updated');
		foreach (self::getColumns() as $n) {
			$this->addAttribute($n, 'string', ['notnull' => false, 'length' => 255]);
		}
	}

	protected function initSettings($settings) {
		$columns = self::getColumns();
		if ($settings->getParam('empty')) {
			$or = [];
			foreach ($columns as $n) {
				$or[] = $n . ' IS NULL';
			}
			$settings->filter(implode(' OR ', $or));
		}
		$columns[] = 'name';
		$columns[] = 'description';
		call_user_func_array([$settings, 'enableQuickSearch'], $columns);
		//$settings->quicksearch->setBounds('right');
		//$settings->filter('translation_items.path<>"admin"');
		//$settings->filter('translation_items.deletion_mark=0');
		$settings->order->setDefault('updated desc');
	}

	protected function beforeWrite() {
		$columns = self::getColumns();
		$columns[] = 'description';
		foreach ($columns as $n) {
			$v = trim($this->$n);
			if (empty($v))
				$this->$n = null;
			else
				$this->$n = $v;
		}
	}

	public function findByName($path, $name) {
		return $this->findByAttributes(['path' => $path, 'name' => $name]);
	}

}
