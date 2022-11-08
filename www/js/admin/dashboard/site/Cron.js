app.dashboard.site.status.Cron = function (site, status) {
	app.dashboard.site.status.StatusItem.call(this, site, {
		title: 'Cron',
		cls: 'cron',
		contentApi: 'cron'
	});

	const self = this;

	this.getStatus = function () { return status; };
	this.isOK = function () { return /\d+/.test(status); };
	this.setContent = function (r) {
		let html = '';
		html += '<h3>Список заданий</h3>';
		html += '<ul>';
		r.tasks.forEach(n => {
			html += '<li>' + n.description + ' (' + n.command + ')' + '</li>';
		});
		html += '</ul>';

		html += '<h3>Лог</h3>';
		html += r.log ? '<pre>' + r.log + '</pre>' : '<i>Log empty</i>';

		this.setHtml(html);
	};
};