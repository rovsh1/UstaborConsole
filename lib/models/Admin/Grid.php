<?php
namespace Api\Model\Admin;

use AppConfig;
use Grid as BaseGrid;
use Api\Model\Admin\Grid\Quicksearch;

class Grid extends BaseGrid{
	
	private $form;
	private $controller;
	
	public function setController($controller) {
		$this->controller = $controller;
	}
	
	public function addQuicksearch() {
		$this->form = new Quicksearch();
		$this->form->submit();
		$this->getData()->setParams($this->form->getData());
		return $this;
	}
	
	public function addDataForm() {
		$this->form = new Quicksearch();
		$this->getData()->setParams($this->form->getData());
		return $this;
	}
	
	public function __get($name) {
		switch ($name) {
			case 'quicksearch': return $this->form;
		}
		return parent::__get($name);
	}
	
	public function addColumn($column, $type = null, array $options = []) {
		if (is_array($type)) {
			$options = $type;
			$type = null;
		}
		if (null === $type) {
			switch ($column) {
				case 'id': return $this->addColumnId($options);
				case 'title': return $this->addColumnTitle($options);
				case 'name': return $this->addColumnName($options);
				case 'edit': return $this->addColumnEdit($options);
				case 'updated': return $this->addColumnUpdated($options);
				case 'created': return $this->addColumnCreated($options);
			}
		}
		return parent::addColumn($column, $type, $options);
	}
	
	public function addColumns() {
		foreach (func_get_args() as $arg) {
			$this->addColumn($arg);
		}
		return $this;
	}
	
	public function addColumnId($options = []) {
		return $this->addColumn('id', 'number', array_merge([
			'text' => '№', 
			'format' => AppConfig::get('format.number'),
			'order' => true
		], $options));
	}
	
	public function addColumnName($options = []) {
		return $this->addColumn('name', 'text', array_merge([
			'text' => lang('name_label'),
			'order' => true
		], $options));
	}
	
	public function addColumnTitle($options = []) {
		return $this->addColumn('title', 'text', array_merge([
			'text' => lang('Title'),
			'order' => true
		], $options));
	}
	
	public function addColumnEdit($options = []) {
		$controller = $this->controller;
		return $this->addColumn('edit', 'text', array_merge([
			'text' => '', 
			'renderer' => function($row) use ($controller) {
				return '<a href="' . $controller->url(array(
					'url' => './edit/' . $row['id'] . '/',
					'query' => array(
						'url' => $controller->url(array(
							'url' => null,
							'language' => null,
							'query' => true
						))
					)
				)) . '" class="btn-edit" title="' . lang('Edit') . '"></a>';
			}
		], $options));
	}
	
	public function addColumnUpdated($options = []) {
		return $this->addColumn('updated', 'date', array_merge([
			'text' => lang('Updated'),
			'format' => 'datetime',
			'order' => true
		], $options));
	}
	
	public function addColumnCreated($options = []) {
		return $this->addColumn('created', 'date', array_merge([
			'text' => lang('Created'),
			'format' => 'datetime',
			'order' => true
		], $options));
	}
	
}