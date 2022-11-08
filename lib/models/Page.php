<?php
/**
 * Страницы сайта
 */
namespace Api\Model;

require_once 'Page/Enums.php';

use Api;
use Api\Model\Page\Content;
use Db;

class Page extends Api{
	
	const ACCESS_EDIT   = 1;
	const ACCESS_DELETE = 3;
	
	protected $pages = null;
	protected $parent = null;
	protected $htmlPage = null;
	protected $content = null;

	public static function getPathPages($path) {
		if ($path === '/') {
			$path = '';
		}
		$path = trim($path, '/');
		$pages = array();
		if ($path === '') {
			$parts = array('');
		} else {
			$parts = explode('/', $path);
		}
		$parentId = null;
		foreach ($parts as $part) {
			$page = Db::from('pages', '*')
					->where('dir=?', $part)
					->where('parent_id' . ($parentId ? '=' . $parentId : ' IS NULL'))
					//->where('status=' . \PAGE_STATUS::PUBISHED)
					->query()->fetchRow();
			if (!$page) {
				return false;
			}
			$pages[] = self::factory('Page', $page['id']);
			$parentId = $page['id'];
		}
		return $pages;
	}
	
	protected function init() {
		$this->_table = 'pages';
		$this
			//->addAttribute('site_id', 'number', array('notnull' => false))
			->addAttribute('parent_id', 'number', array('notnull' => false))
			->addAttribute('content_id', 'number', array('notnull' => false))
			->addAttribute('dir', 'string', array('length' => 20))
			->addAttribute('name', 'string', array('length' => 50, 'required' => true))
			->addAttribute('status', 'enum', array('default' => \PAGE_STATUS::PUBISHED, 'enum' => 'PAGE_STATUS'))
			->addAttribute('access', 'number', array('default' => self::ACCESS_DELETE))
			->addAttribute('xml_priority', 'number', array('length' => 1, 'fractionDigits' => 1, 'default' => 0.5))
			
			->addAttribute('created')
			->addAttribute('updated');
		//Site::initApi($this);
	}
	
	protected function initSettings($settings) {
		$settings->enableQuicksearch('name', 'dir');
	}
	
	public function __call($name, $arguments) {
		if ($this->htmlPage && method_exists($this->htmlPage, $name)) {
			return call_user_func_array(array($this->htmlPage, $name), $arguments);
		}
		return parent::__call($name, $arguments);
	}
	
	public function getData() {
		$data = parent::getData();
		//$data['options'] = $this->getOptions()->getData();
		return $data;
	}
	
	public function getPages() {
		if (null === $this->pages) {
			$this->pages = [];
			$ids = Db::from($this->table, 'id')
						->where('parent_id' . ($this->id == 1 ? ' IS NULL AND id<>1' : '=' . (int)$this->id))
						->where('hidden=0')
						->query()->fetchAll(Db::FETCH_COLUMN);
			foreach ($ids as $id) {
				$page = new self();
				$page->findById($id);
				$this->pages[] = $page;
			}
		}
		return $this->pages;
	}
	
	public function getPath() {
		if ($this->parent_id) {
			$page = new self();
			$page->findById($this->parent_id);
			$path = $page->path;
		} else {
			$path = '';
		}
		return $path . $this->dir . '/';
	}
	
	public function findByPath($path) {
		$pages = self::getPathPages($path);
		if ($pages) {
			if (false) {// && ($menu = $htmlPage->getMenu('breadcrumbs'))
				$href = '/';
				foreach ($pages as $page) {
					if ($page->id == 1) {
						continue;
					}
					$href .= $page->dir . '/';
					$menu->add($page->title, $href);
				}
			}
			$page = array_pop($pages);
			return $this->findById($page->id, ['status' => \PAGE_STATUS::PUBISHED]);
			//$this->initHtmlPage();
		}
		return false;
	}
	
	public function getContent() {
		if (null === $this->content) {
			$this->content = new Content();
			$this->content->setApi($this);
		}
		return $this->content;
	}
	
}
