app.dashboard.site.status.ErrorLog = function (site, status) {
	app.dashboard.site.status.StatusItem.call(this, site, {
		title: 'Errors',
		contentApi: 'errorlog'
	});

	const self = this;

	this.getStatus = function () { return status; };
	this.setContent = function (r) {
		if (!r.content)
			this.setEmpty();
		else
			this.setHtml('<pre>' + r.content + '</pre>');

		if (this.isOK())
			return;

		const el = this.getEl();
		const btnsWrap = $('<div class="content-buttons"></div>').appendTo(el);
		$('<div class="btn">Очистить файл</div>')
			.click(function (e) {
				e.stopPropagation();
				$(this).remove();
				el.addClass('loading');
				Http.get('/dashboard/errorclear/' + site.id + '/', function () {
					el.removeClass('loading');
					status = 'OK';
					self.setEmpty();
					self.trigger('update');
				});
			}).appendTo(btnsWrap);

	};
};