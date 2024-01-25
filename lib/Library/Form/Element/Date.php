<?php
namespace Form\Element;

use DateTime;
use Exception;

class Date extends Text{

	protected $_options = array(
		'maxValue' => null,
		'minValue' => null,
		'format' => 'd.m.Y',
		'inputType' => 'text',
		'emptyValue' => false,
		'autocomplete' => 'off',
		'jsPlugin' => true
	);

	protected function prepareValue($value) {
		if ($value) {
			$dt = self::getDateObject($value);
			if ($dt) {
				$value = $dt->format('Y-m-d');
				if ($this->maxValue && ($value > $this->maxValue)) {
					return null;
				}
				if ($this->minValue && ($value < $this->minValue)) {
					return null;
				}
				return $value;
			}
		} elseif (false !== $this->emptyValue && $this->emptyValue === $value) {
			return $value;
		}
		return null;
	}

	public function getHtml() {
		$d = '';
		if ($this->getValue()) {
			$dt = self::getDateObject($this->getValue());
			if ($dt) {
				$d = $dt->format($this->format);
			}
		}
		$s = '<input type="' . $this->inputType . '"' . $this->attrToString() . ' value="' . $d . '" />';
		return $s;
	}
	
	private static function getDateObject($value) {
		if (empty($value)) {
			return null;
		} if ($value instanceof DateTime) {
			return $value;
		} if (is_numeric($value)) {
			$dt = new DateTime();
			try {
				$dt->setTimestamp($value);
			} catch (Exception $ex) {
				return null;
			}
			return $dt;
		} elseif (is_string($value)) {
			try {
				return new DateTime($value);
			} catch (Exception $ex) {
				return null;
			}
		}
	}

}
