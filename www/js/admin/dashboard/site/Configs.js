app.dashboard.site.status.Configs = function (site, status) {
	app.dashboard.site.status.StatusItem.call(this, site, {
		title: 'Configs',
		cls: 'configs',
		contentApi: 'configs'
	});

	const self = this;

	this.getStatus = function () { return status; };
	this.setContent = function (r) {
		let html = '<table>';

		const param = function (name, value) {
			html += '<tr>';
			html += '<th>' + name + '</th>';
			html += '<td>' + value + '</td>';
			html += '</tr>';
		};

		param('dropbox', r.dropbox ? 'Yes' : 'No');
		param('OAuth', r.oauth ? 'Yes' : 'No');
		param('Languages', r.languages.length > 0 ? r.languages.join(', ') : 'Undefined');
		param('Payments', r.payments.length > 0 ? r.payments.join(', ') : 'Undefined');
		//r.list.forEach(n => {
		//	html += '<li>' + n + '</li>';
		//});
		html += '</table>';

		this.setHtml(html);
	};
};