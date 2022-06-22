(function(){
	
	var metaValues = [{
		name: '',
		values: [],
		extra: [
			{value: '<extends name="default" />', label: 'import metadata'},
			{value: '<title></title>', label: 'Page title'},
			{value: '<base href="" />', label: 'basehref'}
		]
	}, {
		tag: 'meta',
		name: 'Main',
		attributes: {name: '', content: ''},
		valueAttribute: 'name',
		values: ['title', 'keywords', 'description', 'title_prefix', 'language', 'robots', 'copyright', 'viewport', 'application-name']
	}, {
		tag: 'link',
		name: 'Links',
		attributes: {rel: '', href: ''},
		valueAttribute: 'rel',
		values: ['icon', 'shortcut icon', 'publisher', 'dns-prefetch', 'video_src', 'apple-touch-icon', 'canonical', 'alternate', 'next', 'prev']
	}, {
		tag: 'meta',
		name: 'Http meta',
		attributes: {'http-enquiv': '', content: ''},
		valueAttribute: 'http-enquiv',
		values: ['X-UA-Compatible', 'Content-Type', 'Content-language', 'x-dns-prefetch-control']
	}, {
		tag: 'meta',
		name: 'Applications',
		attributes: {name: '', content: ''},
		valueAttribute: 'name',
		values: ['google-site-verification', 'yandex-verification', 'apple-itunes-app', 'google-play-app', 'fb:app_id']
	}, {
		tag: 'meta',
		name: 'Open Graph',
		attributes: {name: '', content: ''},
		valueAttribute: 'name',
		values: ['og:title',
			'og:type',
			'og:url',
			'og:site_name',
			'og:description',

			// Контактная информация
			'og:email',
			'og:phone_number',
			'og:fax_number',
			// Месторасположение
			'og:locale',
			'og:locale:alternate',
			'og:latitude',
			'og:longitude',
			'og:street-address',
			'og:locality',
			'og:region',
			'og:postal-code',
			'og:country-name',
			//image
			'og:image',
			'og:image:secure_url',
			'og:image:type',
			'og:image:width',
			'og:image:height',
			// Видео
			'og:video',
			'og:video:secure_url',
			'og:video:type',
			'og:video:height',
			'og:video:width',
			// Аудио
			'og:audio',
			'og:audio:type',
			'og:audio:secure_url',
			'og:audio:title',
			'og:audio:artist',
			'og:audio:album']
	}, {
		tag: 'meta',
		name: 'Twitter',
		attributes: {name: '', content: ''},
		valueAttribute: 'name',
		values: ['twitter:account_id', 'twitter:title', 'twitter:description', 'twitter:image']
	}, {
		tag: 'meta',
		name: 'MSApplication',
		attributes: {name: '', content: ''},
		valueAttribute: 'name',
		values: ['msapplication-starturl', 'msapplication-TileColor', 'msapplication-tooltip', 'msapplication-TileImage', 'msapplication-task']
	}], _list, ID = 0;

	function getAutocompleteList(request, response) {
		if (!_list) {
			_list = [];
			var i, l = metaValues.length, j, n, mv, a;
			var meta = new Meta();
			for (i = 0; i < l; i++) {
				mv = metaValues[i];
				if (mv.extra) {
					n = mv.extra.length;
					for (j = 0; j < n; j++) {
						mv.extra[j].group = mv.name;
						_list[_list.length] = mv.extra[j];
					}
				}
				n = mv.values.length;
				if (!mv.tag)
					continue;
				meta.setHtml('<' + mv.tag + '>');
				meta.setAttributes(mv.attributes);
				for (j = 0; j < n; j++) {
					meta.setAttribute(mv.valueAttribute, mv.values[j]);
					_list[_list.length] = {value: meta.getHtml(), label: mv.values[j], group: mv.name};
				}
			}
		}
		if (request.term === '')
			return response(_list);
		var result = [], i, l = _list.length,
			key = request.term.indexOf('<') === 0 ? 'value' : 'label',
			term = request.term.toLowerCase();
		for (i = 0; i < l; i++) {
			if (0 === _list[i][key].toLowerCase().indexOf(term))
				result[result.length] = _list[i];
		}
		response(result);
	}
	
	function Head($el, options) {
		var $textarea, $code, $btn;
		var form = new Form();
		var T = this, metaItems = [];
		
		this.init = function() {
			$textarea = $el.find('input').hide();
			$code = $('<code class="language-html editable"></code>').appendTo($el);
			$btn = $('<div class="btn-add"><a href="#" id="btn-add-meta">+Add meta tag</a></div>').appendTo($el);

			form.save = function(meta){
				var m;
				if (form.id === 'new') {
					m = T.add(new Meta());
				} else {
					m = T.get(form.id);
				}
				m.setHtml(meta.getHtml());
				T.update(true);
			};
			form.delete = function() {
				T.remove(form.id);
				T.update(true);
			};
			$el.on('click', 'div.meta', function(e){
				var id = $(this).data('id');
				form.open(id, T.get(id).getHtml());
			});
			$btn.click(function(e) { e.preventDefault();form.open('new'); });
			
			this.setHtml($textarea.val());
			
			$code.sortable({
				axis: 'y',
				update: function() { T.setHtml($code.text(), true); }
			});
			$code.disableSelection();
		};
		this.get = function(id) {
			var i, l = metaItems.length;
			for (i = 0; i < l; i++) {
				if (metaItems[i].id == id)
					return metaItems[i];
			}
			return null;
		};
		this.add = function(meta) {
			metaItems[metaItems.length] = meta;
			meta.id = 'hm_' + (ID++);
			return meta;
		};
		this.remove = function(id) {
			var i, l = metaItems.length;
			for (i = 0; i < l; i++) {
				if (metaItems[i].id != id)
					continue;
				metaItems.splice(i, 1);
				break;
			}
		};
		this.setHtml = function(html, upd) {
			metaItems = [];
			var m = html.match(/<(?:\w+)(?:[^>]*>)(?:([^<]*)(?:<\/\w+>))?/ig);
			if (m) {
				var i, mi;
				for (i = 0; i < m.length; i++) {
					mi = new Meta();
					mi.setHtml(m[i]);
					this.add(mi);
				}
			}
			if (false !== upd)
				this.update(upd);
		};
		this.getHtml = function(highlight) {
			var html = [], i, l = metaItems.length;
			for (i = 0; i < l; i++) {
				html[html.length] = metaItems[i].getHtml(highlight);
			}
			return html.join("\n");
		};
		this.update = function(textarea) {
			$code.html(this.getHtml(true));
			if (textarea)
				$textarea.val(this.getHtml());
		};
	}
	function Meta() {
		var tag, closeTag, attributes, content;
		var nameAttr, contentAttr;
		this.getTag = function() { return tag; };
		this.hasCloseTag = function() { return closeTag; };
		this.isEmpty = function() { return !tag; };
		this.setAttribute = function(name, value) { attributes[name] = value; };
		this.getAttribute = function(name) { return attributes[name]; };
		this.getAttributes = function() { return attributes; };
		this.setAttributes = function(array) {
			for (var i in array) {
				this.setAttribute(i, array[i]);
			}
		};
		this.removeAttribute = function(name) { delete attributes[name]; };
		this.getContent = function() { return content; };
		this.setHtml = function(html) {
			tag = null;
			attributes = {};
			content = '';
			closeTag = false;
			nameAttr = null;
			contentAttr = null;
			
			if (!html)
				return;
			
			var m = html.match(/<(\w+)(?:[^>]*>)(?:([^<]*)(<\/\w+>))?/i);
			if (!m)
				return;
			content = m[2];
			closeTag = !!m[3];
			tag = m[1];
			var attr = html.match(/(\b(?:\w|-)+\b)\s*=\s*(?:"([^"]*)")/ig);
			if (attr) {
				var i, l = attr.length;
				for (i = 0; i < l; i++) {
					m = attr[i].match(/(\b(?:\w|-)+\b)\s*=\s*(?:"([^"]*)")/i);
					attributes[m[1]] = m[2];
				}
			}
			if (tag === 'meta') {
				contentAttr = 'content';
				if (attributes['http-enquiv'])
					nameAttr = 'http-enquiv';
				else if (attributes.name)
					nameAttr = 'name';
				else if (attributes.property)
					nameAttr = 'property';
			} else if (tag === 'link') {
				nameAttr = 'rel';
				contentAttr = 'href';
			} else if (tag === 'script') {
				closeTag = true;
			} else if (tag === 'style') {
				closeTag = true;
			}
		};
		this.getHtml = function(highlight) {
			if (this.isEmpty())
				return '';
			var i, s;
			if (highlight) {
				s = '<div class="meta" data-id="' + this.id + '"><span class="name">&lt;' + tag + '</span>';
				for (i in attributes) {
					s += ' <span class="attr">' + i + '</span>=<span class="string">&quot;' + attributes[i] + '&quot;</span>';
				}
				s += closeTag ? ('<span class="name">&gt;</span>' + content + '<span class="name">&lt;/' + tag+ '&gt;</span>') : ' <span class="name">/&gt;</span>';
				s += '</div>';
			} else {
				s = '<' + tag;
				for (i in attributes) {
					s += ' ' + i + '="' + attributes[i] + '"';
				}
				s += closeTag ? ('>' + content + '</' + tag+ '>') : ' />';
			}
			return s;
		};
	}
	function FormAttr(form, meta, attrName) {
		
		this.name = attrName || '';
		
		var T = this, $el;
		
		this.getEl = function() {
			if (!$el) {
				$el = $('<div class="attr">'
					+ '<input type="text" class="name" value="' + this.name + '" />'
					+ '<input type="text" class="value" />'
					+ '<div class="btn-delete"></div>'
					+ '</div>');
				$el.find('input').focus(function(){ this.select(); });
				$el.find('input.name').change(function(){
					var name = $(this).val();
					if (form.getAttribute(name)) {
						$(this).val(T.name).select();
					} else {
						T.name = name;
						meta.setAttribute(name, T.getValue());
						form.update();
						T.focus('value');
					}
				});
				$el.find('input.value').change(function(){
					if (T.name) {
						meta.setAttribute(T.name, this.value);
						form.update();
					}
				});
				$el.find('div.btn-delete').click(function(){ form.removeAttribute(T.name); });
				if (this.name) {
					this.setValue(meta.getAttribute(this.name));
				} else {
					
				}
			}
			return $el;
		};
		this.setValue = function(value) { $el.find('input.value').val(value); };
		this.getValue = function() { return $el.find('input.value').val(); };
		this.focus = function(name) { $el.find('input.' + (name || 'name')).focus(); };
		this.remove = function() {
			if ($el)
				$el.remove();
		};
		this.getContent = function() { return ''; };
	}
	function Form() {
		var $w, $el, $attributes, $textarea;
		var T = this;
		var meta, attributes = [];
		
		this.open = function(id, html) {
			if (!$w) {
				$w = WindowManager.open({
					title: 'Meta tag editor',
					cls: 'window-page-metadata',
					html: '<div class="code"><code class="language-html"></code><textarea></textarea></div>'
							+ '<div class="attributes-wrap">'
							+ '<div class="attributes"></div>'
							+ '<div class="btns"><div class="btn-add">+ Add attribute</div></div>'
							+ '</div>'
							+ '',
					modal: true,
					closeAction: 'hide',
					buttons:[{
						text: 'Save',
						cls: 'btn-submit',
						handler: function() { T.save(meta);this.close(); }
					}, 'cancel', {
						text: 'Delete',
						cls: 'btn-delete',
						handler: function() { T.delete();this.close(); }
					}]
				});
				$el = $w.getEl();
				meta = new Meta();
				$attributes = $el.find('div.attributes');
				$textarea = $el.find('textarea');
				$textarea
					.textareaAutoHeight()
					.change(function(){
						meta.setHtml(this.value);
						T.update(true);
					})
					.blur(function(){
						if (!meta.isEmpty())
							T.toggleMode();
					})
					.keydown(function(e){
						if (e.keyCode === 13) {
							e.preventDefault();
							$textarea.blur();
						}
					})
					.groupcomplete({
						minLength: 0,
						delay: 100,
						source: getAutocompleteList,
						classes: {'ui-autocomplete': 'page-meta'},
						select: function(e, ui) { $textarea.val(ui.item.value).change().blur(); }
					})
					.focus(function(){ $textarea.groupcomplete('search'); });
				$el.find('div.btn-add').click(function(){ T.addAttribute('', true); });
				$el.find('code').click(function(){ T.toggleMode(); });
			} else {
				$w.show();
			}
			this.id = id;
			$el.find('button.btn-delete')[id === 'new' ? 'hide' : 'show']();
			meta.setHtml(html);
			//if (meta.hasCloseTag())
			//	$textarea.val(meta.getContent()).trigger('update-height').show();
			this.update(true);
			if (meta.isEmpty())
				this.toggleMode();
		};
		this.getAttribute = function(name) {
			var i, l = attributes.length;
			for (i = 0; i < l; i++) {
				if (attributes[i].name === name)
					return attributes[i];
			}
			return null;
		};
		this.addAttribute = function(name, focus) {
			var attr;
			if ((attr = this.getAttribute(name))) {
				
			} else {
				attr = new FormAttr(this, meta, name);
				attributes[attributes.length] = attr;
				$attributes.append(attr.getEl());
			}
			if (focus)
				attr.focus();
		};
		this.removeAttribute = function(name) {
			var i, l = attributes.length;
			for (i = 0; i < l; i++) {
				if (attributes[i].name !== name)
					continue;
				attributes[i].remove();
				if (attributes[i].name)
					meta.removeAttribute(attributes[i].name);
				attributes.splice(i, 1);
				this.update();
				break;
			}
		};
		this.toggleMode = function() {
			if ($el.hasClass('expanded')) {
				$el.removeClass('expanded');
				$w.layout();
			} else {
				$el.addClass('expanded');
				$w.layout();
				$textarea
						.val(meta.getHtml())
						.trigger('update-height')
						.focus();
			}
		};
		this.update = function(attr) {
			$el.find('code').html(meta.getHtml(true));
			if (attr) {
				attributes = [];
				$attributes.html('');
				var metaAttributes = meta.getAttributes(),
					i;
				for (i in metaAttributes)
					T.addAttribute(i);
			}
			$w.layout();
		};
	}
	
	$.fn.headmeta = function(options){
		var head = new Head($(this), options);
		head.init();
	};

})();

