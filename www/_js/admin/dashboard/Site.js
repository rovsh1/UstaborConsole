app.dashboard.Site = function (data) {
	const self = this;
	let el, innerEl;
	let statusItems = [];
	let statusTabs = new app.dashboard.site.ui.StatusTabs(self);

	this.id = +data.id;

	this.getEl = function () {
		this.getEl = function () { return el; };

		el = $('<div class="card dashboard-site">'
			+ '<div class="card-title"><h2>' + data.name + ' <a href="https://www.' + data.domain + '" target="_blank" class="btn-preview"></a></h2></div>'

			+ '<div class="card-body"></div>'
			+ '</div>');

		innerEl = el.find('div.card-body');

		innerEl.append(statusTabs.getEl());

		//if (this.id == 6)
		this.updateStatus();

		return el;
	};
	this.updateStatus = function () {
		el.addClass('loading');

		Http.getJSON('/dashboard/status/' + this.id + '/', function (r) {
			const contentEl = $('<div class="status-content"></div>').appendTo(innerEl);

			const add = function (key, param, title) {
				const item = new app.dashboard.site.status[key](self, param);
				item.key = key;
				//const tab = $('<div class="tab"></div>').appendTo(tabsEl);
				//tabsEl.append(status[i].getTabEl());
				statusTabs.addTab(item, title);
				contentEl.append(item.getEl());
				statusItems.push(item);// = new app.dashboard.site.status.HttpStatus(self, r.monitor_log);
			}

			add('HttpStatus', r.monitor_log, 'Http status');
			add('Configs', r.configs, 'Configs');
			add('ErrorLog', r.error_log, 'ErrorLog');
			add('Robots', r.robots, 'Robots');
			add('Backup', r.backup, 'Backup');
			add('Cron', r.cron, 'Cron');

			statusTabs.setCurrent(statusItems[0].key);

			el.removeClass('loading');
		});
	};
};
app.dashboard.site = {status: {}, ui: {}};