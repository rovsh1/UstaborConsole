<?php

namespace Api\Model\Admin\Menu;

class Main {

	private $current = null;
	private $currentGroup = null;
	private $controller = null;
	private $groups;

	public function __construct($controller) {
		$this->controller = $controller;
	}

	public function init() {
		if (null !== $this->groups)
			return;
		$this->groups = [];
		$this
			->addGroup('reports', 'Отчеты')
			->add('dashboard', 'Dashboard', '/report/dashboard/')
			//->add('-')
			->add('master_contacts_unique', 'Уникальные переходы', '/report/master-contacts-unique/')
			->add('master_contacts_balance', 'Списания за контакт', '/report/master-contacts-balance/')
			->add('master_without_projects', 'Мастера без проектов', '/report/master-without-projects/')
			->add('promotions', 'Продвижение', '/report/promotions/')
			->add('masters', 'Мастера', '/report/masters/')
			->add('customers', 'Заказчики', '/report/customers/')
			->add('customers', 'Заказчики', '/report/more-than-10-clicks-customers/')
			->add('master_statistics', 'Статистика по мастерам', '/report/master-statistics/')
			->add('customer_statistics', 'Статистика по заказчикам', '/report/customer-statistics/')
			//->add('masters', 'Мастера', '/report/masters/')

			->addGroup('category', 'Категории')
			->add('promotions', 'Статистка продвижений', '/report/category-promotions/')
			->add('masters', 'Статистка по мастерам', '/report/category-masters/')
			->add('requests', 'Статистка по заявкам', '/report/category-requests/')
			->add('marketing', 'Прибыль и Убытки', '/report/category-marketing/')
			->addGroup('request', 'Заявки')
			->add('request_1', 'Количество', '/report/request_1/')
			->add('request_2', 'Общий', '/report/request_2/')
			->add('request_3', 'Category/Master', '/report/request_3/')
			->add('request_4', 'Стоимость контакта', '/report/request_4/')
			->add('request_5', 'Отмененные', '/report/request_5/')
			->add('request_6', 'Стоимость услуг', '/report/request_6/')
			->add('request_7', 'Категории', '/report/request_7/')
			->add('request_8', 'По мастерам', '/report/request_8/')
			->addGroup('reference', 'Справочники')
			->add('translation', 'Переводы')
			//->add('mailtemplate', 'Шаблоны писем')
			//->add('constant', 'Константы')
			//->add('variable', 'Переменные')
			//->add('enum', 'Перечисления')

			->addGroup('tests', 'Тесты')
			->add('apitests', 'Site Api')
			->addGroup('menu_administration', 'Администрирование')
			->add('cron', 'Расписание заданий')
			//->add('sync', 'Синхронизация', '/administrator/sync/')
			->add('api', 'Документация API')
			->add('-')
			//->add('portal', 'Серверы')
			->add('administrator', 'Администраторы')
			->addGroup('test', 'Тесты')//->add('portal', 'Серверы')
		;
		$this->setCurrent($this->controller->getRequest()->getPathInfo());
	}

	public function addGroup($name, $label = null) {
		$this->groups[$name] = [
			'text' => $label ? $label : lang($name),
			'items' => []
		];
		$this->currentGroup = $name;
		return $this;
	}

	public function add($modelName, $label = null, $url = null) {
		$group = $this->currentGroup;
		if ($modelName === '-') {
			$this->groups[$group]['items'][] = '-';
		} else {
			if (is_array($modelName))
				list($modelName, $actionName) = $modelName;
			else
				$actionName = 'view';
			$this->groups[$group]['items'][] = [
				'model' => $modelName,
				'text' => $label ? $label : lang('controller_' . $modelName),
				'url' => $url !== null ? $url : '/' . $modelName . '/'
			];
		}
		return $this;
	}

	public function setCurrent($current) {
		$this->current = $current;
		return $this;
	}

	public function getFirstItem() {
		foreach ($this->groups as $group) {
			foreach ($group['items'] as $item) {
				if ($item !== '-')
					return $item;
			}
		}
	}

	public function render() {
		$html = '<div class="menu-inner">';
		foreach ($this->groups as $group) {
			$menuHtml = '';
			$noHr = true;
			$lastIndex = count($group['items']) - 1;
			foreach ($group['items'] as $i => $item) {
				if ($item === '-') {
					if ($noHr || $lastIndex === $i)
						continue;
					$noHr = true;
					$menuHtml .= '<hr />';
				} else {
					$noHr = false;
					$url = $this->url($item['url']);
					$menuHtml .= '<a href="' . $url . '"' . (self::isCurrentUrl($url, $this->current) ? ' class="current"' : '') . '>' . $item['text'] . '</a>';
				}
			}
			if (!$menuHtml)
				continue;
			$html .= '<div class="item">'
				. '<div class="label">'
				. '<i class="fa fa-caret-down"></i>'
				. $group['text']
				. '</div>';
			$html .= '<nav>' . $menuHtml . '</nav>';
			$html .= '</div>';
		}
		$html .= '</div>';
		return $html;
	}

	private function url($url) {
		return $url;
	}

	public function lang($name) {
		foreach ($this->groups as $groupName => $group) {
			foreach ($group['items'] as $item) {
				if ($item === '-') {

				} else if ($item['model'] === $name)
					return $item['text'];
			}
		}
		return '';
	}

	private static function isCurrentUrl($url, $current) {
		//var_dump($url . '=' . $current);
		$u = $url;
		return $current && ($current === $u
				|| 0 === strpos($current, $u . 'view')
				|| 0 === strpos($current, $u . 'edit'));
	}

}