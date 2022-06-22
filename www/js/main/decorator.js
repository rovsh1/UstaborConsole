const decorators = {
	ondocumentclick: function(el, fn) {
		return function(e) {
			if (el.is(e.target) || el.find(e.target).length)
				return;
			fn(e);
		};
	},
	setLoading: function(el) {
		return function(flag) {
			el[flag ? 'addClass' : 'removeClass']('loading');
			return this;
		};
	}
};