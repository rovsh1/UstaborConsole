<?php

class CronController extends InitController{

	protected $_routes = [
		['/edit/:id/', [], ['id' => '\d+|new']],
		['/delete/:id/', [], ['id' => '\d+']]
	];
	
	public function indexAction() {
		$api = $this->getApi();
		$this->get('grid')
			->addColumn('edit')
			->addColumn('name', 'text', ['text' => lang('Name'), 'order' => true])
			->addColumn('exec', 'text', ['text' => lang('Command')])
			->addColumn('period', 'text', ['text' => lang('Period')])
			->addQuicksearch()
			->setData($api);
		$this->page->setTitle($this->mainmenu->lang('cron'));
		$this->get('menu')->add('add', lang('Add task'));
		$this->setTemplate('default', 'index');
	}
	
	public function editAction() {
		$api = $this->get('api');
		
		$form = $this->get('form')
			->addElement('name')
			->addElement('portal_id', ['required' => false, 'emptyItem' => 'Все'])
			->addElement('exec', 'text', ['label' => 'Command'])
			->addElement('period', 'select', [
				'label' => 'Period',
				'items' => [
					'weekly' => 'Weekly'
				]
			])
			;
		if ($form->submit()) {
			$api->write($form->getData());
			$this->redirect('back');
		}
		
		if ($api->isNew()) {
			$this->page->setTitle(lang('New cron task'));
		} else {
			$form->setData($api->getData());
			$this->page->setTitle($api->name);
			$this->get('menu')
				->add('delete', lang('Delete task'));
		}
		
		$this->setTemplate('default', 'edit');
	}
	
	protected function getApi() {
		return Api::factory('Cron');
	}

}