<?php
use Api\Model\Mail\Template;

class MailtemplateController extends InitController{

	protected $_routes = array(
		//array('/view/:id/', array(), array('key' => '\d+')),
		array('/edit/:id/', array(), array('id' => '\d+|new'))
	);
	
	public function indexAction() {
		$api = $this->getApi();
		$this->get('grid')
			->addQuicksearch()
			->addColumn('edit')
			->addColumn('name', 'text', array('text' => lang('Title')))
			->addColumn('subject', 'text', array('text' => lang('Subject')))
			->addColumn('type', 'enum', array('text' => lang('Type'), 'order' => true, 'enum' => 'MAIL_TYPE'))
			->addColumn('country_name', 'text', array('text' => lang('Country'), 'order' => true))
			->setData($api)
			;
		$this->page->setTitle($this->mainmenu->lang('mailtemplate'));
		$this->get('menu')->add('add', lang('Add template'));
		$this->setTemplate('default', 'index');
	}
	
	public function editAction() {
		$api = $this->get('api');
		$o = [];
		if (!$api->isNew()) $o['id'] = array('value' => $api->id, 'comparison' => '!=');
		$form = $this->get('form')
			//->addElements('site_id', 'country_id', 'name')
			->addElement('type', 'enum', array('label' => lang('Type'), 
				'enum' => 'MAIL_TYPE',
				'emptyItem' => ''
			))
			->addElement('parent_id', 'tree', array('label' => lang('Parent'), 
				'emptyItem' => '--' . lang('Empty') . '--',
				'items' => $this->getApi()->select($o)
			))
			->addElement('subject', 'text', array('label' => lang('Title'), 'required' => true))
			->addElement('body', 'textarea', array('label' => lang('Message'), 'stripTags' => false, 'required' => true))
			;
		if ($form->submit()) {
			$api->write($form->getData());
			$this->redirect('back');
		}
		if ($api->isNew()) {
			$this->page->setTitle(lang('New template'));
		} else {
			$form->setData($api->getData());
			$this->page->setTitle($api->name);
			$this->get('menu')
				//->add('view', lang('Open page'), $this->url($api->getPath()))
				//->add('-')
				->add('delete', lang('Delete page'));
		}
	}
	
	protected function getApi() {
		return new Template();
	}

}