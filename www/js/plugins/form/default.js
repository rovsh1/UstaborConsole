Form = {
	element: {}	
};
$.fn.elementNumber = function(options) {
	options = $.extend({
		digits: 8,
		fractionDigits: 0,
		nonnegative: true
	}, options);
	if (options.fractionDigits) {
		var i, s = $(this).val();
		if (s) s = s.split('.'); else s = [];
		if (!s[0]) s[0] = '0';
		if (!s[1]) s[1] = '';
		for (i = s[1].length;i < options.fractionDigits;i++) {
			s[1] += '0';
		}
		$(this).val(s.join(','));
		return $(this).mask("#,##0.00", {reverse: true});
	} else {
		return $(this).mask("#,##0", {reverse: true});
	}
};
$.fn.maskPhone = function(options){
	var v = '+' + options.code + ' ',
		c = v + '(000) 000-00-00';
	return $(this)
		.unmask()
		.unbind('focus blur')
		.focus(function(){ if ('' === this.value) this.value=v; })
		.blur(function(){ if(this.value===v)this.value=''; })
		.mask(c, {placeholder: v+""});
};
$.fn.elementPhone = $.fn.elementTel = function(){
	var input = $(this),
		wr = input.parent();

	wr.addClass('phone-mask');
	input.maskPhone({
		code: '7'
	});
};
$.fn.elementDate = function(options) {
	$(this)
			.focus(function () {
				//if ('' === this.value)
				//	this.value = '+7 (';
			})
			.blur(function () {
				//if (this.value === '+7 (')
				//	this.value = '';
			})
			.mask('00.00.0000', {
				onKeyPress: function (val, e, field, options) {
					//console.log(arguments);
					//field.mask(SPMaskBehavior.apply({}, arguments), options);
				}
			});
	if ($.fn.datepicker) {
		$(this).datepicker(options);
	}
	return $(this);
};
Form.initElement = function(id, plugin, options) {
	var fn = 'element' + ucfirst(plugin);
	if ($.fn[fn]) {
		return $(id.indexOf('#') === 0 ? id : '#' + id)[fn](options);
	}
};
$.fn.elementMultiselect = function(options) {
	options = $.extend({
		columns: 5
	}, options);

	function group(wrapper, group, id, val) {
		var items = group.find('option'), count = items.length,
			l = val ? val.length : 0,
			maxRowsDl, minRows,
			columns = options.columns,
			col, s = '', v, i = 1, n = 0, j;
		items.each(function(){
			if ($(this).val() !== '') return;
			count--;
		});
		maxRowsDl = count % columns;
		minRows = Math.floor(count / columns) + (maxRowsDl ? 1 : 0);
		items.each(function(){
			if ($(this).val() === '') return;
			v = $(this).val();
			s += '<div class="item">'
				+ '<input type="checkbox" id="' + id + '_c' + n + '" value="' + v + '"';
			for (j = 0; j < l;j++) {
				if (val[j] == v) {
					s += ' checked="checked"';
					break;
				}
			}
			s += ' />'
				+ '<label for="' + id + '_c' + n + '">' + $(this).text() + '</label>'
				+ '</div>';
			if (i > 0 && i === minRows) {
				col = $('<div class="column">' + s + '</div>').appendTo(wrapper);
				maxRowsDl--;
				if (maxRowsDl === 0) {
					minRows--;
				}
				i = 0;
				s = '';
			}
			i++;
			n++;
		});
	}
	$(this).each(function(){
		var el = $(this).hide(),
			d = $('<div class="multiselect"></div>').appendTo(el.parent()),
			id = el.attr('id'), cbs = [],
			val = el.val(),
			groups = el.find('optgroup');
		if (groups.length === 0) {
			group(d, el, id, val);
		} else {
			var gi = 0;
			groups.each(function(){
				d.append('<div class="title">' + $(this).attr('label') + '</div>');
				group(d, $(this), id + '_' + gi++, val);
			});
		}
		cbs = d.find('input').change(function(){
			var val = [];
			cbs.each(function(){
				if ($(this).is(':checked')) {
					val.push($(this).val());
				}
			});
			el.val(val);
		});
	});
};