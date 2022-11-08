<?php
use Navigation\Paginator;
use Api\Model\Admin\Menu\Main as MainMenu;
use Api\Model\Admin\Menu\Actions as ActionsMenu;
use Api\Model\Admin\Form as AdminForm;
use Api\Model\Admin\Grid as AdminGrid;

class InitController extends Controller_Action{

	public $layout = 'layout';

	/**
	 * Общая функция до загрузки страницы
	 */
	public function init() {
		//Site::setSiteId(isset($_SESSION['site_id']) ? $_SESSION['site_id'] : 1);
		if (false === $this->initAuthUser()) {
			$this->redirect('/account/auth/');
		}

		$this->page->getHead()
			->addMetaHttpEquiv('Content-Type', 'text/html; charset=utf-8')
			->addMetaHttpEquiv('Content-language', 'ru')
			->addLink(array('rel' => 'icon', 'href' => '/favicon.ico', 'type' => 'image/x-icon'))
			->addStyle('admin/')
			->addScript('admin/')
			->addContent('<style>*{padding:0;margin:0}body{position:relative;}body:before{content:"";display:block;position:fixed;top:0;left:0;width:100%;height:100%;background:#ffffff url("/images/preload.svg") 50% 50% no-repeat;background-size:80px 80px;z-index:110;}</style>')
			;
	}
	
	public function set($name, $param) {
		switch ($name) {
			case 'grid':
				$param->setPaginator($this->get('paginator'));
				$param->setController($this);
				$param->emptyGridText = lang('Data empty');
				break;
		}
		return parent::set($name, $param);
	}
	
	public function get($name) {
		$param = parent::get($name);
		if ($param)
			return $param;
		switch ($name) {
			case 'api':
				$api = $this->getApi();
				if ($this->id === 'new')
					$api->setId('new');
				elseif ($this->id !== null && !$api->findById($this->id))
					$this->redirect(404);
				$this->set('api', $api);
				return $api;
			case 'grid':
				$grid = new AdminGrid();
				$this->set('grid', $grid);
				return $grid;
			case 'paginator':
				$paginator = new Paginator($this, 30);
				$this->set('paginator', $paginator);
				return $paginator;
			case 'form':
				$form = new AdminForm(['name' => 'data', 'id' => 'form_data']);
				$this->set('form', $form);
				return $form;
			case 'mainmenu':
				$menu = new MainMenu($this);
				$menu->init();
				$this->set('mainmenu', $menu);
				return $menu;
			case 'menu':
				$menu = new ActionsMenu($this);
				$this->set('menu', $menu);
				return $menu;
		}
		return null;
	}

	/**
	 * Определение авторизованного пользователя
	 * @return $this
	 */
	protected function initAuthUser() {
		if (!$this->authUser && defined('UserId')) {
			$authUser = Api::factory('User');
			if ($authUser->findById(UserId)) {
				$this->authUser = $authUser;
			} else {
				Auth::logout();
			}
		}
		return (bool)$this->authUser;
	}
	
	public function url($url = null, $params = null) {
		switch ($url) {
			case 'back':
				$u = $this->getRequest()->getQuery('url');
				if ($u)
					$url = $u;
				else
					$url= './';
				break;
		}
		return parent::url($url, $params);
	}
	
	public function h1() {
		$html = '<div class="h1-wrap">'
				. $this->page->h1();
		if ($this->has('grid') && $this->grid->quicksearch) {
			$form = $this->grid->quicksearch;
			if ($form && $form->getElement('quicksearch')) {
				$html .= '<form method="get" class="quicksearch">'
						. $form->getElement('quicksearch')->renderInput()
						. '<button type="submit"></button>'
						. '</form>';
			}
		}
		if ($this->has('menu') && $this->get('menu')->count() > 0) {
			$html .= '<div class="menu-actions" id="menu-actions">' . $this->menu->render() . '</div>';
		}
		$html .= '</div>';
		return $html;
	}
	
	public function deleteAction() {
		if (!$this->id) {
			$this->redirect(404);
		}
		$api = $this->getApi();
		if (!$api->findById($this->id)) {
			$this->redirect(404);
		}
		$api->delete();
		$this->redirect('./');
	}

}