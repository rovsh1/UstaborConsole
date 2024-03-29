<?php

namespace Form;

abstract class Element
{

    private static $_default = [
        'required' => false,
        'render' => true
    ];
    protected $_options = [];
    protected $_rendered = false;
    protected $_error;
    protected $_value = null;
    protected $_label = null;
    protected $_parent;
    protected $_attributes = [];

    public static function factory($name, $type, $options = null)
    {
        if (!is_array($options)) {
            $options = [];
        }
        $cls = 'Form\\Element\\' . ucfirst($type);
        $element = new $cls($name, $options);

        return $element;
    }

    public function __set($name, $value)
    {
        $this->setOption($name, $value);
    }

    public function __get($name)
    {
        return $this->getOption($name);
    }

    public function __construct($name, $options = [])
    {
        if (!isset($options['class'])) {
            $options['class'] = '';
        }
        $options['type'] = strtolower(str_replace('Form\\Element\\', '', get_class($this)));
        $options['class'] .= ' field-' . $options['type'];
        $this->setName($name)
            ->setOptions(array_merge(self::$_default, $options));
        $this->init();
    }

    public function setOptions($options)
    {
        $lastOptions = [];
        foreach (['default', 'value'] as $k) {
            if (array_key_exists($k, $options)) {
                $lastOptions[$k] = $options[$k];
                unset($options[$k]);
            }
        }
        foreach ($options as $k => $v) {
            $this->setOption($k, $v);
        }
        foreach ($lastOptions as $k => $v) {
            $this->setOption($k, $v);
        }

        return $this;
    }

    public function getOption($name)
    {
        switch ($name) {
            case 'value':
                return $this->getValue();
            case 'id':
                return $this->getId();
            case 'inputName':
                return $this->getInputName();
        }
        if (isset($this->_options[$name])) {
            return $this->_options[$name];
        }

        //if (isset(self::$_default[$name])) return self::$_default[$name];
        return null;
    }

    public function setOption($key, $option)
    {
        switch ($key) {
            case 'value':
                $this->setValue($option);
                break;
            case 'default':
                $this->_options[$key] = $this->prepareValue($option);
                $this->setValue($option);
                break;
            default:
                $this->_options[$key] = $option;
        }

        return $this;
    }

    public function setParent($parent)
    {
        $this->_parent = $parent;

        /*if ($parent instanceof \Form) {
            $this->id = $this->_parent->getName() . '_formfield_' . $this->name;
        } else {
            $this->id = $this->_parent->id . '_' . $this->name;
        }*/

        return $this;
    }

    public function getForm()
    {
        return ($this->_parent instanceof \Form ? $this->_parent : null);
    }

    public function setForm($form)
    {
        return $this->setParent($form);
    }

    public function getId()
    {
        if (!isset($this->_options['id'])) {
            $parts = [];
            if ($this->_parent && $this->_parent->getId()) {
                $parts[] = $this->_parent->getId();
            }
            $parts[] = $this->name;
            $this->setId(implode('_', $parts));
        }

        return $this->_options['id'];
    }

    public function setId($id)
    {
        return $this->setOption('id', $id);
    }

    public function getInputName()
    {
        if (!isset($this->_options['inputName'])) {
            if ($this->_parent) {
                switch (true) {
                    case $this->_parent instanceof \Form\Element\Fieldset:
                    case $this->_parent instanceof \Form\Element:
                        $name = $this->_parent->getInputName();
                        break;
                    default:
                        $name = $this->_parent->getName();
                }
                if ($name && null !== $this->name) {
                    $name .= '[' . $this->name . ']';
                } else {
                    $name = $this->name;
                }
            } else {
                $name = $this->name;
            }
            $this->_options['inputName'] = $name;
        }

        return $this->_options['inputName'];
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->_options['name'] = $name;

        return $this;
    }

    public function checkValue($value)
    {
        return true;
    }

    public function getValuePresentation()
    {
        return $this->getValue();
    }

    public function getValue()
    {
        if (null === $this->_value && null !== $this->default) {
            //return $this->default;
        }

        return $this->_value;
    }

    public function setValue($value)
    {
        if ($this->checkValue($value)) {
            $this->_value = $this->prepareValue($value);
            $this->setError(null);

            return true;
        } elseif (null !== $value) {
            $this->_value = null;
            if ($this->required) {
                //$this->setError(true);
            } else {
                $this->setError(null);
            }
        }

        return false;
    }

    public function getLabel()
    {
        if (null === $this->_label) {
            $this->_label = new Label(
                array_merge(
                    $this->_options,
                    is_array($this->lableOptions) ? $this->lableOptions : [],
                    ['text' => $this->label]
                )
            );
            $this->_label->setElement($this);
        }

        return $this->_label;
    }

    public function setLabel($label)
    {
        $this->_label = $label;

        return $this;
    }

    public function getError()
    {
        if ($this->_error) {
            return $this->_error;
        }
        if (($this->required && !$this->disabled && $this->isEmpty())) {
            return lang('form_element_error');
        }

        return null;
    }

    public function setError($error)
    {
        $this->_error = $error;

        return $this;
    }

    public function isEmpty()
    {
        return empty($this->_value);
    }

    public function isValid()
    {
        if ($this->_error || ($this->required && !$this->disabled && $this->isEmpty())) {
            return false;
        }

        return true;
    }

    public function isRendered($flag = null)
    {
        if (null === $flag) {
            return $this->_rendered;
        }
        $this->_rendered = (bool)$flag;

        return $this;
    }

    public function renderLabel()
    {
        return $this->getLabel()->render();
    }

    public function renderInput()
    {
        return $this->getHtml();
    }

    public function isFileUpload()
    {
        return false;
    }

    public function reset()
    {
        $this->_value = null;
        $this->_error = null;
        $this->_rendered = false;

        return $this;
    }

    protected function prepareValue($value)
    {
        return $value;
    }

    protected function attrToString()
    {
        $attr = [];

        $attr[] = 'name="' . $this->getInputName() . '"';
        foreach (array_merge(['id', 'class'], $this->_attributes) as $k) {
            if ($this->$k) {
                $attr[] = $k . '="' . $this->$k . '"';
            }
        }
        if ($this->disabled) {
            $attr[] = 'disabled="disabled"';
        }
        if ($this->attr) {
            $attr[] = $this->attr;
        }

        return ' ' . implode(' ', $attr);
    }

    protected function init() {}

    protected function getJsPlugin($options = [])
    {
        if ($this->jsPlugin) {
            if (method_exists($this, 'getJsPluginOptions')) {
                $options = $this->getJsPluginOptions();
            } elseif ($this->jsPluginOptions) {
                $options = $this->jsPluginOptions;
            } else {
                $options = $this->_options;
            }

            return '<script type="text/javascript">$(document).ready(function(){ Form.initElement("' . $this->id . '","' . (true === $this->jsPlugin ? $this->type : $this->jsPlugin) . '",' . json_encode(
                    $options
                ) . '); });</script>';
        }

        return '';
    }

    public function render()
    {
        $this->_rendered = true;
        $s = $this->getHtml();
        if ($this->jsPlugin) {
            $s .= $this->getJsPlugin();
        }

        return $s;
    }


    public static function escape($val)
    {
        if (is_array($val)) {
            $val = implode(',', $val);
        }

        if (is_string($val)) {
            return str_replace('"', '&quot;', $val);
        } else {
            return '';
        }
    }

}