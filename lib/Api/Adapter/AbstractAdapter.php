<?php
namespace Api\Adapter;

use Db;
use Db\Expr as Expr;
use Api\Util\Translation;
use Api\Util\BaseApi;
use Api\Util\Settings\Collection\Filter as FilterItem;
use Api\Attribute\AttributeFile;

abstract class AbstractAdapter{

	protected $_api = null;

	protected $_query = null;

	public function __construct(BaseApi $api) {
		$this->_api = $api;
	}

	private function getColumnTable($settings, $name, $field = false) {
		if (($attribute = $this->_api->getAttribute($name))) {
			return $this->_api->table() . ($field ? '.' . Translation::getColumn($attribute) : '');
		}
		foreach ($settings->joins as $join) {
			if (($col = $join->getColumn($name))) {
				return $join->alias . ($field ? '.' . $col->name : '');
			}
		}
		return false;
	}
	
	protected function getColumnExpression($column, $attribute = null) {
		$alias = null;
		if ($column) {
			if ($column->expression) {
				return $column->expression;
			}
			$name = ($column->locale ? Translation::getColumn($column->name) : $column->name);
			$alias = $column->alias;
		}
		if ($attribute) {
			if ($attribute->hidden) {
				return null;
			}
			if ($attribute instanceof AttributeFile) {
				if (false === $attribute->fieldPreview) {
					return null;
				}
				$name = $attribute->fieldPreview;
				if (true === $name || !$name) {
					$name = $attribute->name;
				}
				if ($attribute->fieldName) {
					return new Expr('(SELECT guid FROM files'
						. ' WHERE `files`.id=`' . $this->_api->table() . '`.`' . $attribute->name . '`'
						. ' ORDER BY `index` ASC'
						. ' LIMIT 1) as `' . $name . '`');
				} else {
					return new Expr('(SELECT guid FROM files
						WHERE `files`.parent_id=`' . $this->_api->table() . '`.id
							AND files.`type`=' . $attribute->type . ''
						. ' ORDER BY `index` ASC'
						. ' LIMIT 1) as `' . $name . '`');
				}
			}
			if ($attribute->locale) {
				$name = Translation::getColumn($attribute->name);
				$alias = $attribute->name;
			} else {
				$name = $attribute->name;
			}
		}
		if (!$alias) {
			$alias = $name;
		}
		return $name . ' as ' . $alias;
	}

	protected function initColumns($settings) {
		$columns = array();
		foreach ($this->_api->getAttributes() as $attribute) {
			if ($settings->columns->hasName($attribute->name)) {
				if (($expression = $this->getColumnExpression(null, $attribute))) {
					$columns[] = $expression;
				}
			}
		}
		foreach ($settings->columns->get() as $column) {
			if (($expression = $this->getColumnExpression($column))) {
				$columns[] = $expression;
			}
		}
		$this->_query->columns($columns);
		return $this;
	}

	protected function initFilters($settings) {
		foreach ($settings->filter->get() as $item) {
			if (!$item->isEmpty()) {
				$this->filter($item);
			}
		}
		if ($settings->quicksearch->isEnabled()) {
			$or = array();
			foreach ($settings->quicksearch->getValues() as $value) {
				switch ($settings->quicksearch->getBounds()) {
					case 'left':$boundValue = '%' . $value;break;
					case 'right':$boundValue = $value . '%';break;
					case 'both':$boundValue = '%' . $value . '%';break;
				}
				$quotedValue = Db::quote($boundValue);
				foreach ($settings->quicksearch->getColumns() as $column) {
					if (($attribute = $this->_api->getAttribute($column))) {
						$table = $this->_api->table() . '.`' . Translation::getColumn($attribute);
						switch ($attribute->getType()) {
							case \AttributeType::Boolean:
								$or[] = $table . '`=' . ($value ? '1' : '0');
								continue 2;
							case \AttributeType::Enum:
							case \AttributeType::Number:
							case \AttributeType::Year:
								if (is_numeric($value)) {
									$or[] = $table . '`=' . (int)$value;
								}
								continue 2;
						}
					}
					if (($table = $this->getColumnTable($settings, $column, true))) {
						$or[] = $table . ' LIKE ' . $quotedValue;
					}
				}
			}
			$this->where(implode(' OR ', $or));
		}
		return $this;
	}

