<?php
namespace Api\Model\Report;

use Form as BaseForm;
use Api;

class Form extends BaseForm{
	
	protected function init() {
		$this->name = 'report';
		$this->id = 'form_report';
		$this
				->addElement('portal_id')
				->addElement('action', 'hidden')
				->addElement('offset', 'hidden')
				->addElement('step', 'hidden')
				->addElement('tmpname', 'hidden')
				//->addElement('email')
				//->addElement('date_interval')
				/*->addElement('fileparams', 'fieldset', [
					'legend' => 'Параметры отчета',
					'elements' => [
						//['type' => 'select', 'label' => 'Формат', 'items' => ['csv' => 'Csv']],
						'delimeter' => [
							'type' => 'select',
							'label' => 'Разделитель',
							'items' => [';' => 'Semicolon ";"',',' => 'Comma ","']
						]
					]
				])*/;
	}
	
	public function addElementEmail(array $options = []) {
		return $this->addElement('email', 'email', array_merge([
			'label' => lang('Email to')
		], $options));
	}
	
	public function addElementPortal(array $options = []) {
		return $this->addElement('portal_id', 'select', array_merge([
			'label' => lang('Portal'),
			'emptyItem' => '',
			'items' => Api::factory('Portal')->select(),
			'required' => true
		], $options));
	}
	
	public function addElementSelect($name, $label, array $options = []) {
		return $this->addElement($name, 'select', array_merge([
			'label' => $label,
			'allowNotExists' => true
		], $options));
	}
	
	public function addElementDateInterval(array $options = []) {
		return $this->addElement('created', 'interval', array_merge([
			'label' => 'Период',
			'elements' => [
				'from' => ['type' => 'date', 'label' => 'с', 'autocomplete' => 'off'],
				'to' => ['type' => 'date', 'label' => 'по', 'autocomplete' => 'off', 'class' => 'date-to']
			]
		], $options));
	}
	
	public function addElement($element, $type = null, array $options = []) {
		if (is_array($type)) {
			$options = $type;
			$type = null;
		}
		if (null === $type) {
			switch ($element) {
				case 'email': return $this->addElementEmail($options);
				case 'portal_id': return $this->addElementPortal($options);
				case 'created':
				case 'date_interval': return $this->addElementDateInterval($options);
				case 'site_id': return $this->addElementSelect($element, lang('Site'), $options);
				case 'master_id': return $this->addElementSelect($element, lang('Master'), $options);
				case 'country_id': return $this->addElementSelect($element, lang('Country'), $options);
				case 'category_id': return $this->addElementSelect($element, lang('Category'), $options);
			}
		}
		return parent::addElement($element, $type, $options);
	}
	
}