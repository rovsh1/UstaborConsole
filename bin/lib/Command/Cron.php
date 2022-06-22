<?php
namespace Console\Command;

class Cron extends AbstractCommand{
	
	public function main() {
		switch ($this->getArgument()) {
			case 'daily':
				$this->console('master:update-master-index');
				$this->console('master:send-master-alerts');
				break;
			case 'weekly':
				$this->console('admin:master-without-projects');
				break;
			default:
				return $this->help();
		}
	}
	
	public function help() {
		
	}
	
	public function test() {
		$this->out('test output "' . php_sapi_name() . '"');
	}
	
}