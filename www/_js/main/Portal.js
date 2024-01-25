function PortalApi(id){
	
	var T = this, data;
	var callbacks = [];
	
	this.id = id;
	
	function init() {
		Http.getJSON('/portal/data/' + id + '/', function(r) {
			data = r.data;
			onready();
		});
	}
	function onready() {
		for (var i = 0, l = callbacks.length; i < l; i++) {
			callbacks[i](T);
		}
		callbacks = [];
	}
	
	this.getLanguages = function() { return data.languages; };
	this.getSites = function() { return data.sites; };
	this.getCountries = function() { return data.countries; };
	this.getCategories = function() { return data.categories; };
	this.getTranslation = function(callback) {
		if (data.translation)
			callback(data.translation);
		else {
			Http.getJSON('/portal/translation/' + id + '/', function(r) {
				data.translation = r;
				T.getTranslation(callback);
			});
		}
	};
	this.ready = function(callback) {
		if (data)
			callback(this);
		else
			callbacks[callbacks.length] = callback;
	};
	
	init();
	
}
var Portal = new function() {
	
	var api = [];
	
	this.get = function(id, callback) {
		if (!api[id])
			api[id] = new PortalApi(id);
		api[id].ready(callback);
	};
	
};