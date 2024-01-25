$.fn.textareaAutoHeight = function(options) {

	var textarea = $(this), 
		$hidden, disabled = false;
		
	options = $.extend({
		//minHeight: 26,
		maxHeight: 300
	}, options);

	function css() {
		if (!$hidden)
			return;
		$hidden.css({
			'font-size': textarea.css('font-size'),
			'line-height': textarea.css('line-height'),
			height: textarea.css('line-height'),
			width: textarea.width()
		});
		options.minHeight = $hidden.height();
	}
	function onchange() {
		setTimeout(function () {
			if (disabled) return;
			if (!$hidden) {
				$hidden = $('<textarea></textarea>')
					.css({
						visibility: 'hidden',
						position: 'absolute',
						left:-1000,
						top:-1000,
						padding: 0,
						border: 0,
						overflow: 'hidden'
					}).appendTo(document.body);
				css();
			}
			$hidden.val(textarea.val());
			var height = Math.max(options.minHeight, $hidden[0].scrollHeight);
			height = Math.min(options.maxHeight, height);
			//console.log(height, textarea.height(), $hidden[0].scrollHeight, options)
			if (height != textarea.height()) {
				textarea.height(height);
				if (options.resize)
					options.resize();
			}
		}, 0);
	}

	if (options.collapseOnBlur) {
		textarea
			.focus(function(){
				disabled = false;
				textarea.css('white-space', 'normal');
				onchange();
			})
			.blur(function(e){
				disabled = true;
				textarea.css('white-space', 'nowrap');
				textarea.height(options.minHeight);
			});
	}
	textarea.bind('keydown change cut paste', onchange)
			.bind('update-height', onchange);
	$(window).resize(function(){ css();onchange(); });
	if (textarea.val())
		textarea.change();

	return textarea;
};