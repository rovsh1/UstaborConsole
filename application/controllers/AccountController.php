<?php
class AccountController extends InitController{
	
	public function menuAction() {
		$this->layout = false;
	}
	
	public function authAction(){
		$url = $this->getRequest()->getQuery('url');
		if ($url) {
			$url = urldecode($url);
		} else {
			$url = '';
		}
		if ($this->authUser) {
			//$this->redirect($url);
		}
		$this->form = new Form(array('name' => 'auth'));
		$this->form
				->addElement('login', 'text', array('placeholder' => 'Логин', 'required' => true))
				->addElement('password', 'password', array('placeholder' => 'Пароль', 'required' => true))
			;
		
		if ($this->form->submit()) {
			$data = $this->form->getData();
			if (Auth::login($data)) {
				$this->redirect($url);
			}
			$this->form->addError('Неправильный логин или пароль');
		} elseif ($this->getRequest()->getQuery('error')) {
			$this->form->addError('Неправильный логин или пароль');
		}

		if (($login = $this->getRequest()->getQuery('login'))) {
			$this->form->login->setValue($login);
		}
		$this->page->setTitle('Войти в систему');
		$this->layout = false;
	}
	
	public function indexAction() {
		$this->page->setTitle($this->authUser->presentation . ' &mdash; ' . lang('Profile'));
		$this->page->setH1($this->authUser->presentation);
	}
	
	public function logoutAction() {
		Auth::logout();
		$this->redirect('home');
	}
	
	protected function initAuthUser() {
		return parent::initAuthUser() || $this->getRequest()->getActionName() === 'auth';
	}
	
}