const app = {
	dashboard: {}
};

Application = new function () {

	this.init = function () {
		$.cookie.json = true;
		WindowManager.init();
		WindowManager
			.add('form', {modal: true, buttons: [{text: 'Save', cls: 'btn-submit', handler: function(){ this.getEl().find('form').submit(); }}, 'cancel']});
		$(window).scroll(function(){ $(document.body)[$(this).scrollTop() > 10 ? 'addClass' : 'removeClass']('scrolled'); }).scroll();
		$('.tooltip').tooltip();
		//$('form').submit(function(e){ $(document.body).addClass('loading'); });
		$(document.body).addClass('ready');
		
		$.datepicker.setDefaults({
			firstDay:1,
			showButtonPanel:false,
			closeText:'Закрыть',
			prevText:'Предыдущий',
			nextText:'Следующий',
			currentText:'Сегодня',
			dateFormat: 'dd.mm.yy',
			dayNames: ['Воскресение', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
			dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
			dayNamesShort: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
			monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
			monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек']
		});
		//onoffline ononline onunload
	};
	this.initHtmlEditor = function (selector, params) {
		/*'images_upload_url' => 'fff',
		 'automatic_uploads' => true,
		 //'image_uploadtab' => true,
		 'force_br_newlines' => true,
		 'force_p_newlines' => false,*/
		if (!window.tinymce) {
			var head = document.getElementsByTagName('head')[0],
				script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = '/js/tinymce/tinymce.min.js';
			script.onload = function () { Application.initHtmlEditor(selector, params); };
			head.appendChild(script);
			return;
		}
		tinymce.init($.extend({
			selector: selector,
			language: 'ru',
			branding: false,
			elementpath: false,
			statusbar: false,
			height: 400,
			resize: true, //enable vertical
			image_advtab: true,
			image_title: true,
			//automatic_uploads: true,
			file_picker_types: 'file image media',
			file_picker_callback: function () {
				console.log(arguments)
				tinymce.activeEditor.execCommand("mceInsertFile");
			},
			//remove_script_host: false,
			forced_root_block: false,
			extended_valid_elements: 'i[class],span[class]',
			relative_urls: false,
			document_base_url: "/",
			fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
			plugins: [
				'advlist autolink lists link image charmap print preview anchor',
				'searchreplace visualblocks code fullscreen',
				'insertdatetime media table paste moxiemanager'
			],
			toolbar: [//insertfile
				'styleselect fontsizeselect | bold italic removeformat | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | insertfile image link'
			],
			body_class: 'page-content',
			content_css: ['/css/default/']
		}, params));
	};
	this.ping = function(success) {
		//Http.getJSON('/ajax/ping/', function(result){ logoutFlag = (result && result.success); });
	};

};