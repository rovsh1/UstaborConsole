<?php
namespace Form\Element;

require_once 'Library/Form/Element/Textarea.php';

class Htmleditor extends Textarea{

	protected function prepareValue($value) {
		return trim((string)$value);
	}

	public function getHtml() {
        $html = parent::getHtml();
		if (false !== $this->htmleditor)
			$html .= '<script type="text/javascript">Application.initHtmlEditor("#' . $this->id . '");</script>';
		return $html;
	}

}
