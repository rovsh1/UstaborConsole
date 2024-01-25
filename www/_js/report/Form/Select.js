function ReportFormSelect($el) {
	
	var itemHandler = EmptyFn;
	var items;
	
	this.getValue = function() { return $el.val(); };
	this.setDisabled = function(flag) { $el.attr('disabled', flag); };
	this.setParent = function(select, index) {
		this.setHandler(function(item){
			var id = select.getValue();
			return id !== '' && item[index] === +id;
		});
	};
	this.setItems = function(array) { items = array;this.update(); };
	this.setHandler = function(handler) { itemHandler = handler; };
	this.update = function() {
		var html = '';
		var l = items.length, i, html = '';
		for (i = 0; i < l; i++) {
			if (false === itemHandler(items[i]))
				continue;
			html += '<option value="' + items[i].id + '">' + items[i].name + '</option>';
		}
		this.setDisabled(html === '');
		$el.html('<option></option>' + html);
	};
	this.change = function(callback) { $el.change(callback); };
	
	this.setDisabled(true);
	
}
function ReportFormCategories($el) {
	
}