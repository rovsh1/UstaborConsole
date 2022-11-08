<?php

class ConstantController extends InitController{

	protected $_routes = [
		['/edit/:id/', [], ['id' => '\d+|new']],
		['/delete/:id/', [], ['id' => '\d+']]
	];
	
	public function indexAction() {
		$api = $this->getApi();
		$this->get('grid')
			->addColumn('edit')
			->addColumn('key', 'text', ['text' => lang('Key'), 'order' => true])
			->addColumn('name', 'text', ['text' => lang('Name'), 'order' => true])
			->addColumn('value', 'text', ['text' => lang('Value')])
			->addQuicksearch()
			->setData($api);
		$this->page->setTitle($this->mainmenu->lang('constant'));
		$this->get('menu')->add('add', lang('Add constant'));
		$this->setTemplate('default', 'index');
	}
	
	public function editAction() {
		$api = $this->get('api');
		
		$form = $this->get('form')
			->addElements('key', 'name', 'value')
			;
		if ($form->submit()) {
			$api->write($form->getData());
			$this->redirect('back');
		}
		
		if ($api->isNew()) {
			$this->page->setTitle(lang('New constant'));
		} else {
			$form->setData($api->getData());
			$this->page->setTitle($api->name);
			$this->get('menu')
				->add('delete', lang('Delete constant'));
		}
		
		$this->setTemplate('default', 'edit');
	}
	
	protected function getApi() {
		return Api::factory('Constants');
	}

}