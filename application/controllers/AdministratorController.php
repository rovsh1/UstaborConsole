<?php

class AdministratorController extends InitController{

	protected $_routes = [
		['/edit/:id/', [], ['id' => '\d+|new']],
		['/delete/:id/', [], ['id' => '\d+']]
	];
	
	public function indexAction() {
		$api = $this->getApi();
		$this->get('grid')
			->addColumn('edit')
			->addColumn('presentation', 'text', ['text' => lang('User name'), 'order' => true])
			->addQuicksearch()
			->setData($api);
		$this->get('menu')->add('add', lang('Add administrator'));
		$this->page->setTitle($this->mainmenu->lang('administrator'));
		$this->setTemplate('default', 'index');
	}
	
	public function editAction() {
		$api = $this->get('api');
		$form = $this->get('form')
			->addElement('presentation', 'text', ['label' => lang('Presentation'), 'required' => true])
			->addElement('login', 'text', ['label' => lang('Login'), 'required' => true])
			->addElement('password', 'password', ['label' => lang('Password'), 'required' => $api->isNew()])
			;
		if ($form->submit()) {
			$data = $form->getData();
			if (empty($data['password']))
				unset($data['password']);
			$api->write($data);
			$this->redirect('back');
		}
		
		if ($api->isNew()) {
			$this->page->setTitle(lang('New administrator'));
		} else {
			$form->setData($api->getData());
			$this->page->setTitle($api->presentation);
			$this->get('menu')
				->add('auth', lang('Auth as user'), $this->url('./auth/' . $api->id . '/'))
				->add('-')
				->add('delete', lang('Delete administrator'));
		}
	}
	
	public function getApi() {
		return Api::factory('User');
	}

}