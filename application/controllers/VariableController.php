<?php
use Api\Service\Variables;

class VariableController extends InitController{

	protected $_routes = array(
		array('/edit/:id/', array(), array('id' => '\d+|new')),
		array('/delete/:id/', array(), array('id' => '\d+'))
	);
	
	public function indexAction() {
		$api = $this->get('api');
		$this->get('grid')
			->addQuicksearch()
			->addColumn('edit')
			->addColumn('key')
			->addColumn('name', 'text', array('text' => lang('Title')))
			->addColumn('value', 'text', array('text' => lang('Value')))
			->setData($api)
			;
		$this->page->setTitle($this->mainmenu->lang('variable'));
		$this->get('menu')->add('add', lang('Add variable'));
		$this->setTemplate('default', 'index');
	}
	
	public function editAction() {
		$api = $this->get('api');
		$form = $this->get('form')
			->addElements('site_id', 'key', 'name', 'value');
		if ($form->submit()) {
			$api->write($form->getData());
			$this->redirect('back');
		}
		if ($api->isNew()) {
			$this->page->setTitle(lang('New variable'));
		} else {
			$form->setData($api->getData());
			$this->page->setTitle($api->name);
			$this->get('menu')
				->add('delete', lang('Delete page'));
		}
		$this->setTemplate('default', 'edit');
	}
	
	protected function getApi() {
		return Api::factory('Variables');
	}

}