<?php
namespace Http\Content;

use Http\Util as HttpUtil;
use File\AbstractFile;

abstract class AbstractContent{
	
	protected $fileExtension = '*';
	protected $includePath = '';
	protected $headers = [];
	protected $content = [];
	protected $params = [];
	
	abstract protected function init();
	
	public function __construct() {
		$this->init();
	}
	
	public function set($name, $value) {
		$this->params[$name] = $value;
		return $this;
	}
	
	public function get($name) {
		return isset($this->params[$name]) ? $this->params[$name] : null;
	}
	
	public function enableCache($destination, $time = null) {
		if ($time === null) {
			$time = 2592000;//month
		}
		$this->setHeader('Cache-Control', 'private, max-age=' . $time . ', pre-check=' . $time);
		//$this->addHeader('Pragma', 'private');
		$this->setHeader('Expires', date('r', time() + $time));

		$fileModTime = filemtime($destination);

		$headers = HttpUtil::getHeaders();

		if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $fileModTime)) {
			$this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', $fileModTime) . ' GMT', true, 304);
			$this->outHeaders();
			exit;
		}
		$this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', $fileModTime) . ' GMT', true, 200);
		return $this;
	}

	public function setIncludePath($path) {
		$this->includePath = $path;
		return $this;
	}
	
	public function setHeader($name, $header, $replace = true, $code = null) {
		$this->headers[$name] = [$header, $replace, $code];
		return $this;
	}
	
	public function setHeaderIf($name, $header, $replace = true, $code = null) {
		if (!isset($this->headers[$name]))
			$this->setHeader($name, $header, $replace, $code);
		return $this;
	}
	
	public function setContentType($type, $charset = false) {
		if ($charset === 'default' || $charset === true)
			$charset = 'utf-8';
		return $this->setHeader('Content-Type', $type . ($charset ? '; charset=' . $charset : ''));
	}
	
	public function addFile($file) {
		if (is_array($file)) {
			foreach ($file as $fp) {
				$this->addFile($fp);
			}
		} elseif (is_string($file)) {
			if (isset($this->content[$file])) {
				return $this;
			}
			$this->content[$file] = file_get_contents($this->includePath . $file);
		} elseif ($file instanceof AbstractFile) {
			$this->addContent($file->getData());
		}
		return $this;
	}
	
	public function addDir($dir, $deep = true) {
		if (is_array($dir)) {
			foreach ($dir as $d) {
				$this->addDir($d);
			}
			return $this;
		}
		$path = $this->includePath . rtrim($dir, DIRECTORY_SEPARATOR);
		$deepDirs = [];
		$dh = opendir($path);
		while (false !== ($file = readdir($dh))) {
			if (in_array($file, ['.', '..']))
				continue;
			$filepath = $path . DIRECTORY_SEPARATOR . $file;
			if (is_dir($filepath)) {
				if ($deep) {
					$deepDirs[] = $dir . DIRECTORY_SEPARATOR . $file;
				}
			} else {
				if ('*' === $this->fileExtension || substr($file, strrpos($file, '.')) == ('.' . $this->fileExtension)) {
					if (!file_exists($filepath)) {
						die($filepath);
					}
					$this->addFile($dir . DIRECTORY_SEPARATOR . $file);
				}
			}
		}
		foreach ($deepDirs as $dir) {
			$this->addDir($dir, $deep);
		}
		return $this;
	}
	
	public function setContent($content) {
		$this->content = [$content];
		return $this;
	}
	
	public function addContent($content) {
		$this->content[] = $content;
		return $this;
	}

	public function getContent() {
		return implode("\n", $this->content);
	}
	
	public function sendHeaders() {
		if (!isset($this->headers['Content-Length']))
			$this->setHeader('Content-Length', strlen($this->getContent()));
		foreach ($this->headers as $name => $value) {
			header($name . ': ' . $value[0], $value[1], $value[2] ?? 0);
		}
	}
	
	public function out() {
		$this->sendHeaders();
		die($this->getContent());
	}
	
}