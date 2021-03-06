<?php
/**
 * Вспомогательный класс поиска для админ панели
 */
namespace Api\Model\Admin;

use Api\Model\Admin\Search\Form;

class Search{
	
	private static $elementsAssoc = array();
	
	private $export;
	private $form;
	private $headElements = array();
	private $options = array();
	
	public function __construct() {
		$this->form = new Form(array('method' => 'GET', 'api' => $this));
	}
	
	public function __set($name, $value) {
		$this->options[$name] = $value;
	}
	
	public function __call($name, $arguments) {
		if (method_exists($this->form, $name)) {
			call_user_func_array(array($this->form, $name), $arguments);
		}
		return $this;
	}
	
	public function submit() {
		$this->form->submit();
		$options = $this->getOptions();
		if ($this->export) {
			$this->export->submit($options);
		}
		return $options;
	}
	
	public function renderElements() {
		$this->form->submit();
		$html = $this->form->render(
			'client_id', 'date', 'legal_id', 'merchandiser_id',
			'project_id', //'region_id',
			'format_id', 'country_id',
			'group_id', 'federal_id',
			'channel_id', 'district_id',
			'route_id', 'city_id'
		);
		$html .= $this->form->render();
		return $html;
	}
	
	public function render() {
		if (!$this->hasElements()) {
			return '';
		}
		$this->form->submit();
		$html = '<div class="panel-search" id="panel-search">';
		$buttons = '<div class="search-buttons"><input type="submit" class="button-submit" value="Найти" />';
		if ($this->export) {
			$buttons .= '<button type="submit" name="' . $this->export->dataIndex . '" class="button-export" value="1">' . $this->export->buttonText . '</button>';
		}
		$buttons .= '</div>';
		$this->setHeadElements(true);
		if ($this->headElements) {
			$html .= '<div class="search-head">';
			foreach ($this->headElements as $k) {
				$element = $this->form->getElement((isset(self::$elementsAssoc[$k]) ? self::$elementsAssoc[$k] : $k));
				//if ($element instanceof ElementSelect) {
					//$element->emptyItem = $element->label;
				//	$html .= $element->renderInput();
				//} else {
					$html .= $this->form->render($element->name);
				//}
				$element->render = false;
			}
			//$html .= $buttons;
			$html .= '</div>';
			$html .= '<script type="text/javascript">$(document).ready(function(){$("#panel-search").find("div.form-field>input,select").change(function(){$(this.form).submit();});});</script>';
		}
		$inner = $this->renderElements();
		if ($inner) {
			if ($this->headElements) {
				$html .= '<div class="br10"></div>';
			}
			$html .= '<div class="button-toggle">Поиск по параметрам</div>';
			$html .= '<div class="search-inner">';
			$html .= $inner;
			//if (!$this->headElements) {
				$html .= $buttons;
			//}
			$html .= '</div>';
		}
		$html .= '</div>';
		$html .= $this->form->renderScripts();
		return $html;
	}
	
	public function getOptions() {
		$args = func_get_args();
		$options = array();
		if (empty($args)) {
			$args = array_keys($this->form->getElements());
			$options = $this->options;
		}
		foreach ($args as $name) {
			if (null !== ($value = $this->getOption($name))) {
				$options[$name] = $value;
			}
		}
		return $options;
	}
	
	public function getOption($name) {
		if (($element = $this->form->getElement($name))) {
			return (isset($_GET[$name]) ? ($_GET[$name] === '' ? null : $_GET[$name]) : $element->default);//$element->getValue();
		} elseif (isset($this->options[$name])) {
			return $this->options[$name];
		}
		return null;
	}
	
	public function getForm() {
		return $this->form;
	}
	
	public function getElement($name){
		return $this->form->getElement($name);
	}
	
	public function hasElements(){
		$min = 0;
		if ($this->form->getElement('quicksearch')){
			$min++;
		}
		return count($this->form->getElements()) > $min;
	}
	
	public function setHeadElements($all = true) {
		if (true === $all) {
			$this->headElements = array_keys($this->form->getElements());
		} else {
			$this->headElements = func_get_args();
		}
		return $this;
	}
	
	public function addElement($name, $type = null, $options = array()) {
		$this->form->addElement($name, $type, $options);
		return $this;
	}
	
	public function addElements() {
		$args = func_get_args();
		if ($args && is_array($args[0])) {
			$args = $args[0];
		}
		foreach ($args as $arg) {
			$this->addElement($arg);
		}
		return $this;
	}

}