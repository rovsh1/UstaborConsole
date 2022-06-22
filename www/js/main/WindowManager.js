WindowManager = new function(){
	
	var current, _extends = {};
	var $shadow;
	
	this.add = function(name, params) {
		_extends[name] = params;
		return this;
	};
	this.open = function(params) {
		if (typeof params === 'string' && _extends[params])
			params = _extends[params];
		params = $.extend({}, params);
		var en;
		while (params.extend) {
			en = params.extend;
			delete params.extend;
			params = $.extend({}, _extends[en], params);
		}			
		this.close();
		current = new Window(params);
		return current;
	};
	this.close = function() {
		if (current)
			current.close();
	};
	this.getWindow = function() { return current ? current : null; };
	this.shadow = function(flag) { $shadow[flag ? 'show' : 'hide'](); };
	
	var WindowHasFocus = true, holdQueue = [], holdParams,
		handlers = [];

	function getHoldParams() {
		var params = holdParams;
		if (!params) {
			var i, l = holdQueue.length;
			for (i = 0; i < l;i++) {
				if (holdQueue[i][0]()) {
					params = holdQueue[i][1];
					break;
				}
			}
		}
		if (typeof (params) === 'string') {
			params = {message: params};
		}
		return params;
	}
	function fn(action, args) {
		var i, l = handlers.length;
		for (i = 0; i < l;i++) {
			if (handlers[i][0] !== action) continue;
			if (false === handlers[i][0].apply(WindowManager, args)) {
				return false;
			}
		}
	}
	function onBeforeUnload(callback) {
		var params = getHoldParams();
		if (params) {
			MessageBox({
				title: params.title || 'Подтверждение',
				text: params.message + ' Обновить страницу?',
				buttons: [{
					text: 'Обновить',
					cls: 'button-green',
					handler: function(){
						this.close();
						WindowManager.unhold();
						callback();
					}
				}, 'cancel']
			});
		} else {
			callback();
		}
	}

	this.init = function() {
		$shadow = $('<div class="shadow"></div>').appendTo(document.body);
		$(window)
			.blur(function(){ WindowHasFocus = false; })
			.bind('focus click', function(){
				WindowHasFocus = true;
				//BrowserNotifications.reset();
			});
		window.onbeforeunload = function(){
			var params = getHoldParams();
			if (params) {
				return params.message;
			}
		};
	};
	this.bind = function(action, fn, params) {
		handlers.push([action, fn, params]);
		return this;
	};
	this.hold = function(params) {
		holdParams = params;
		return this;
	};
	this.holdIf = function(fn, params) {
		holdQueue.push([fn, params]);
		return this;
	};
	this.unhold = function(queue) {
		holdParams = null;
		if (queue) holdQueue = [];
		return this;
	};
	this.isHolding = function() { return !!getHoldParams(); };
	this.reload = function() { onBeforeUnload(function(){ location.reload(); }); };
	this.assign = function(url) {
		if (false === fn('assign', [url])) {
			return false;
		}
		onBeforeUnload(function(){ location = url; });
	};
	this.url = function(url) { return this.assign(url); };
	this.hasFocus = function() { return WindowHasFocus; };
	
};