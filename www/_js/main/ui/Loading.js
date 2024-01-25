LoadingMask = {
	show: function($el){
		var mask = $el.find('div.i-loading-mask');
		if (0 === mask.length) {
			mask = $('<div class="i-loading-mask"><div class="indicator"></div></div>').appendTo($el);
		}
		$el.addClass('i-loading');
		mask.show();
		return this;
	},
	hide: function($el) {
		$el.removeClass('i-loading');
		$el.find('div.i-loading-mask').hide();
	},
	set: function($el, flag) {
		this[flag ? 'show' : 'hide']($el);
	}
};