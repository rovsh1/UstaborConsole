function Button(options) {
	
	var i, _this = this, $el;
	
	for (i in options) {
		this[i] = options[i];
	}
	
	this.render = function(container){
		var html = ['<button', (options.cls ? ' class="' + options.cls + '"' : ''), ' type="button">', 
			options.text, 
			'</button>'].join('');
		$el = $(html).click(function(){
			options.handler.call(options.scope, _this);
		}).appendTo(container);
	};
}