function ReportResponse(form, $el) {

	var T = this;
	var $current;
	var waitTimer;
	var $toolbar, $inner, iframe, $log;
	var result;

	var log = function (text, type) {
		if (waitTimer) {
			clearInterval(waitTimer);
			waitTimer = null;
			$current.html($current._text);
		}
		$current = $('<div class="item ' + (type || 'info') + '">' + text + '</div>').appendTo($log);
		if (type === 'waiting') {
			$current._text = text;
			var i = 3;
			waitTimer = setInterval(function () {
				$current.html(text + str_pad('', 3 - i, '.'));
				i--;
				if (i < 0)
					i = 3;
			}, 400);
		}
	};

	this.init = function () {
		if ($toolbar) {
			$log.html('');
			$inner.html('');
			$toolbar.hide();
			return;
		}
		$el.addClass('report-response');// = $('<div class="report-response" id="report-response"></div>');
		$log = $('<div class="response-log"></div>').appendTo($el);
		$inner = $('<div class="response-content"></div>').appendTo($el);
		$toolbar = $('<div class="response-toolbar" style="display:none">'
				//+ '<button type="button" class="btn-cancel btn-back">Back</button>'
				+ '<button type="button" class="btn-submit btn-save">Download file</button>'
				+ '<div class="mail">'
				+ '<input type="email" placeholder="Send to email" />'
				+ '<button type="button" class="btn-submit">Send</button>'
				+ '</div>'
				+ ''
				+ ''
				+ ''
				+ '</div>').appendTo($el);
		//$toolbar.find('button.btn-back').click(function () { form.toggle('form'); });
		$toolbar.find('button.btn-save').click(function () {
			//var btn = $(this).addClass('loading');
			if (!iframe) {
				iframe = document.createElement('iframe');
				iframe.style = 'display:none;';
				iframe.onload = function () {
					//btn.removeClass('loading');
					//$(iframe).remove();
				};
				document.body.appendChild(iframe);
			}
			iframe.src = '/report/file/?' + $.param(result);
		});

		(function (el) {
			var input = el.find('input'),
					btn = el.find('button');
			btn.click(function () {
				if (input.val() === '') {
					input.focus();
					return;
				}
				btn.addClass('loading');
				var q = $.extend({email: input.val()}, result);
				$.get('/report/mail/?' + $.param(q), function () {
					btn.removeClass('loading');
				});
			});
		})($toolbar.find('div.mail'));

		//this.message(text, 'start');
	};
	this.error = function (text) { return this.message(text, 'error'); };
	this.message = function (text, type) {
		log(text, type);
	};
	this.setResult = function (_result) {
		result = _result;
		//form.toggle('response');
		//$toolbar.hide();
		$toolbar.show();
		//console.log(result);
		if (result.status) {
			if (result.status.code === 'error') {
				this.error(result.status.message);
				return;
			}
		}
		$el.addClass('loading');
		Http.get('/report/preview/', result, function (html) {
			$toolbar.show();
			$inner.html(html);
			$el.removeClass('loading');
		});
	};

}