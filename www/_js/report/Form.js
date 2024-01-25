function ReportForm(params) {

	const T = this;
	let request;
	const formEl = $(params.formEl),
		responseEl = $(params.responseEl),
		tabsEl = $(params.tabsEl);
	const response = new ReportResponse(this, responseEl);
	const portalEl = $('#form_report_portal_id'),
		siteSelect = new ReportFormSelect($('#form_report_site_id')),
		categoriesSelect = new ReportFormSelect($('#form_report_category_id')),
		countriesSelect = new ReportFormSelect($('#form_report_country_id')),
		//mastersSelect = new ReportFormMasters($('#form_report_master_id')),
		btn = formEl.find('button[type="submit"]');

	categoriesSelect.setParent(siteSelect, 'site_id');
	/*countriesSelect.setHandler(function(item){
		var siteId = siteSelect.getValue();
		if (!siteId)
			return;
		//return (item.site_id === siteId);
	});*/
	siteSelect.change(function () {
		categoriesSelect.update();
		countriesSelect.update();
	});

	const update = function (api) {
		siteSelect.setItems(api.getSites());
		countriesSelect.setItems(api.getCountries());
		categoriesSelect.setItems(api.getCategories());
		formEl.removeClass('loading');
	};

	formEl.submit(function (e) {
		e.preventDefault();
		if (portalEl.val() === '') {
			portalEl.parent().addClass('field-invalid');
			return;
		}
		T.setDisabled(true);
		response.init();
		//response.message('export started', 'waiting');
		request = new ReportRequest(T, response, params);
		request.send();
	});
	portalEl.change(function () {
		formEl.addClass('loading');
		$(this).parent().removeClass('field-invalid');
		var id = $(this).val();
		if (id)
			Portal.get(id, update);
	});

	this.toggle = function (tab) {
		formEl.tabs('select', 'tab-' + tab);
	};
	this.getForm = function () { return formEl; };
	this.setDisabled = function (flag) {
		btn.attr('disabled', flag);
		if (flag) {
			formEl.addClass('loading');
		} else {
			formEl.removeClass('loading');
		}
	};

	return;
	response.init();
	response.setResult({
		filename: "report_master-contacts-balance.csv",
		tempnam: "csvuYSZrB"
	});

}