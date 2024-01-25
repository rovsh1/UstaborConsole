Http = new function(){
	
	function HttpRequest(params) {
		
		this.send = function(){
			var response = new HttpResponse(params);
			var ajaxParams = $.extend({}, params);
			ajaxParams.error = function(xhr, status, error){
				response.init({error: {code: status, message: error}}, 'error', xhr);
			};
			ajaxParams.success = function(result, status, xhr){ response.init(result, status, xhr); };
			$.ajax(ajaxParams);
			return response;
		};
	}
	
	function HttpResponse(params) {
		
		this.init = function(result, status, xhr) {
			if (typeof result === 'string' && '{' === result.substr(0, 1))
				result = JSON.parse(result);
			if (result.success === false || result.error)
				status = 'error';
			
			switch (result.action) {
				case 'reload': Http.reload(); return;
				case 'redirect': Http.redirect(result.url); return;
				case 'window-open': WindowManager.open(result.window); break;
			}
			
			//console.log(result, status, xhr);
			this.trigger(status, [result, status, xhr]);
		};
		this.trigger = function(method, args) {
			if (typeof params[method] === 'function')
				params[method].apply(this, args);
		};
	}
	
	var logoutFlag = false;

	function error(xhr, status, error) {
		switch (status) {
			case 'Not found':
				//check auth
				if (logoutFlag) Loader.ping();

				break;
			case ''://offline
			default:
				//nothing
		}
	}
	
	function send(args, params) {
		if (typeof args[1] === 'function') {
			args[2] = args[1];
			args[1] = null;
		}
		if (typeof args[0] === 'object')
			params = $.extend({}, args[0], params);
		else
			params = $.extend({
				url: args[0],
				data: args[1],
				success: args[2],
				dataType: args[3]
			}, params);
		var request = new HttpRequest(params);
		var response = request.send();
	}
	
	this.getJSON = function(url, data, success) { send(arguments, {dataType: 'json'}); };
	this.get = function(url, data, success, dataType) { send(arguments, {method: 'get'}); };
	this.post = function(url, data, success, dataType) { send(arguments, {method: 'post'}); };
	this.ajax = function(data){ send(arguments); };
	this.redirect = function(url) { document.location = url; };
	this.reload = function(noConfirmMsg) {
		if (false !== noConfirmMsg) {
			window.noBefereUnloadMesage = true;
		}
		location.reload(true);
	};
	
};