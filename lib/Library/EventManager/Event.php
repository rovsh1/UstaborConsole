<?php
namespace EventManager;

class Event implements EventInterface{
	
	/**#@+
     * Mvc events triggered by eventmanager
     */
    const EVENT_BOOTSTRAP      = 'bootstrap';
    const EVENT_DISPATCH       = 'dispatch';
    const EVENT_DISPATCH_ERROR = 'dispatch.error';
    const EVENT_FINISH         = 'finish';
    const EVENT_RENDER         = 'render';
    const EVENT_RENDER_ERROR   = 'render.error';
    const EVENT_ROUTE          = 'route';
    /**#@-*/

	/**
	 * @var string Event name
	 */
	protected $name;

	/**
	 * @var string|object The event target
	 */
	protected $target;

	/**
	 * @var array|ArrayAccess|object The event parameters
	 */
	protected $params = array();

	/**
	 * @var bool Whether or not to stop propagation
	 */
	protected $stopPropagation = false;

	public function __construct($name = null, $target = null, $params = null) {
		if (null !== $name) {
			$this->setName($name);
		}
		if (null !== $target) {
			$this->setTarget($target);
		}
		if (null !== $params) {
			$this->setParams($params);
		}
	}

	public function __get($name) {
		switch ($name) {
			case 'name':
			case 'target':
				return $this->$name;
			case 'params':
				return $this->getParams();
		}
		return null;
	}

	/**
	 * Get event name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Get the event target
	 *
	 * This may be either an object, or the name of a static method.
	 *
	 * @return string|object
	 */
	public function getTarget() {
		return $this->target;
	}

	/**
	 * Set the event name
	 *
	 * @param  string $name
	 */
	public function setName($name) {
		$this->name = (string) $name;
	}

	/**
	 * Set the event target/context
	 *
	 * @param  null|string|object $target
	 */
	public function setTarget($target) {
		$this->target = $target;
	}

	/**
	 * Set parameters
	 *
	 * Overwrites parameters
	 *
	 * @param  array|ArrayAccess|object $params
	 * @throws Exception\InvalidArgumentException
	 */
	public function setParams(array $params) {
		$this->params = $params;
	}

	/**
	 * Get all parameters
	 *
	 * @return array|object|ArrayAccess
	 */
	public function getParams() {
		return $this->params;
	}
	
    public function getParam($name, $default = null) {
		return isset($this->params[$name]) ? $this->params[$name] : null;
	}

	/**
	 * Set an individual parameter to a value
	 *
	 * @param  string|int $name
	 * @param  mixed $value
	 */
	public function setParam($name, $value) {
		$this->params[$name] = $value;
	}

	/**
	 * Stop further event propagation
	 *
	 * @param  bool $flag
	 */
	public function stopPropagation($flag = true) {
		$this->stopPropagation = (bool) $flag;
	}

	/**
	 * Is propagation stopped?
	 *
	 * @return bool
	 */
	public function isPropagationStopped() {
		return $this->stopPropagation;
	}

}
