<?php
namespace Api\Model\Admin\Grid;

use Form;

class Quicksearch extends Form{
	
	public function __construct($options = null) {
		parent::__construct(['method' => 'GET']);
		$this->addElement('quicksearch', 'text', ['placeholder' => 'Быстрый поиск']);
	}
	
}