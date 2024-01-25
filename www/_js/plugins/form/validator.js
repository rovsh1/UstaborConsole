$.fn.validator = function(options) {
	if (typeof options === 'string')
		options = {name: options};
	var form = $(this);
	form.submit(function(e){
		if (form.find('input.input-hash').length > 0)
			return;
		e.preventDefault();
		e.stopImmediatePropagation();
		form.find('button[type="submit"]')
				.attr('disabled', true)
				.addClass('loading');
		Http.getJSON('/auth/check/', function(r){
			form.append('<input type="hidden" class="input-hash" name="' + options.name + '[hash]" value="' + r.hash + '" />');
			form.find('button[type="submit"]').removeClass('loading');
			form.submit();
		});
	});
};