	protected function initSelect($settings) {

		$this
				->initColumns($settings)
				->initJoins($settings)
				->initFilters($settings)
				->initOrder($settings)
				->initGroup($settings)
				->initLimit($settings);
		if ($settings->distinct)
			$this->_query->distinct(true);
		if (method_exists($this->_api, 'beforeSelect')) {
			$this->_api->beforeSelect($this->_query);
		}

		return $this;
	}

	protected function initJoins($settings, $columns = true) {
		/*$fileds = null;
		if (isset($options['fields']) && is_array($options['fields'])) {
			$fileds = $options['fields'];
		}*/
		foreach ($settings->joins as $join) {
			if ($columns) {
				$cols = array();
				foreach ($join->columns->get() as $column) {
					if ($settings->columns->hasName($column->alias)) {
						$cols[] = $this->getColumnExpression($column);
					}
				}
				if (empty($cols)) {
					$cols = null;
				}
			} else {
				$cols = null;
			}
			switch ($join->type) {
				case 'inner':$this->joinInner($join->getTableName(), $join->condition, $cols);break;
				case 'right':$this->joinRight($join->getTableName(), $join->condition, $cols);break;
				//case 'outer':$this->joinOuter($join->name . ' as ' . $join->alias, $join->condition, $cols);break;
				default:
					$this->joinLeft($join->getTableName(), $join->condition, $cols);
			}
		}
		return $this;
	}

	protected function initOrder($settings) {
		$order = array();
		foreach ($settings->order->get() as $item) {
			switch (true) {
				case (bool)$item->expression:
					$order[] = $item->expression;
					break;
				case (!$item->name):break;
				case ($item->name instanceof Expr):
					$order[] = $item->name;
					break;
				case $item->name === 'rand':
				case $item->name === 'random':
					$order[] = 'RAND()';
					break;
				case (bool)($table = $this->getColumnTable($settings, $item->name, true)):
					$order[] = $table . ' ' .$item->direction;
					break;
				default:
					foreach ($settings->columns->get() as $column) {
						if ($item->name === $column->alias) {
							$order[] = $column->alias . ' ' .$item->direction;
							break;
						}
					}
			}
		}
		if ($order) {
			$this->order($order);
		}
		return $this;
	}

	protected function initGroup($settings, $count = false) {
		$group = array();
		foreach ($settings->group->getNames() as $name) {
			if (($table = $this->getColumnTable($settings, $name, true))) {
				$group[] = $table;
			}
		}
		foreach ($settings->group->get() as $item) {
			$group[] = $item->expression;
		}
		if ($group) {
			$this->group($group);
			if ($count) {
				$this->_query = Db::from($this->_query, 'count(*)');
			}
		}
		return $this;
		if (isset($options['groupby'])) {

			$attributes = $this->_api->getAttributes();

			$flds = $options['groupby'];
			if (is_array($flds)) {
				if (isset($flds['fields'])) {
					$flds = $flds['fields'];
				}
			} else {
				$options['groupby'] = array();
			}
			if (!is_array($flds)) {
				$flds = array($flds);
			}
			$gb = array();
			foreach ($flds as $g) {
				if (isset($attributes[$g])) $gb[] = $g;
			}
			if (!empty($gb)) {
				$this->group($gb);
				if ($count) {
					$this->_query = \Db::from($this->_query, 'count(*)');
				} elseif (isset($options['groupby']['func'])) {
					$func = $options['groupby']['func'];
					if (is_array($func)) {
						if (!isset($func[0][0])) {
							$func = array($func);
						}
						foreach ($func as $fn) {
							if (is_array($fn) && isset($fn[0]) && isset($fn[1])) {
								if (isset($attributes[$fn[1]]) && preg_match('/(sum|count|min|max)/i', $fn[0])) {
									$this->_query->columns(new \Db\Expr($fn[0] . '(' . $this->_quote($fn[1]) . ') as `' . $fn[1] . '`'));
								}
							}
						}
					}
				}
			}
		}
		//$this->group('id');
		return $this;
	}

	protected function initLimit($settings) {
		if ($settings->limit->step) {
			$this->limit($settings->limit->step, $settings->limit->start);
		}
		return $this;
	}

	protected function reset() {
		$this->_query = null;
		return $this;
	}

	abstract protected function where($where);

	abstract protected function filter(FilterItem $item);

	abstract public function get($settings);

	abstract public function select($settings);

	abstract public function count($settings);

	abstract public function write();

	abstract public function delete($where = null);

	abstract public function group($group);

	abstract public function query($query);

}