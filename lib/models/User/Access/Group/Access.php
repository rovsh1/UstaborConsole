<?php
namespace Api\Model\User\Access\Group;

use Api;
use Db;
use Api\Model\User\Access\Group;

class Access{
	
	private static $tableRules = 'user_access_rules';
	
	private $rules = [];
	private $alias = [];
	
	public function __construct(Group $group) {
		$this->group = $group;
		$this->init();
	}
	
	public function has($model, $action) {
		return (bool)$this->get($model, $action);
	}
	
	public function get($model, $action) {
		if ($model instanceof Api) {
			$model = strtolower($model->getModelName());
		}
		if (isset($this->alias[$model]))
			$model = $this->alias[$model];
		foreach ($this->rules as $rule) {
			if ($rule->match($model, $action))
				return $rule;
		}
		return null;
	}
	
	public function allow($model = null, $action = null) {
		return $this->_allow($model, $action, true);
	}
	
	public function deny($model = null, $action = null) {
		return $this->_allow($model, $action, false);
	}
	
	public function isAllowed($model, $action = null) {
		$rule = $this->get($model, $action);
		if ($rule) {
			return $rule->flag;
		}
		return false;
	}
	
	public function getRules() {
		return $this->rules;
	}
	
	public function write() {
		if ($this->group->isEmpty())
			return;
		Db::delete(self::$tableRules, ['group_id' => $this->group->id]);
		$array = [];
		foreach ($this->rules as $rule) {
			$array[] = array(
				'group_id' => $this->group->id,
				'model' => $rule->model,
				'action' => $rule->action,
				'flag' => $rule->flag
			);
		}
		if ($array) {
			Db::writeArray(self::$tableRules, $array);
		}
		return $this;
	}
	
	protected function init() {
		$this
			//->add(null, null) //default rule
			->add('page', 'default')
			->add('pagecontent', 'default')
				->addAlias('page\content', 'pagecontent')
			->add('mailtemplate', 'default')
				->addAlias('mail\template', 'mailtemplate')
			->add('menu', 'default')
			->add('request', 'default')
			->add('user', ['default', 'auth', 'banlist'])
				->addAlias('user\user', 'user')
			->add('category', 'default')
				->addAlias('reference\category', 'category')
			->add('categorytag', 'default')
				->addAlias('category\tag', 'categorytag')
			->add('translation', 'default')
			
			->add('banlist', 'default')
				->addAlias('banlist\banlist', 'banlist')
				
			->add('constant', 'default')
				->addAlias('constants', 'constant')
			->add('oauth', 'default')
			->add('enum', 'default')
				->addAlias('reference\enum', 'enum')
			->add('administrator', ['default', 'auth'])
				->addAlias('user\administrator', 'administrator')
			->add('accessgroup', 'default')
				->addAlias('user\access\group', 'accessgroup')
			//->add('administration', ['base', 'users', 'access'])
				;
		
		if ($this->group->isEmpty())
			return;
		$q = Db::from(self::$tableRules)
			->where('group_id=?', $this->group->id)
			->query();
		while ($r = $q->fetch()) {
			$this->_allow($r['model'], $r['action'], (bool)$r['flag']);
		}
	}
	
	protected function addAlias($alias, $model) {
		$this->alias[$alias] = $model;
		return $this;
	}
	
	protected function add($model, $action) {
		if ($action === 'default')
			$action = ['view', 'edit', 'add', 'delete'];
		if (is_array($action)) {
			foreach ($action as $a) {
				$this->add($model, $a);
			}
		} else {
			$rule = new Rule($model, $action);
			$this->rules[] = $rule;
		}
		return $this;
	}
	
	protected function _allow($model, $action, $flag) {
		$rule = $this->get($model, $action);
		if ($rule) {
			$rule->setFlag($flag);
		}
		return $this;
	}
	
}