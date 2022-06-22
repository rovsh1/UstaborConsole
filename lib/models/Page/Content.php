<?php
namespace Api\Model\Page;

use Api;

class Content extends Api{
	
	private $api;
	
	protected function init() {
		$this->_table = 'page_contents';
		$this
				//->addAttribute('base_href', 'string', array('notnull' => false))
				//->addAttribute('language', 'language', array('length' => 10))
				->addAttribute('name', 'string', array('length' => 50, 'notnull' => false))
				->addAttribute('title', 'string', array('length' => 255, 'notnull' => false))
				->addAttribute('keywords', 'string', array('length' => 255, 'notnull' => false))
				->addAttribute('description', 'string', array('length' => 255, 'notnull' => false))
				->addAttribute('h1', 'string', array('length' => 255, 'notnull' => false))
				->addAttribute('head', 'string', array('notnull' => false))
				->addAttribute('text', 'string', array('notnull' => false));
	}
	
	public function setApi(Api $api) {
		$this->api = $api;
		if ($api->content_id)
			$this->findById($api->content_id);
		else
			$this->setId('new');
		return $this;
	}
	
	public function findByName($name) {
		return $this->findByAttribute('name', $name);
	}
	
	public function _getData() {
		if ($this->isEmpty())
			return [];
		$data = parent::getData();
		
		$data['title'] = $this->getMetaRegexp('<title>([^<]*)<\/title>');
		$data['keywords'] = $this->getMetaRegexp('<meta name="keywords" content="([^"]*)" \/>');
		$data['description'] = $this->getMetaRegexp('<meta name="description" content="([^"]*)" \/>');
		return $data;
	}
	
	public function getHeadExtended() {
		if ($this->isEmpty())
			return;
		$api = new self();
		$head = $this->head;
		$head = preg_replace_callback('/<extends name="([^"]*)" \/>/', function($matches) use ($api) {
			if ($matches[1] && $api->findByName($matches[1])) {
				return $api->getHeadExtended();
			}
			return '';
		}, $head);
		return $head;
	}
	
	protected function afterWrite($new = false) {
		if ($new && $this->api)
			$this->api->write(array('content_id' => $this->id));
	}
	
	private function getMetaRegexp($regexp) {
		preg_match('/' . $regexp . '/i', $this->head, $m);
		return $m ? $m[1] : null;
	}
	
}