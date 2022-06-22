(function(){

	var Bounds = 0,
		defaultOptions = {
			title: '',
			html: '',
			cls: '',
			closeAction: 'remove',
			processHtml: true,
			modal: true,
			autoclose: true,
			closable: true,
			draggable: false,
			buttons: []
		},
		defaultButtons = {
			cancel: {text: 'Отмена', cls: 'btn-cancel', handler: 'close'},
			close: {text: 'Закрыть', cls: 'btn-cancel', handler: 'close'},
			ok: {text: 'ОK', cls: 'btn-submit', handler: 'close'}
		}, defaultHandlers = {
			ok: function(){ this.close(); },
			close: function(){ this.close(); }
		}, $ID = 0;
		
	Window = function(o) {
		var T = this;
		var options = $.extend({}, defaultOptions, o);
		var $html, $el, $body, $id = 'window-' + $ID++;

		function createEl() {
			$el = $(['<div id="' + $id + '" class="window">',
				'<div class="btn-close" title="Закрыть"></div>',
				'<div class="window-title"></div>',
				'<div class="window-body"></div>',
				'<div class="window-buttons"></div>',
				'</div>'].join('')).appendTo(document.body);
			$el.find('div.btn-close').click(function(){ T.close(); });
			$body = $el.find('div.window-body');
		}
		function layout() {
			var Ww = $(window).width(),
				Wh = $(window).height(),
				Wsl = $(window).scrollLeft(),
				Wst = $(window).scrollTop(),
				Ew = $el.outerWidth(),
				Eh = $el.outerHeight(),
				offset;
			if (options.align) {
				var parent = $(options.align.element),
					align = (options.align.position || 'tl-bl').split('-');
				offset = parent.offset();
				if (align[0][0] == 'b') offset.top -= Eh;
				if (align[0][1] == 'r') offset.left -= Ew;
				if (align[1][0] == 'b') offset.top += parent.outerHeight();
				if (align[1][1] == 'r') offset.left += parent.outerWidth();
				if (options.align.offset) {
					offset.left += options.align.offset[0];
					offset.top += options.align.offset[1];
				}
			} else {
				offset = {
					top: (Wh - Eh) / 2 + Wst,
					left: (Ww - Ew) / 2 + Wsl
				};
			}
			if (offset.left < Bounds + Wsl) offset.left = Bounds + Wsl;
			else if (offset.left > Ww + Wsl - (Bounds + Ew)) offset.left = Ww + Wsl - (Bounds + Ew);
			if (offset.top > Wh + Wst - (Bounds + Eh)) offset.top = Wh + Wst - (Bounds + Eh);
			if (offset.top < Bounds + Wst) offset.top = Bounds + Wst;
			$el.css({top: offset.top, left: offset.left});
		}
		function setHtml(html, l) {
			if (html === $html) 
				return;
			$html = html;
			$body.html(html);
			if (options.processHtml) {
				$el.find('a').click(function(e){
					var hash = $(this).attr('href');
					if (0 === hash.indexOf('#window-')) {
						e.preventDefault();
						WindowManager.open(hash.replace('#window-', ''));
					}
				});
				
				var firstEl = $($body[0].firstChild);
				if (firstEl.data('title'))
					T.setTitle(firstEl.data('title'));
				if (firstEl.data('cls'))
					setOption('cls', firstEl.data('cls'));
				
				var form = $el.find('form');
				if (form.length) {
					form.submit(function(e){
						e.preventDefault();
						T.setLoading(true);
						var data = new FormData(this);
						Http.ajax({
							url: form.attr('action') || options.url,
							method: 'post',
							data: data,
							cache: false,
							//dataType: 'json',
							contentType: false,
							processData: false,
							success: function(r) {
								if (typeof (r) === 'string')
									setHtml(r, true);
								T.setLoading(false);
							}
						});
					});
				}
			}
			if (l)
				layout();
		}
		function ondocumentclick(e) {
			if (!$el.is(e.target) && $el.find(e.target).length == 0)
				T.close();
		}
		function onresize() { layout(); }
		function setOption(name, value) {
			switch (name) {
				case 'cls': $el.addClass(value); break;
				case 'title': T.setTitle(value); break;
				case 'url': T.load(value); break;
				case 'html': setHtml(value); break;
				case 'loading': T.setLoading(true); break;
				case 'modal': WindowManager.shadow(true); break;
				//case 'renderTo':$el.appendTo(value);break;
				case 'draggable':
					if (value)
						$el.draggable({
							handle: 'div.window-title',
							opacity: 0.8,
							//grid: [5, 5],
							containment: document.body,
							scroll: false,
							stop: function(e, ui) {
								options.left = ui.position.left;
								options.top = ui.position.top;
							}
						});
					break;
			}
		}
		
		this.load = function(url) {
			this.setLoading(true);
			$.get(url, function(html){
				T.setHtml(html);
				T.setLoading(false);
				if (options.load)
					options.load();
			});
			return this;
		};
		this.setTitle = function(title) {
			$el.find('div.window-title').html(title);
			return this;
		};
		this.setHtml = function(html) {
			setHtml(html, true);
			return this;
		};
		this.getEl = function() { return $el; };
		this.show = function(cfg) {
			if (cfg) {
				options = $.extend({}, options, cfg);
				options.html = options.html || options.message || options.text;
			}
			if (!$el) createEl();
			$el.show();
			if (options.autoclose && !options.modal) {
				window.setTimeout(function(){
					$(document)
						.unbind('click', ondocumentclick)
						.click(ondocumentclick);
				}, 50);
			}
			$el.find('div.btn-close')[options.closable ? 'show' : 'hide']();

			var buttonsEl = $el.find('div.window-buttons');
			if (options.buttons && options.buttons.length) {
				buttonsEl.html('').show();
				var button, i = 0, l = options.buttons.length;
				for (;i < l; i++) {
					button = options.buttons[i];
					if (typeof button === 'string') {
						button = defaultButtons[button];
					}
					if (typeof button.handler === 'string') {
						if (defaultHandlers[button.handler]) {
							button.handler = defaultHandlers[button.handler];
						} else {
							button.handler = window[button.handler];
						}
					}
					if ((i + 1) === l) {
						button.cls = (button.cls ? button.cls + ' last' : 'last');
					}
					button.scope = T;
					if (!(button instanceof Button))
						button = new Button(button);
					button.render(buttonsEl);
				}
			} else {
				buttonsEl.hide();
			}

			for (i in options)
				setOption(i, options[i]);
			layout();
			if (!options.loading && !options.url) this.setLoading(false);
			$(window).resize(onresize);
			return this;
		};
		this.close = function(action) {
			if ($el) {
				this.setLoading(false);
				var a = action || options.closeAction;
				$el[a]();
				if (a === 'remove') {
					$el = null;
					$html = null;
				}
			}
			$(window).unbind('resize', onresize);
			$(document).unbind('click', ondocumentclick);
			WindowManager.shadow(false);
			if (action && options[action]) options[action]();
			if (options.close) {call_user_func(options.close)}
		};
		this.layout = function() { layout(); };
		this.setLoading = function(flag) {
			if ($el)
				$el[flag ? 'addClass' : 'removeClass']('loading');
		};
		
		if (o && !o.hidden) this.show(o);

	};
	
})();
