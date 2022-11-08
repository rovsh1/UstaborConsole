app.dashboard.site.status.Backup = function (site, status) {
	app.dashboard.site.status.StatusItem.call(this, site, {
		title: 'Backups',
		cls: 'backups',
		contentApi: 'backups'
	});

	const self = this;

	this.getStatus = function () { return status; };
	this.setContent = function (r) {
		let html = '';
		html += '<h3>Список заданий</h3>';
		html += '<ul>';
		r.list.forEach(n => {
			html += '<li>' + n + '</li>';
		});
		html += '</ul>';

		html += '<h3>Лог</h3>';
		html += r.log ? '<pre>' + r.log + '</pre>' : '<i>Log empty</i>';

		this.setHtml(html);
	};
};