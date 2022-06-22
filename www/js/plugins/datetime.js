function DateTime(time) {
	var now = new Date(),
		utilDate = {
			clone : function(date) { return new Date(date.getTime()); },
			isValid : function(y, m, d, h, i, s, ms) {
				// setup defaults
				h = h || 0;
				i = i || 0;
				s = s || 0;
				ms = ms || 0;

				// Special handling for year < 100
				var dt = this.add(new Date(y < 100 ? 100 : y, m - 1, d, h, i, s, ms), 'y', y < 100 ? y - 100 : 0);

				return y == dt.getFullYear() &&
					m == dt.getMonth() + 1 &&
					d == dt.getDate() &&
					h == dt.getHours() &&
					i == dt.getMinutes() &&
					s == dt.getSeconds() &&
					ms == dt.getMilliseconds();
			},
			isLeapYear: function(d) {
				var year = d.getFullYear();
				return !!((year & 3) == 0 && (year % 100 || (year % 400 == 0 && year)));
			},
			getWeekOfYear: (function() {
				// adapted from http://www.merlyn.demon.co.uk/weekcalc.htm
				var ms1d = 864e5, // milliseconds in a day
					ms7d = 7 * ms1d; // milliseconds in a week

				return function(date) { // return a closure so constants get calculated only once
					var DC3 = Date.UTC(date.getFullYear(), date.getMonth(), date.getDate() + 3) / ms1d, // an Absolute Day Number
						AWN = Math.floor(DC3 / 7), // an Absolute Week Number
						Wyr = new Date(AWN * ms7d).getUTCFullYear();

					return AWN - Math.floor(Date.UTC(Wyr, 0, 7) / ms7d) + 1;
				};
			}()),
			getGMTOffset: function(date, colon) {
				var offset = date.getTimezoneOffset();
				return (offset > 0 ? "-" : "+")
					+ Ext.String.leftPad(Math.floor(Math.abs(offset) / 60), 2, "0")
					+ (colon ? ":" : "")
					+ Ext.String.leftPad(Math.abs(offset % 60), 2, "0");
			},
			getDayOfYear: function(date) {
				var num = 0,
					d = utilDate.clone(date),
					m = date.getMonth(),
					i;

				for (i = 0, d.setDate(1), d.setMonth(0); i < m; d.setMonth(++i)) {
					num += utilDate.getDaysInMonth(d);
				}
				return num + date.getDate() - 1;
			},
			getDaysInMonth: (function() {
				var daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

				return function(date) { // return a closure for efficiency
					var m = date.getMonth();

					return m == 1 && utilDate.isLeapYear(date) ? 29 : daysInMonth[m];
				};
			}()),
			add : function(date, interval, value) {
				var d = this.clone(date),
					day, decimalValue, base = 0;
				if (!interval || value === 0) {
					return d;
				}

				decimalValue = value - parseInt(value, 10);
				value = parseInt(value, 10);

				if (value) {
					switch(interval.toLowerCase()) {
						case 'ms':
							d.setTime(d.getTime() + value);
							break;
						case 's':
							d.setTime(d.getTime() + value * 1000);
							break;
						case 'mi':
							d.setTime(d.getTime() + value * 60 * 1000);
							break;
						case 'h':
							d.setTime(d.getTime() + value * 60 * 60 * 1000);
							break;
						case 'd':
							d.setDate(d.getDate() + value);
							break;
						case 'm':
							day = date.getDate();
							if (day > 28) {
								day = Math.min(day, Ext.Date.getLastDateOfMonth(Ext.Date.add(Ext.Date.getFirstDateOfMonth(date), Ext.Date.MONTH, value)).getDate());
							}
							d.setDate(day);
							d.setMonth(date.getMonth() + value);
							break;
						case 'y':
							day = date.getDate();
							if (day > 28) {
								day = Math.min(day, utilDate.getLastDateOfMonth(Ext.Date.add(Ext.Date.getFirstDateOfMonth(date), Ext.Date.YEAR, value)).getDate());
							}
							d.setDate(day);
							d.setFullYear(date.getFullYear() + value);
							break;
					}
				}

				if (decimalValue) {
					switch (interval.toLowerCase()) {
						case 'ms':    base = 1;               break;
						case 's':   base = 1000;            break;
						case 'mi':   base = 1000*60;         break;
						case 'h':     base = 1000*60*60;      break;
						case 'd':      base = 1000*60*60*24;   break;

						case 'm':
							day = utilDate.getDaysInMonth(d);
							base = 1000*60*60*24*day;
							break;

						case 'y':
							day = (utilDate.isLeapYear(d) ? 366 : 365);
							base = 1000*60*60*24*day;
							break;
					}
					if (base) {
						d.setTime(d.getTime() + base * decimalValue);
					}
				}
				return d;
			},
			now: Date.now || function() { return +new Date(); },
			toString: function(date) {
				var pad = Ext.String.leftPad;
				return date.getFullYear() + "-"
					+ pad(date.getMonth() + 1, 2, '0') + "-"
					+ pad(date.getDate(), 2, '0') + "T"
					+ pad(date.getHours(), 2, '0') + ":"
					+ pad(date.getMinutes(), 2, '0') + ":"
					+ pad(date.getSeconds(), 2, '0');
			}
		},
		formats = {
			a: function () {},
			B: function () {
				var n = new DateTime(), c = [
					(n.getFullYear() === this.getFullYear()),
					(n.getMonth() === this.getMonth()),
					(n.getDate() === this.getDate())
				];
				if (c[0] && c[1] && c[2]) {
					return 'сегодня';
				} else if (c[0] && c[1]) {
					now.setDate(now.getDate() - 1);
					if (now.getDate() === this.getDate()) {
						return 'вчера';
					}					
					now.setDate(now.getDate() + 2);
					if (now.getDate() === this.getDate()) {
						return 'завтра';
					}
					return this.format('d.m');
				} else if (c[0]) {
					return this.format('d.m');
				}
				return this.format(d, 'd.m.Y');
			},
			P: function () {
				var now = new Date(),
						diff = now.getTime() - this.getTime(),
						i, s = diff / 1000,
						parts = {
							years: [31536000, 'год,года,лет'],
							months: [2592000, 'месяц,месяца,месяцев'],
							weeks: [604800, 'день,дня,дней'],
							days: [86400, 'день,дня,дней'],
							hours: [3600, 'час,часа,часов'],
							minutes: [60, 'минуту,минуты,минут'],
							seconds: [0, 'секунду,секунды,секунд']
						};
				if (s < 1) {
					s = 1;
				}
				for (i in parts) {
					if (s >= parts[i][0]) {
						return Math.round(s / parts[i][0]) + ' ' + parts[i][1] + ' назад';
					}
				}
				return '';
			},
			A: function () {},
			d: function () { return fp(this.getDay()); },
			D: function () { return DAY_NAMES_MIN[this.getWeekDay()]; },
			F: function () { return MONTH_NAMES[this.getMonth() - 1]; },
			g: function () { return (this.getHours() > 12 ? this.getHours - 12 : this.getHours()); },
			G: function () { return this.getHours(); },
			h: function () { return fp(this.getHours() > 12 ? this.getHours - 12 : this.getHours()); },
			H: function () { return fp(this.getHours()); },
			i: function () { return fp(this.getMinutes()); },
			I: function () { return false; },
			j: function () { return this.getDay(); },
			l: function () { return DAY_NAMES[this.getWeekDay()]; },
			L: utilDate.isLeapYear,
			m: function () { return fp(this.getMonth()); },
			M: function () { return substr(MONTH_NAMES[this.getMonth() - 1], 0, 3); },
			n: function () { return this.getMonth(); },
			O: function () { return utilDate.getGMTOffset(); },
			s: function () { return fp(this.getSeconds()); },
			t: utilDate.getDaysInMonth,
			U: function () { return (this.getTime() / 1000); },
			w: function () { return getDay(this.getDay()); },
			W: utilDate.getWeekOfYear,
			Y: function () { return now.getFullYear(); },
			y: function () { return now.getFullYear().substr(2); },
			z: function () {}
		};

	switch (time) {
		case null:
		case undefined:
		case 'now':
			break;
		default:
			var p = time.split(' '),
					sd = p[0].split('-');
			now.setDate(1);
			now.setFullYear(sd[0]);
			now.setMonth(parseInt(sd[1]) - 1);
			now.setDate(sd[2]);
			if (p[1]) {
				sd = p[1].split(':');
				now.setHours(sd[0]);
				now.setMinutes(sd[1]);
				now.setSeconds(sd[2]);
			}
	}
	
	function fp(i) {
		return (i > 9 ? i.toString() : '0' + i.toString());
	}

	this.format = function (format) {
		if (!format) {
			format = 'Y-m-d H:i:s';
		}
		var i, ch = '', special = false, code =[];
		for (i = 0; i < format.length; ++i) {
			ch = format.charAt(i);
			if (!special && ch == "\\") {
				special = true;
			} else if (special) {
				special = false;
				code.push(ch);
			} else if (formats[ch]) {
				code.push(formats[ch].call(this));
			} else {
				code.push(ch);
			}
		}
		return code.join('');
	};
	this.getTime = function () {
		return now.getTime();
	};
	this.getTimestamp = function () {
		return now.getTime() * 1000;
	};
	this.getYear = function () {
		return now.getFullYear();
	};
	this.getMonth = function () {
		return now.getMonth() + 1;
	};
	this.getDay = function () {
		return now.getDate();
	};
	this.getWeekDay = function () {
		return now.getDay() === 0 ? 7 : now.getDay();
	};
	this.getHours = function () {
		return now.getHours();
	};
	this.getMinutes = function () {
		return now.getMinutes();
	};
	this.getSeconds = function () {
		return now.getSeconds();
	};
	this.setYear = function(year) {
		now.setFullYear(year);
		return this;
	};
	this.setMonth = function(month) {
		now.setMonth(month - 1);
		return this;
	};
	this.setDay = function(day) {
		now.setDate(day);
		return this;
	};
	this.setHours = function(hours) {
		now.setHours(hours);
		return this;
	};
	this.setMinutes = function(minutes) {
		now.setMinutes(minutes);
		return this;
	};
	this.setSeconds = function(seconds) {
		now.setSeconds(seconds);
		return this;
	};
	this.diff = function(datetime) {
		var days = 0, d = datetime.clone(), i = 100;
		while (!d.isEqual(this) && i > 0) {
			d.setDate(d.date + 1);
			days++;
			i--;
		}
		return days;
	};
	this.modify = function(format) {
		var sign = format.charAt(0),
			format = format.substr(1).split(' ');
		format[0] = parseInt(format[0]);
		switch (sign) {
			case '-':format[0] = -format[0];
		}
		switch (format[1]) {
			case 'day':
			case 'days':
				now.setDate(now.getDate() + format[0]);
				break;
			case 'month':
			case 'months':
				now.setMonth(now.getMonth() + format[0]);
				break;
			case 'year':
			case 'years':
				now.setYear(now.getYear() + format[0]);
				break;
		}
		return this;
	};

}