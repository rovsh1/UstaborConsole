<?php
namespace Api\Model\Report;

class File{
	
	private $data = array();
	private $handle;
	private $charset;
	
	public function __construct($name) {
		$this->name = $name;
		$this->empty = true;
	}
	
	public function __destruct() {
		$this->close();
		//$this->unlink();
	}
	
	public function __set($name, $value) {
		$this->data[$name] = $value;
	}
	
	public function __get($name) {
		switch($name) {
			case 'empty':
				return !$this->filename || !file_exists($this->filename) || $this->size <= 3;
			case 'size':
				return filesize($this->filename);
			case 'content':
				return file_get_contents($this->filename);
		}
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}
	
	public function setCharset($charset) {
		$this->charset = $charset;
		return $this;
	}

	public function addRow($row) {
		if ($this->handle) {
			fputcsv($this->handle, $row, ";");
		}
		return $this;
	}
	
	public function write($s) {
		if ($this->handle)
			fputs($this->handle, $s);
		return $this;
	}
	
	public function getContents() {
		return $this->empty ? '' : file_get_contents($this->filename);
	}
	
	public function open($tmpname = null) {
		if ($tmpname) {
			$filename = TEMP_PATH . '/' . $tmpname;
			if (file_exists($filename)) {
				$this->tmpname = $tmpname;
				$this->filename = $filename;
				$this->handle = fopen($filename, 'a+');
			} else {
				
			}
		} else {
			$filename = tempnam(TEMP_PATH, 'csv');
			$this->handle = fopen($filename, 'w+');
			$this->write(chr(0xEF) . chr(0xBB) . chr(0xBF)); //write BOM
			$this->tmpname = basename($filename);
			$this->filename = $filename;
		}
		return (bool)$this->handle;
	}
	
	public function close() {
		if ($this->handle) {
			fclose($this->handle);
		}
		$this->handle = null;
		return $this;
	}
	
	public function unlink() {
		$this->close();
		unlink($this->filename);
	}
	
	public function output() {
		if ($this->empty)
			exit;
		header('Pragma: public');
		header('Content-type: text/csv');
		header('Content-Length: ' . $this->size);
		header('Content-disposition: attachment;filename="' . $this->name . '"');
		echo $this->content;
		$this->unlink();
		exit;
	}
	
}