<?php

class TranslationNamespaces {

	public function handle() {
		$q = Db::from('translation_items')
			->query();
		while ($r = $q->fetch()) {
			Db::update('translation_items', [
				'name' => static::replace($r['name'])
			], ['id' => $r['id']]);
		}
	}

	private static function replace($v) {
		if (is_null($v))
			return null;
		else if (0 !== strpos($v, 'App\\'))
			return $v;

		$v = str_replace('App\\Custom\\', 'Ustabor\\Custom\\', $v);

		return str_replace('App\\', 'Ustabor\\Infrastructure\\', $v);
	}

}