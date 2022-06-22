MessageBox = function(options) {
	options = $.extend({
		modal: true,
		autoclose: false
	}, options);
	options.cls = 'messagebox' + (options.cls ? ' ' + options.cls : '');
	return new Window(options);
};
WindowConfirm = function(options, callback){
	if (typeof options === 'string')
		options = {title: 'Подтверждение', html: options};
	options = $.extend({
		modal: true,
		autoclose: false
	}, options);
	options.cls = 'messagebox' + (options.cls ? ' ' + options.cls : '');
	options.buttons = [{
		text: 'Confirm',
		cls: 'btn-cancel',
		handler: callback
	}, {
		text: 'Cancel',
		cls: 'btn-submit',
		handler: function(){ this.close(); }
	}];
	return new Window(options);
};

function user_avatar(image) {
	var src = image ? '/file/' + image + '/' : '/images/icons/user.svg';
	return '<img src="' + src + '" />';
}

function getUserProfile(userId) {
	WindowManager.open({url: '/profile/carduser/' + userId + '/', modal: true});
}

function getHelperProfile(userId) {
	WindowManager.open({url: '/profile/cardhelper/' + userId + '/', modal: true});
}