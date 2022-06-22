function ReportRequest(form, response, params) {

	const formEl = form.getForm();
	const formData = new FormData(formEl[0]);
	var offset = 0;
	var action = 'start';
	const step = params.step || 1000;
	
	formData.set('report[step]', step);

	const send = function() {
		formData.set('report[action]', action);
		formData.set('report[offset]', offset);
		$.ajax({
			url: formEl.attr('action') || location.toString(),
			method: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			dataType: 'json',
			success: function(r) {
				if (!r) {
					response.message('report empty');
					form.setDisabled(false);
					return;
				} else if (r.exception) {
					form.setDisabled(false);
					response.error(r.exception);
					return;
				}
				switch (action) {
					case 'start':
						if (r.rows == 0) {
							response.error('result empty');
							form.setDisabled(false);
						} else {
							action = 'next';
							offset += step;
							formData.set('report[tmpname]', r.tmpname);
							response.message('report file created (rows=' + r.rows + ';tmpname=' + r.tmpname + ')', 'waiting');
							send();
						}
						break;
					case 'next':
						if (r.rows <= 0) {
							action = 'finish';
							response.message('report file prepare', 'waiting');
						} else {
							offset += step;
							response.message('next iteration (rows=' + r.rows + ')', 'waiting');
						}
						send();
						break;
					case 'finish':
						response.message('report finished');
						response.setResult(r);
						form.setDisabled(false);
						break;
				}
			},
			error: function(){
				form.setDisabled(false);
				response.error('request error');
			}
		});
	};
	
	this.send = function() {
		send();
		form.toggle('response');
		response.message('report started (step=' + step + ')', 'waiting');
	};
}
