$.fn.multiselect = function(options) {
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