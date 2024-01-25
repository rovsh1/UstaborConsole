$.widget('custom.groupcomplete', $.ui.autocomplete, {
	_create: function () {
		this._super();
		this.widget().menu('option', 'items', '> li:not(.ui-menu-group)');
	},
	_renderMenu(ul, items) {
		var self = this, currentGroup;
		$.each( items, function( index, item ) {
			if (item.group !== currentGroup) {
				currentGroup = item.group;
				ul.append('<li class="ui-menu-group">' + currentGroup + '</li>');
			}
			self._renderItemData(ul, item);
		});
	}
});