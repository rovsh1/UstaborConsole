<?php
namespace Api\Model\Admin\Search;

use Api;
use Form as AbstractForm;

class Form extends AbstractForm{
	
	const EmptyItem = 'Все';
	
	private $script = array();
	
	public function renderScripts() {
		if ($this->script) {
			$html = '<script type="text/javascript">';
			$html .= '$(document).ready(function(){'
				. implode('', $this->script)
				. '});';
			$html .= '</script>';
			return $html;
		}
		return '';
	}
	
	public function addElement($name, $type = null, $options = array()) {
		if (null === $type) {
			switch ($name) {
				case 'quicksearch':$this->addQuicksearch();break;
				default:
					$this->{'addElement' . ucfirst($name)}();
			}
		} else {
			parent::addElement($name, $type, $options);
			//$this->submit();
		}
		return $this;
	}
	
	public function addQuicksearch() {
		$this->addElement('quicksearch', 'text', array('placeholder' => lang('Quicksearch'), 'render' => false));
		return $this;
	}
	
	public function addElementDate() {
		$this->addElement('date', 'date', array(
			'label' => 'Дата',
			'default' => \Dater::serverDate()
		));
		return $this;
	}
	
	public function addElementPeriod() {
		$this->addElement('date', 'interval', array(//'label' => 'Период',
			'elements' => array(
				'from' => array('type' => 'date', 'label' => 'За период с', 'default' => 'first day of this month'),
				'to' => array('type' => 'date', 'label' => 'по', 'class' => 'date-to', 'default' => 'last day of this month')
			)));
		return $this;
	} 
	
	public function addElementClient($options = array()) {
		$this->addElement('client_id', 'select', array(
			'emptyItem' => 'Клиент',
			'items' => Api::factory('Client')->select($options)
		));
		return $this;
	}
	
	public function addElementProject() {
		$clientId = $this->api->getOption('client_id');
		if ($clientId) {
			$projects = Api::factory('Project')->select(array('client_id' => $clientId));
		} elseif ($this->getElement('client_id')) {
			$projects = array();
		} else {
			$projects = Api::factory('Project')->select();
		}
		$this->addElement('project_id', 'select', array(
			'emptyItem' => self::EmptyItem,
			'label' => 'Проект',
			'items' => $projects
		));
		$this->initElementScript('project_id', '/ajax/getprojects/', 'client_id');
		return $this;
	}
	
	public function addElementUser($name = 'user_id', $elementOptions = array(), $apiOptions = array()) {
		if (is_array($name)) {
			$apiOptions = $elementOptions;
			$elementOptions = $name;
			$name = 'user_id';
		}
		if (is_string($elementOptions)) {
			$elementOptions = array('emptyItem' => $elementOptions);
		}
		$elementOptions = array_merge(array(
			'textIndex' => 'presentation',
			'emptyItem' => 'Сотрудник',
			'items' => Api::factory('User')->select($apiOptions)
		), $elementOptions);
		$this->addElement($name, 'select', $elementOptions);
		return $this;
	}
	
	public function addElementOrder() {
		$this->addElement('order_id', 'select', array(
			'emptyItem' => 'Заказ',
			'items' => array()
		));
		//$this->initElementScript('order_id', '/ajax/apiselect/', 'client_id');
		return $this;
	}
	
	public function addElementUserStatus() {
		$this->addElement('status', 'enum', array(
			'emptyItem' => self::EmptyItem,
			'label' => 'Статус',
			'enum' => 'USER_STATUS'
		));
		return $this;
	}
	
	public function addElementUserRole() {
		$items = \USER_ROLE::getLabels();
		unset($items[\USER_ROLE::DEVELOPER]);
		$this->addElement('role', 'select', array(
			'emptyItem' => self::EmptyItem,
			'label' => 'Тип пользователя',
			'items' => $items
		));
		return $this;
	}
	
	private function initElementScript($name, $url, $dataIndex, $disabledText = false) {
		if (!is_array($dataIndex)) {
			$dataIndex = array($dataIndex);
		} else {
			$parent = '#' . $dataIndex;
		}
		$parent = array();
		foreach ($dataIndex as $k) {
			if ($this->getElement($k)) {
				$parent[] = '#' . $k;
			}
		}
		if (empty($parent)) {
			return;
		}
		$element = $this->getElement($name);
		$this->script[] = '$("#' . $element->getId() . '").childCombo({'
			. 'url:"' . $url . '",'
			. 'value:' . json_encode($this->api->getOption($name)) . ','
			. ($disabledText ? 'allowEmptyParent:false,disabledText:"' . $disabledText . '",' : '')
			. 'parent:' . json_encode($parent)
			. '});';
	}
	
}