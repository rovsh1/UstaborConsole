app.dashboard.site.ui.StatusTabs = function (item) {
	const self = this;
	const el = $('<div class="status-tabs"></div>');
	let tabs = [];
	let current;

	function Tab(item, title) {
		const el = $('<div class="tab ' + (item.isOK() ? 'status-ok' : 'status-error') + '">' +
			'<div class="status" title="' + item.getStatus() + '"></div>' +
			'<span class="title">' + title + '</span>' +
			//'<div class="inner"></div>' +
			'</div>');

		el.click(function () { self.setCurrent(item.key); });

		item.bind('update', function () {
			let cls = 'tab ' + (item.isOK() ? 'status-ok' : 'status-error');
			if (el.hasClass('current'))
				cls += ' current';
			el.attr('class', cls);
		});

		this.key = item.key;
		this.getEl = function () { return el; };
		this.getItem = function () { return item; };
		this.setActive = function (flag) {
			el[flag ? 'addClass' : 'removeClass']('current');
		};
	}

	this.getEl = function () { return el; };
	this.addTab = function (item, title) {
		const tab = new Tab(item, title);

		el.append(tab.getEl());
		tabs.push(tab);
	};
	this.getTab = function (key) {
		for (let i = 0; i < tabs.length; i++) {
			if (tabs[i].key === key)
				return tabs[i];
		}
		return null;
	};
	this.setCurrent = function (key) {
		if (current === key)
			return;
		else if (current) {
			const currentTab = this.getTab(current);
			currentTab.setActive(false);
			currentTab.getItem().hide();
		}

		current = key;
		const tab = this.getTab(key);
		tab.setActive(true);
		tab.getItem().show();
	};

	constructors.triggers.call(this);
};