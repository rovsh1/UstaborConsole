<?php

class BanlistController extends InitController{
	
	const IP_REGEXP = '[0-9\.]+';

	protected $_routes = [
		['/edit/:id/', [], ['id' => '\d+|new']],
		['/delete/:id/', [], ['id' => '\d+']],
		['/ip/:ip/', [], ['ip' => self::IP_REGEXP]],
		['/ipauthlog/:ip/', [], ['ip' => self::IP_REGEXP]],
		['/ipbanlog/:ip/', [], ['ip' => self::IP_REGEXP]]
	];
	
	public function indexAction() {
		$api = $this->getApi();
		$this->get('grid')
			->addColumn('edit')
			->addColumn('value', 'text', ['text' => lang('Value'), 'renderer' => function($row){
				switch ($row['type']) {
					case BANLIST_TYPE::EMAIL:
						return '<a href="/banlist/email/' . $row['value'] . '/">' . $row['value'] . '</a>';
					case BANLIST_TYPE::IP:
						return '<a href="/banlist/ip/' . $row['value'] . '/">' . $row['value'] . '</a>';
				}
			}])
			->addColumn('type', 'enum', ['text' => lang('Type'), 'enum' => 'BANLIST_TYPE'])
			->addColumn('created')
			->addQuicksearch()
			->setData($api);
		$this->page->setTitle(lang('controller_banlist'));
		$this->get('menu')->add('add', lang('Add to banlist'));
		$this->setTemplate('default', 'index');
	}
	
	public function editAction() {
		$api = $this->get('api');
		
		$form = $this->get('form')
			->addElement('type', 'enum', ['label' => lang('Type'), 'enum' => 'BANLIST_TYPE', 'required' => true])
			->addElement('reason', 'enum', ['label' => lang('Type'), 'enum' => 'BANLIST_REASON', 'required' => true])
			->addElement('value', 'text', ['label' => lang('Value'), 'required' => true])
			->addElement('description', 'textarea', ['label' => lang('Description')])
			;
		if ($form->submit()) {
			$api->write($form->getData());
			$this->redirect('back');
		}
		
		if ($api->isNew()) {
			$form->setData($this->getRequest()->getQuery());
			$this->page->setTitle(lang('New ban'));
		} else {
			$form->setData($api->getData());
			$this->page->setTitle($api->value);
			$this->get('menu')
				->add('delete', lang('Unban'));
		}
		
		$this->setTemplate('default', 'edit');
	}
	
	public function ipAction() {
		$banlist = Api::factory('Banlist\Banlist');
		$menu = $this->get('menu');
		
		if ($banlist->findByIp($this->ip)) {
			$menu->add('delete', lang('Remove from ban'), $this->url('./delete/' . $banlist->id . '/'));
		} else {
			$menu->add('add', lang('Add to ban'), $this->url('./edit/new/?type=' . BANLIST_TYPE::IP . '&ip=' . $this->ip));
		}
		
		$this->page->setTitle($this->ip);
	}
	
	public function ipauthlogAction() {
		$grid = $this->get('grid');
		$q = Db::from('auth')
				->joinInner('users', 'users.id=auth.user_id', 'presentation as user_presentation')
				->where('auth.user_ip=?', $this->ip)
				->order('auth.created desc')
				->limit($this->paginator->step, $this->paginator->getStartIndex())
				->query();
		$grid
				->addColumn('user_presentation', 'text', ['text' => lang('User'), 'href' => $this->url('/user/view/%user_id%/')])
				->addColumn('user_agent', 'text', ['text' => lang('User agent')])
				//->addColumn('user_presentation', 'text', ['text' => lang('User')])
				->addColumn('created')
				->setData($q->fetchAll());
		$this->grid = $grid;
		$this->layout = false;
		$this->setTemplate('default', 'grid');
	}
	
	public function ipbanlogAction() {
		Enum::load('Banlist');
		$grid = $this->get('grid');
		$this->paginator->setCount(Db::from('banlist_log', 'count(*)')
				->where('banlist_log.ip=?', $this->ip)
				->query()->fetchColumn());
		$q = Db::from('banlist_log')
				//->joinInner('users', 'users.id=auth.user_id', 'presentation as user_presentation')
				->where('banlist_log.ip=?', $this->ip)
				->order('banlist_log.created desc')
				->limit($this->paginator->step, $this->paginator->getStartIndex())
				->query();
		$grid
				->addColumn('reason', 'enum', ['text' => lang('Reason'), 'enum' => 'BANLIST_LOG'])
				//->addColumn('reason', 'enum', ['text' => lang('User'), 'href' => $this->url('/user/view/%user_id%/')])
				->addColumn('uri', 'text', ['text' => lang('Uri')])
				->addColumn('user_agent', 'text', ['text' => lang('User agent')])
				//->addColumn('user_presentation', 'text', ['text' => lang('User')])
				->addColumn('created')
				->setData($q->fetchAll());
		$this->grid = $grid;
		$this->layout = false;
		$this->setTemplate('default', 'grid');
	}
	
	protected function getApi() {
		return Api::factory('Banlist\Banlist');
	}
	
	public function isAllowed($model, $action = null) {
		switch ($action) {
			case 'ipauthlog':
			case 'ipbanlog':
			case 'ip':
				return parent::isAllowed($model, 'view');
				//return parent::isAllowed($model, 'edit');
		}
		return parent::isAllowed($model, $action);
	}

}