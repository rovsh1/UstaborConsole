<?php
namespace Api\Model\Admin\Menu;

class Actions{
	
	private $items = [];
	private $controller;
	
	public function __construct($controller) {
		$this->controller = $controller;
	}
	
	public function add($action, $label = null, $uri = null, array $options = []) {
		if ($action === '-')
			$this->items[] = '-';
		else { //if ($this->controller->isAllowed($action)) {
			if (null === $uri) {
				switch ($action) {
					case 'view': $uri = $this->controller->url('./view/' . $this->controller->id . '/'); break;
					case 'add': $uri = $this->controller->url('./edit/new/'); break;
					case 'edit': $uri = $this->controller->url('./edit/' . $this->controller->id . '/'); break;
					case 'delete': $uri = $this->controller->url('./delete/' . $this->controller->id . '/'); break;
				}
			}
			$this->items[$action] = [$label, $uri];
		}
		return $this;
	}
	
	public function count() {
		$count = 0;
		foreach ($this->items as $item) {
			if ($item !== '-')
				$count++;
		}
		return $count;
	}
	
	public function render() {
		$i = 0;
		$c = count($this->items) - 1;
		$html = '<nav class="menu menu-icon">';
		foreach ($this->items as $action => $item) {
			if ($item === '-') {
				if ($i > 0 && $i < $c)
					$html .= '<hr />';
			} else
				$html .= '<a class="' . $action . '" href="' . $item[1] . '">' . $item[0] . '</a>';
			$i++;
		}
		$html .= '</nav>';
		return $html;
	}
	
}