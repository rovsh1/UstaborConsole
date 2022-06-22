(function(){
	
	function dateToString(date, format) {
		if (!format) format = 'd.m.Y';
		var s = [],
			i = date.getDate();
		if (i < 10) i = ['0', i].join('');
		format = format.replace('d', i);
		i = date.getMonth() + 1;
		if (i < 10) i = ['0', i].join('');
		format = format.replace('m', i);
		format = format.replace('Y', date.getFullYear());
		return format;
	}

	function dateFromString(str) {
		var parts = str.split('.');
		if (!(
			parts.length == 3
			&& /^\d{2}$/.test(parts[0])
			&& /^\d{2}$/.test(parts[1])
			&& /^\d{4}$/.test(parts[2])
		)) {
			return false;
		}
		var d = new Date(parts[2], Number(parts[1]) - 1, parts[0]);
		return d;
	}
			
	var fn = {
		all: ['', ''],
		today: function(){
			return [new Date(), new Date()];
		},
		yesterday: function() {
			var from = new Date(),
				to = new Date();
			from.setDate(from.getDate() - 1);
			to.setDate(from.getDate());
			return [from, to];
		},
		last7days: function() {
			var from = new Date(),
				to = new Date();
			from.setDate(from.getDate() - 7);
			return [from, to];
		},
		last14days: function() {
			var from = new Date(),
				to = new Date();
			from.setDate(from.getDate() - 14);
			return [from, to];
		},
		last30days: function() {
			var from = new Date(),
				to = new Date();
			from.setDate(from.getDate() - 30);
			return [from, to];
		},
		thisyear: function() {
			var from = new Date(),
				to = new Date();
			from.setMonth(0);
			from.setDate(1);
			to.setMonth(11);
			to.setDate(31);
			return [from, to];
		},
		prevmonth: function() {
			var from = new Date(),
				to = new Date();
			from.setMonth(from.getMonth() - 1);
			from.setDate(1);
			to.setDate(0);
			return [from, to];
		},
		thismonth: function() {
			var from = new Date(),
				to = new Date();
			from.setDate(1);
			to.setMonth(to.getMonth() + 1);
			to.setDate(0);
			return [from, to];
		},
		thisweek: function() {
			var from = new Date(),
				to = new Date(),
				date = from.getDate(),
				d = from.getDay();
			if (d === 0) {
				d = 7;
			}
			if (d !== 1) {
				from.setDate(date - d + 1);
			}
			if (d !== 7) {
				to.setDate(date + 7 - d);
			}
			return [from, to];
		}
	}, now = new Date();;
	
	$.fn.buttonPeriod = function(options){
	
		var el = $(this), dialog;
		el.addClass('plugin-button-period');
		var input = el.parent().find('input.field-date'),
			inputFrom = $(input[0]),
			inputTo = $(input[1]),
			label = el.find('>label'),
			textEmpty = label.text(),
			names = {
				all: 'За все время',
				today: 'Сегодня',
				yesterday: 'Вчера',
				last7days: 'Последние 7 дней',
				last14days: 'Последние 14 дней',
				last30days: 'Последние 30 дней',
				prevmonth: 'Прошлый месяц',
				thisweek: 'Текущая неделя',
				thismonth: 'Текущий месяц',
				thisyear: 'Текущий год'
			};
		options = $.extend({
			prevNext: false
		}, options);
		var onselect = function(event) {
			select($(this).data('value'));
			hide();
			event.stopPropagation();
			inputTo.change();
		};
		var createDialog = function() {
			dialog = $('<div class="date-period-dialog"></div>').appendTo(document.body);
			var ul = $('<ul></ul>').appendTo(dialog),
				i;
			for (i in names) {
				$('<li class="value-' + i + '" data-value="' + i + '"><i></i>' + names[i] + '</li>').appendTo(ul);
			}
			ul.find('li').click(onselect);
		};
		var initSelection = function() {
			if (dialog) dialog.find('li.selected').removeClass('selected');
			var value;
			if (!inputFrom.val() || !inputTo.val()) {
				value = 'all';
			} else if (!inputFrom.val() || !inputTo.val()) {
				return;
			} else {
				var d, i, v = [dateFromString(inputFrom.val()), dateFromString(inputTo.val())];
				for (i in fn) {
					if (i === 'all') {
						continue;
					}
					d = fn[i]();
					if (dateToString(d[0]) === dateToString(v[0]) && dateToString(d[1]) === dateToString(v[1])) {
						value = i;
						break;
					}
				}
			}
			if (value) {
				if (dialog) dialog.find('li.value-' + value).addClass('selected');
				label.html(names[value]);
			} else {
				label.html(textEmpty);
			}
		};
		var select = function(value) {
			var values = fn[value];
			if (typeof values === 'function') {
				var dates = values();
				values = [dateToString(dates[0]), dateToString(dates[1])];
			}
			inputFrom.val(values[0]);
			inputTo.val(values[1]);
			initSelection();
		};
		var show = function(event) {
			if (!dialog)
				createDialog();
			if (!dialog.is(':hidden')) {
				hide();
			} else {
				initSelection();
				var offset = el.offset();
				dialog.css({
					top: offset.top + el.outerHeight() + 10,
					left: offset.left
				});
				dialog.show();
				$(document).click().bind('click', ondocumentclick);
			}
			event.stopPropagation();
			event.preventDefault();
		};
		var ondocumentclick = function(e) { hide(); }
		var hide = function() {
			dialog.hide();
			$(document).unbind('click', ondocumentclick);
		};
		var refreshLimit = function() {
			if (options.maxDate) {
				inputFrom.datepicker('option', 'maxDate', options.maxDate);
				inputTo.datepicker('option', 'maxDate', options.maxDate);
			}
			if (inputFrom.val()) inputTo.datepicker('option', 'minDate', inputFrom.val());
			if (inputTo.val()) inputFrom.datepicker('option', 'maxDate', inputTo.val());
		};
		
		if (options.prevNext) {
			var prev, next;
			
			function dateInc(inpt, type, inc) {
				var d = dateFromString(inpt.val());
				if (!d) {
					return false;
				}
				switch (type) {
					case 1:
						if (inpt === inputTo) {
							if (inc == 1) {
								d.setMonth(d.getMonth() + 2, 0);
							} else {
								d.setDate(0);
							}
						} else {
							d.setMonth(d.getMonth() + inc);
						}
						break;
					default:
						d.setDate(d.getDate() + inc * (type == 2 ? 7 : 1));
				}
				inpt.val(dateToString(d)).change();
				return inpt;
			}
			function getRangeType() {
				var d1 = dateFromString(inputFrom.val()),
					d2 = dateFromString(inputTo.val());
				if (d1 && d2) {
					var d22 = dateFromString(inputTo.val()),
						d2d = d2.getDay();
					d22.setDate(d22.getDate() + 1);
					if (d1 && d2) {
						switch (true) {
							case d1.getDate() == 1 && (d2.getMonth() !== d22.getMonth()):
								return 1;
							case d1.getDay() == 1 && (d2d == 5 || d2d == 0):
								return 2;
						}
					}
				}
				
				return false;
			}
			function go(inc) {
				var a = $(this);
				if (a.hasClass('disabled')) {
					return false;
				}
				var type = getRangeType();
				dateInc(inputTo, type, inc);
				dateInc(inputFrom, type, inc);
				refreshLimit();
				return false;
			}

			prev = $('<div class="go prev" title="Предыдущий"></div>')
					.click(function(){ return go.call(this, -1); });
			inputFrom.before(prev);
			next = $('<div class="go next" title="Следующий"></div>')
					.click(function(){ return go.call(this, 1); });
			inputTo.after(next);

		}
		
		label.click(show);
		refreshLimit();
		initSelection();
		if (options.value)
			select(options.value);
	};
	$.fn.buttonPeriodNav = function(options){
		var din;
		if (typeof options === 'string') din = options;
		else din = options.dateInputName;
		var n = [$('#' + din + '_from'), $('#' + din + '_to')];
		$(this).html('<button type="submit" class="icon prev" title="Предыдущие" data-value="prev"></button><button type="submit" class="icon current" title="Текущие сутки" data-value="now"></button><button type="submit" class="icon next" title="Следующие" data-value="next"></button>');
		$(this).find('button').click(function(){
			switch ($(this).data('value')) {
				case 'now':
					var s = dateToString(now);
					n[0].val(s);
					n[1].val(s);
					break;
				case 'prev':
					var d = dateFromString(n[0].val());
					if (!d) return false;
					d.setDate(d.getDate() - 1);
					var s = dateToString(d);
					n[0].val(s);
					n[1].val(s);
					break;
				case 'next':
					var d = dateFromString(n[1].val()),
						ds = d.getFullYear() + now.getMonth() + now.getDate();
					if (!d || dateToString(d, 'Ymd') >= dateToString(now, 'Ymd')) return false;
					d.setDate(d.getDate() + 1);
					var s = dateToString(d);
					n[0].val(s);
					n[1].val(s);
					break;
			}
		});
	};
	
})();