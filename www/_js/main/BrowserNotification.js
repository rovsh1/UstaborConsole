BrowserNotification = new function () {

	var timerTitle, audio,
		$favicon,
		origTitle, origFavicon, alertFavicon,
		ready = false;

	this.init = function() {
		//return;
		if (ready) return;
		ready = true;
		$favicon = $('head link[type="icon"]');
		origTitle = $(document).attr('title');
		origFavicon = $favicon.attr('href');
		alertFavicon = origFavicon.replace('.ico', '-alert.ico');
	};
	this.reset = function () {
		timerTitle.stop();
		$(document).attr('title', origTitle);
		$favicon.attr('href', origFavicon);
	};
	this.notify = function (params) {
		if (!timerTitle) {
			timerTitle = new TimeInterval(function() {
				$(document).attr('title', (this._alt ? origTitle : '* ' + params.title + ' *'));
				this._alt = !this._alt;
			}, 1000);
		}
		timerTitle.start();
		$('#favicon').attr('href', alertFavicon);
	};
	this.sound = function (file) {
		if (!audio)
			audio = new Audio();
		audio.src = '/sounds/notification/' + file;
		audio.play();
	};
};