<?php
namespace Form\Element;

class Year extends Select{

	protected $_options = array(
		'yearRange' => 100,
		'emptyItem' => false,
		'maxYear' => null
	);

	public function getItems() {
		if (null === $this->_items) {
			$this->_items = array();
			if (null !== $this->items) {
				foreach ($this->items as $k => $v) {
					$this->initItem($k, $v);
				}
			} else {
				$currentYear = $this->maxYear;
				if (!$currentYear)
					$currentYear = Year();
				for ($i = 0; $i <= $this->yearRange; $i++) {
					$this->initItem($currentYear - $i, $currentYear - $i);
				}
			}
		}
		return $this->_items;
	}
}
