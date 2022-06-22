<?php

class ApiController extends InitController{
	
	public function indexAction() {
		$this->layout = false;
		$this->page->setTitle($this->mainmenu->lang('api'));
	}

}