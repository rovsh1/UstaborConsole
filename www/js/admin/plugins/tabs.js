$.fn.tabs = function(options) {
	var el = $(this),
		ul = el.find('ul.tabs');
	if (options === 'select') {
		var tab = arguments[1];
		ul.find('li').each(function(){
			if ($(this).data('tab') === tab) {
				$(this).click();
				return false;
			}
		});
		return el;
	}
	options = $.extend({
		hash: true,
		tabSelector: 'div.tab'
	},options);
	function select() {
		var tab = $(this);
		if (tab.hasClass('current'))
			return;
		el.find(options.tabSelector).hide();
		ul.find('li.current').removeClass('current');
		tab.addClass('current');
		$('#' + tab.data('tab')).show();
		if (options.hash) {
			if (tab.is(':first-child')) {
				if (location.hash) {
					history.replaceState(null, null, location.pathname + location.search);
				}
			} else {
				history.replaceState(null, null, location.pathname + location.search + '#!' + tab.data('tab'));
			}
		}
		el.trigger('tab-select', tab.data('tab'));
	}
	function initCurrent() {
		if (options.hash && location.hash) {
			var hash = location.hash.replace(/^[#!]+/, '');
			ul.find('li').each(function(){
				if ($(this).data('tab') === hash) {
					$(this).click();
					return false;
				}
			});
		} else {
			ul.find('li:first-child').click();
		}
	}

	ul.find('li').click(select);
	initCurrent();
	var cur = ul.find('li.current');
	if (!cur.length)
		ul.find('li:first-child').click();
	if (options.hash)
		window.addEventListener("popstate", initCurrent);
};