<?php
namespace Console\Command;

use Console\Handler as ConsoleHandler;
use Console\Terminal;
use Exception;

abstract class AbstractCommand{
	
	protected $debug = false;
	private $tokens = [];
	
	public function __construct(array $tokens) {
		$this->tokens = $tokens;
		$this->debug = $this->hasAttribute('d');
		$this->init();
	}
	
	public function getAttribute($name) {
		foreach ($this->tokens as $i => $token) {
			if ($token === '-' . $name || $token === '--' . $name) {
				if (!isset($this->tokens[$i + 1])) {
					throw new Exception('Attribute "' . $name . '" undefined');
				}
				return $this->tokens[$i + 1];
			}
		}
		return null;
	}
	
	public function hasFlag($name, $shortName) {
		$flagName = '--' . $name;
		$flagShortName = '-' . $shortName;
		foreach ($this->tokens as $token) {
			if ($token === $flagName || $token === $flagShortName)
				return true;
		}
		return false;
	}
	
	public function hasAttribute($name) {
		foreach ($this->tokens as $token) {
			if ($token === '-' . $name || $token === '--' . $name)
				return true;
		}
		return false;
	}
	
	public function getArgument($index = 0) {
		$i = 0;
		foreach ($this->tokens as $token) {
			if (0 === strpos($token, '-'))
				continue;
			if ($i === $index)
				return $token;
			$i++;
		}
		return null;
	}
	
	abstract public function main();
	
	protected function init() {}
	
	protected function console($command, array $tokens = null) {
		if (null === $tokens)
			$tokens = $this->tokens;
		$this->debug('run ' . $command);
		ConsoleHandler::command($command, $tokens);
	}
	
	protected function out($result, $color = null, $background = null) {
		if (!is_string($result)) {
			$result = serialize($result);
		}
		Terminal::out($result, $color, $background);
	}
	
	protected function debug($result) {
		if ($this->debug)
			$this->out($result);
	}
	
}