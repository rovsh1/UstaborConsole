app.dashboard.site.status.HttpStatus = function (site, log) {
	app.dashboard.site.status.StatusItem.call(this, site, {
		title: 'Http status',
		cls: 'monitor-status'
	});

	let okFlag = true;

	this.init = function (el, innerEl) {
		if (log.length === 0)
			return this.setEmpty();

		let html = '';
		for (let i = 0; i < log.length; i++) {
			const d = log[i];
			html += '<div class="">';
			html += '<span class="status ' + d.status + '">' + d.status.toUpperCase() + '</span>&nbsp;&nbsp;';
			html += '<span class="date">' + d.updated + '</span>&nbsp;&nbsp;';
			html += '<a href="' + d.url + '" target="_blank" class="url">' + d.url + '</a>';
			html += '</div>';
			if (okFlag && d.status !== 'ok')
				okFlag = false;
		}

		innerEl.html(html);

		if (!okFlag)
			el.addClass('expanded');
	};
	this.getStatus = function () { return okFlag ? 'OK' : 'ERROR'; };
};