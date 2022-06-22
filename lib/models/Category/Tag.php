<?php
namespace Api\Model\Category;

use Api;
use Db;

class Tag extends Api{
	
	private static $tableVariants = 'category_tag_variants';

	protected function init() {
		$this->_table = 'category_tags';
		$this
			->addAttribute('category_id', 'number', array())
			->addAttribute('text', 'string', array());
	}
	
	protected function initSettings($settings) {
		$settings->joinInner('categories', 'categories.id=category_tags.category_id', 'name as category_name');
		if (!$settings->quicksearch->isEmpty()) {
			$text = Db::quote($settings->quicksearch->getValue());
			$settings->filter('category_tags.`text`=' . $text . ' OR EXISTS(SELECT 1 FROM ' . self::$tableVariants . ' WHERE tag_id=category_tags.id AND `text`=' . $text . ')');
		}
		$settings->filterIf('user_id', 'EXISTS(SELECT 1 FROM user_category_tags WHERE tag_id=category_tags.id AND user_id=' . (int)$settings->user_id . ')');
	}
	
	protected function beforeWrite() {
		//if (!$this->urlname)
		//	$this->urlname = Util::urlname($this->name);
	}
	
	public function getVariants() {
		return Db::from(self::$tableVariants, 'text')
				->where('tag_id=' . (int)$this->id)
				->order('text')
				->query()->fetchAll(Db::FETCH_COLUMN);
	}
	
	public function addVariant($text) {
		if ($this->text !== $text && !$this->hasVariant($text))
			Db::insert(self::$tableVariants, array(
				'tag_id' => $this->id,
				'text' => $text
			));
		return $this;
	}
	
	public function removeVariant($text) {
		Db::delete(self::$tableVariants, array(
			'tag_id' => $this->id,
			'text' => $text
		));
		return $this;
	}
	
	public function hasVariant($text) {
		return (bool)Db::from(self::$tableVariants)
				->where('tag_id=' . (int)$this->id)
				->where('text=?', $text)
				->query()->fetchRow();
	}

}