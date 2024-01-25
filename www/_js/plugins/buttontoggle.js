$.fn.buttontoggle = function(options){
	var btn = $(this), ready = false;
	var menu = $(options.menu);
	
	function ondocumentclick(e) {
		if (menu.is(e.target) || menu.find(e.target).length)
			return;
		close();
	}
	function open() {
		menu.show();
		if (!ready || options.cache === false) {
			ready = true;
			if (options.url) {
				menu.addClass('loading');
				Http.get(options.url, function(html){
					menu.html(html);
					menu.removeClass('loading');
				});
			}
		}
		$(document)
				.trigger('click')
				.bind('click', ondocumentclick);
	}
	function close() {
		menu.hide();
		$(document).unbind('click', ondocumentclick);
	}
	
	btn.click(function(e){
		e.stopPropagation();
		if (!ready || menu.is(':hidden'))
			open();
		else
			close();
	});
	
	return btn;
};