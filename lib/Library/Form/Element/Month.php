<?php
namespace Form\Element;

class Month extends Select{

	protected $_options = array(
		'emptyItem' => false,
		'parts' => array("gregorian", "stand-alone", "wide")//format
	);

	public function getItems() {
		if (null === $this->_items) {
				$this->_items = array(
					/*'option_january',
					'option_february',
					'option_march',
					'option_april',
					'option_may',
					'option_june',
					'option_july',
					'option_august',
					'option_september',
					'option_november',
					'option_december',*/
				);
			if ($this->items) {
				foreach ($this->items as $k => $i) {
					$this->initItem($k, $i);
				}
			} else {
				foreach (lang($this->parts, 'months') as $i => $l) {
					$this->initItem($i, $l);
				}
			}
		}
		return $this->_items;
	}
}
