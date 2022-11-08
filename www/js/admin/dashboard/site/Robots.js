app.dashboard.site.status.Robots = function (site, status) {
	app.dashboard.site.status.StatusItem.call(this, site, {
		title: 'robots.txt',
		cls: 'log-content robots',
		status: status,
		contentApi: 'robots'
	});
	this.getStatus = function () { return status; };
};