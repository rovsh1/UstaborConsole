<?php
namespace Console\Command;

class Test extends AbstractCommand{
	
	public function main() {
		switch ($this->getArgument()) {
			default:
				return $this->help();
		}
	}
	
	public function help() {
		$this->out('color red', 'red');
		$this->out('background cyan', 'cyan');
		$this->out('flag -d: ' . ($this->hasFlag('debug', 'd') ? 'yes' : 'no'), 'cyan');
	}
	
}