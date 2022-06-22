<?php
namespace Api\Model\Admin;

use Form as AbstractForm;
use Api;

class Form extends AbstractForm{
	
	public function addElementPortal(array $options = []) {
		return $this->addElement('portal_id', 'select', array_merge([
			'label' => lang('Portal'),
			'emptyItem' => '',
			'items' => Api::factory('Portal')->select(),
			'required' => true
		], $options));
	}
	
	public function addElementName(array $options = []) {
		return $this->addElement('name', 'text', array_merge([
			'label' => lang('name_label'),
			'required' => true
		], $options));
	}
	
	public function addElementTitle(array $options = []) {
		return $this->addElement('title', 'text', array_merge([
			'label' => lang('Title'),
			'required' => true
		], $options));
	}
	
	public function addElementText(array $options = []) {
		return $this->addElement('text', 'htmleditor', array_merge([], $options));
	}
	
	public function addElementKey(array $options = []) {
		return $this->addElement('key', 'text', array_merge([
			'label' => lang('key_label'),
			'required' => true
		], $options));
	}
	
	public function addElementValue(array $options = []) {
		return $this->addElement('value', 'text', array_merge([
			'label' => lang('Value'),
			'required' => true
		], $options));
	}
	
	public function addElementLanguage(array $options = []) {
		return $this->addElement('language', 'language', array_merge([
			'label' => lang('Language'),
			'emptyItem' => ''
		], $options));
	}
	
	public function addElement($element, $type = null, array $options = []) {
		if (is_array($type)) {
			$options = $type;
			$type = null;
		}
		if (null === $type) {
			switch ($element) {
				case 'portal_id': return $this->addElementPortal($options);
				case 'name': return $this->addElementName($options);
				case 'title': return $this->addElementTitle($options);
				case 'text': return $this->addElementText($options);
				case 'key': return $this->addElementKey($options);
				case 'value': return $this->addElementValue($options);
				case 'language': return $this->addElementLanguage($options);
			}
		}
		return parent::addElement($element, $type, $options);
	}
	
	public function addElements() {
		foreach (func_get_args() as $arg) {
			$this->addElement($arg);
		}
		return $this;
	}
	
}