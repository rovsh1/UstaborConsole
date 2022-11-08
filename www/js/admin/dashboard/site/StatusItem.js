app.dashboard.site.status.StatusItem = function (site, params) {
	const self = this;
	let contentLoaded;
	let el, innerEl;

	this.getEl = function () {
		this.getEl = function () { return el; };

		el = $('<div class="content-inner ' + (params.cls || '') + '">' +
			//'<div class="title">' + params.title + '</div>' +
			//'<div class="inner"></div>' +
			'</div>');

		innerEl = el;//.find('div.inner');
		contentLoaded = params.contentApi ? false : null;
		/*el.find('div.title').click(function () {
			if (false === contentLoaded) {
				self.load();
				contentLoaded = true;
			}
			el.toggleClass('expanded');
		});*/

		this.init(el, innerEl);

		return el;
	};
	this.init = function () {};
	this.getStatus = function () { return 'OK'; };
	this.isOK = function () { return this.getStatus() === 'OK'; };
	//this.getError = function () { return this.isOK() ? null : params.status; };
	this.setHtml = function (html) { innerEl.html(html); };
	this.setEmpty = function (text) {
		innerEl.html('<i>' + (text || 'Empty') + '</i>');
	};
	this.load = function () {
		el.addClass('loading');
		Http.getJSON('/dashboard/' + params.contentApi + '/' + site.id + '/', function (r) {
			self.setContent(r);
			el.removeClass('loading');
		});
	};
	this.setContent = function (r) {
		if (!r.content)
			this.setEmpty();
		else
			this.setHtml('<pre>' + r.content + '</pre>');
	};
	this.show = function () {
		this.getEl().show();

		if (false === contentLoaded) {
			contentLoaded = true;
			self.load();
		}
	};
	this.hide = function () {
		if (el)
			el.hide();
	};

	constructors.triggers.call(this);
};