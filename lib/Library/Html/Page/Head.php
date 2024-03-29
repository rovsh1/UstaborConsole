<?php
namespace Html\Page;

use Html\Page\Head\AbstractMeta;
use Html\Page\Head\BaseHref;
use Html\Page\Head\Meta;
use Html\Page\Head\Link;
use Html\Page\Head\Script;
use Html\Page\Head\Style;

class Head{
	
	protected $title = '';
	protected $relativePath = '/';
	protected $headAttr = '';
	protected $baseHref;
	protected $meta = [];
	protected $styles = [];
	protected $scripts = [];
	protected $contents = [];
	
	public function fromString($html) {
		if (empty($html))
			return;
		//if (preg_match('/<title>(.*)<\/title>/iU', $html, $m))
		//	$this->setTitle($m[1]);
		//preg_match_all('/<(\w+)(?:\s.*)>/imU', $html, $mTag, PREG_SET_ORDER);
		preg_match_all('/<(\w+\b)(?:([^>]*)\/?>)(?:([^<]*)(?:<\/\w+>))?/im', $html, $mTag, PREG_SET_ORDER);
		if (!$mTag)
			return;
		foreach ($mTag as $tag) {
			$attr = [];
			preg_match_all('/(\b(?:\w|-)+\b)\s*=\s*(?:"([^"]*)")/imU', $tag[0], $mAttr, PREG_SET_ORDER);
			if ($mAttr) {
				foreach ($mAttr as $m) {
					$attr[$m[1]] = $m[2];
				}
			}
			if ($tag[1] === 'title' && isset($tag[3])) {
				$this->setTitle($tag[3]);
			} elseif ($tag[1] === 'meta') {
				$this->addMeta($attr);
			} elseif ($tag[1] === 'base')
				$this->setBaseHref($attr['href']);
			elseif ($tag[1] === 'link')
				$this->addLink($attr);
			else
				$this->addContent($tag[0]);
			
			//else var_dump($tag[1], $attr);
		}
		//echo $html, "\n\n",$this->getHtml();
		//exit;
	}
	
	public function setHeadAttr($attr) {
		$this->headAttr = $attr;
		return $this;
	}
	
	public function setRelativePath($path) {
		$this->relativePath = $path;
		return $this;
	}
	
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	
	public function setKeywords($keywords) {
		$this->addMetaName('keywords', $keywords);
		return $this;
	}
	
	public function setDescription($description) {
		$this->addMetaName('description', $description);
		return $this;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setBaseHref($href) {
		$this->baseHref = new BaseHref($href);
		return $this;
	}
	
	public function addContent($content) {
		$this->contents[] = $content;
		return $this;
	}
	
	public function addMeta(array $attributes) {
		return $this->_addMeta(new Meta($attributes));
	}
	
	public function addLink(array $attributes) {
		return $this->_addMeta(new Link($attributes));
	}
	
	public function addLinkRel($rel, $href, array $attributes = []) {
		$attributes['rel'] = $rel;
		$attributes['href'] = $href;
		return $this->addLink($attributes);
	}
	
	public function addMetaName($name, $content, array $attributes = []) {
		$attributes['name'] = $name;
		$attributes['content'] = $content;
		return $this->addMeta($attributes);
	}
	
	public function addMetaProperty($property, $content, array $attributes = []) {
		$attributes['property'] = $property;
		$attributes['content'] = $content;
		return $this->addMeta($attributes);
	}
	
	public function addMetaHttpEquiv($keyValue, $content, array $attributes = []) {
		$attributes['http-equiv'] = $keyValue;
		$attributes['content'] = $content;
		return $this->addMeta($attributes);
	}
	
	public function addStyle($href, array $attributes = []) {
		$this->styles[] = new Style($this->url($href, 'css'), $attributes);
		return $this;
	}
	
	public function addScript($src, array $attributes = []) {
		$this->scripts[] = new Script($this->url($src, 'js'), $attributes);
		return $this;
	}
	
	public function url($url, $path = null) {
		if (substr($url, 0, 1) !== '/' && substr($url, 0, 4) !== 'http') {
			return $this->relativePath . ($path ? $path . '/' : '') . $url;
		}
		return $url;
	}
	
	public function getHtml($format = null) {
		$html = '';
		switch ($format) {
			case 'scripts':
				foreach ($this->scripts as $script)
					$html .= $script->getHtml() . "\n";
				return $html;
			case 'styles':
				foreach ($this->styles as $style)
					$html .= $style->getHtml() . "\n";
				return $html;
			case 'footer':
				//$html .= $this->getHtml('styles');
				$html .= $this->getHtml('scripts');
				$html .= '<noscript>';
				$scripts = '';
				foreach ($this->styles as $style) {
					$scripts .= '$("head").append(\'' . $style->getHtml() . '\');';
					$html .= $style->getHtml();
				}
				$html .= '</noscript>';
				$scripts .= 'for (var i=0,l=$q.length;i<l;i++){$q[i]();}delete window.$q;';
				$html .= '<script>$(document).ready(function(){' . $scripts . '});</script>';
				return $html;
				
		}
		$html .= '<head' . ($this->headAttr ? ' ' . $this->headAttr : '') . '>' . "\n";
		$html .= '<title>' . $this->title . '</title>' . "\n";
		
		/*$assoc = array(
			'og:title' => 'title',
			'og:description' => 'description',
			'twitter:title' => 'title',
			'twitter:description' => 'description',
			'twitter:url' => 'og:url',
			'twitter:image' => 'og:image'			
		);*/
		
		foreach ($this->meta as $meta) {
			$html .= $meta->getHtml() . "\n";
		}
		
		if ($format === 'async') {
			$html .= '<script>(function(){window.$q=[];window.$=function(){return {ready:function(fn){window.$q.push(fn);}};};})();</script>' . "\n";
		} else {
			$html .= $this->getHtml('scripts');
			$html .= $this->getHtml('styles');
		}
		
		if ($this->baseHref)
			$html .= $this->baseHref->getHtml() . "\n";
		
		$html .= implode("\n", $this->contents);
		
		$html .= '</head>';
		return $html;
	}
	
	protected function _addMeta(AbstractMeta $meta) {
		$uid = $meta->getIdentifier();
		if ($uid)
			$this->meta[$uid] = $meta;
		else
			$this->meta[] = $meta;
		return $this;
	}
	
}