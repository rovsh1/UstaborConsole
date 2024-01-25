(function(){
	
	$.fn.buttonmenu = function(options){
		var outer = $(this),
			menu, disabled = false, mt;
			
		options = $.extend({
			click: true,
			mouseover: true,
			buttonCls: 'buttonmenu-button'
		}, options);
		
		if (options.buttonCls) outer.addClass('');
	
		menu = new Menu({
			renderTo: outer,
			cls: 'buttonmenu',
			items: options.items,
			handler: options.handler
		});
		
		function settext(e, text) {
			//el.html(text);
		}
		function onclick(e) {
			if (disabled) return false;
			onmouseout();
			if (menu.isHidden()) menu.show();
			else menu.close();
			return false;
		}
		function onmouseover() {
			if (disabled) return false;
			onmouseout();
			mt = setTimeout(function(){menu.show();}, 500);
		}
		function onmouseout() {
			if (mt) {
				clearTimeout(mt);
				mt = null;
			}
		}
		function disable() {
			menu.setLoading(true);
			disabled = true;
			outer.addClass('disabled');
		}
		function enable() {
			menu.setLoading(false);
			disabled = false;
			outer.removeClass('disabled');
		}
	
		outer
			.bind('buttonmenudisable', disable)
			.bind('buttonmenuenable', enable)
			.bind('buttonmenuclose', function(){menu.close();})
			.bind('buttonmenusettext', settext);
		outer.click(function(e){e.preventDefault();});
		if (options.click) outer.click(onclick);
		if (options.mouseover) {
			outer.mouseover(onmouseover)
				.mouseout(onmouseout);
		}
			
		return outer;
	};
	
})();

