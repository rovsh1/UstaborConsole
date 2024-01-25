$.fn.childCombo = function(options) {

	options = $.extend({
		dataIndex: 'parent_id',
		resultIndex: 'data',
		emptyItem: 'Не выбрано',
		emptyText: 'Пусто',
		hidden: false
	}, options);
		
	var child = $(this),
		parent = $(options.parent),
		value = child.val();

	if (child.is('input[type="hidden"]')) {
		options.hidden = true;
		var c = $('<select name="' + child.attr('name') + '"></select>');
		parent.after(c);
		child.remove();
		child = c;
	}

	function onchange() {
		
		child.attr('disabled', 'disabled');
		
		if ($(this).val() === '') {
			child.html('<option value="">' + options.disabledText + '</option>');
			return;
		}
		
		var valTemp = child.val(),
			data;
		if (isFunction(options.data))
			data = options.data();
		else
			data = options.data || {};
		data[options.dataIndex] = $(this).val();
		if (options.value) {
			valTemp = options.value;
			delete options.value;
		}
		
		child.html("<option value=''>Загрузка</option>");
		$.getJSON(options.url, data, function(result){
			child.html('');
			var items = result[options.resultIndex],
				val, i, l = items.length;
			if (l === 0) {
				child.append('<option value="">' + options.emptyText + '</option>');
				return;
			}
			child.append('<option value="">' + options.emptyItem + '</option>');
			console.log(result)
			for (i = 0; i < l; i++) {
				if (valTemp == items[i].id) {
					val = items[i].id;
				}
				child.append("<option value='" + items[i].id + "'>" + items[i].name + "</option>");
			}
			if (val) {
				child.val(val);
			}
			child.attr("disabled", false);
		});
		
	}

	parent.change(onchange).change();
	
	return child;
	
};