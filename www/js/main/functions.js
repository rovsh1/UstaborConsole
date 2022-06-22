function in_array(value, array) {
	var i = 0, l = array.length;
	for (; i < l; i++) {
		if (array[i] === value) {
			return true;
		}
	}
	return false;
}

function str_pad(input, length, pad_string, options) {
	input = input.toString();
	if (input.length < length) {
		var s = [], i, l = length - input.length;
		for (i = 0; i < l; i++) {
			s[s.length] = pad_string;
		}
		input = s.join('') + input;
	}
	return input;
}

function ucfirst(str) {
	return str[0].toUpperCase() + str.substring(1, str.length);
}

function explode(delimiter, string) {
	return string.toString().split(delimiter.toString());
}

function htmlspecialchars(text) {
	if (!text)
		return '';
	var map = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;'
	};
	return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function getWordDeclension(number, variants) {
	number = Math.abs(number);
	var i;
	switch (true) {
		case (number % 100 == 1 || (number % 100 > 20) && (number % 10 == 1)):
			i = 0;
			break;
		case (number % 100 == 2 || (number % 100 > 20) && (number % 10 == 2)):
		case (number % 100 == 3 || (number % 100 > 20) && (number % 10 == 3)):
		case (number % 100 == 4 || (number % 100 > 20) && (number % 10 == 4)):
			i = 1;
			break;
		default:
			i = 2;
	}
	if (typeof (variants) === 'string') {
		variants = variants.split(',');
	}
	return variants[i] || null;
}

function getSpellVariants(text) {
	var ru = 'йцукенгшщзхъфывапролджэячсмитьбюЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ',
			en = 'qwertyuiop[]asdfghjkl;\'zxcvbnm,.QWERTYUIOP{}ASDFGHJKL:"ZXCVBNM<>',
			c, p, i, l = text.length,
			textInv = [];
	for (i = 0; i < l; i++) {
		c = text[i];
		p = ru.indexOf(c);
		if (p === -1) {
			p = en.indexOf(c);
			if (p === -1) {
				textInv[textInv.length] = c;
			} else {
				textInv[textInv.length] = ru[p];
			}
		} else {
			textInv[textInv.length] = en[p];
		}
	}
	return [text, textInv.join('')];
}

function searchSpellVariants(text, variants) {
	var t = text.toLowerCase();
	return (t.indexOf(variants[0]) === 0 || t.indexOf(variants[1]) === 0);
}

function call_user_func(callable, arg) {
	if (isFunction(callable)) {
		return callable(arg);
	} else if (isArray(callable)) {
		var s = callable[0].split('.'), i, l = s.length,
				h = window;
		for (i = 0; i < l; i++) {
			if (!h[s[i]])
				return;
			h = h[s[i]];
		}
		if (isFunction(h))
			return h(callable[1] || arg);
	}
}

function floatval(value) {
	if (typeof value === 'string') {
		while (value.indexOf(',') !== - 1)
			value = value.replace(',', '');
		while (value.indexOf(' ') !== - 1)
			value = value.replace(' ', '');
	}
	return parseFloat(value);
}

function intval(value) {
	if (typeof value === 'string') {
		while (value.indexOf(',') !== - 1)
			value = value.replace(',', '');
		while (value.indexOf(' ') !== - 1)
			value = value.replace(' ', '');
	}
	return Math.round(value);
}

function isEmpty(value, allowEmptyString) {
	return (value === null) || (value === undefined) || (!allowEmptyString ? value === '' : false) || (Ext.isArray(value) && value.length === 0);
}

isArray = ('isArray' in Array) ? Array.isArray : function (value) {
	return toString.call(value) === '[object Array]';
};

function isDate(value) {
	return toString.call(value) === '[object Date]';
}

isObject = (toString.call(null) === '[object Object]') ?
		function (value) {
			return value !== null && value !== undefined && toString.call(value) === '[object Object]' && value.ownerDocument === undefined;
		} : function (value) {
	return toString.call(value) === '[object Object]';
};

function isSimpleObject(value) {
	return value instanceof Object && value.constructor === Object;
}

function isScalar(value) {
	var type = typeof value;

	return type === 'string' || type === 'number' || type === 'boolean';
}

isFunction =
		(typeof document !== 'undefined' && typeof document.getElementsByTagName('body') === 'function') ? function (value) {
	return !!value && toString.call(value) === '[object Function]';
} : function (value) {
	return !!value && typeof value === 'function';
};

function isNumber(value) {
	return typeof value === 'number' && isFinite(value);
}

function isNumeric(value) {
	return !isNaN(parseFloat(value)) && isFinite(value);
}

function isString(value) {
	return typeof value === 'string';
}

function isBoolean(value) {
	return typeof value === 'boolean';
}

function rand(min, max) {
	return min + Math.round((max - min) * Math.random());
}

function generateHash(length) {
	var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789',
			i, l = chars.length - 1, hash = '';
	for (i = 0; i < length; i++) {
		hash += chars.substr(rand(0, l), 1);
	}
	return hash;
}

EmptyFn = function(){};