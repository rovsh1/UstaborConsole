$.fn.avatarUpload = function() {
	var el = $(this).addClass('user-avatar editable');
	var input = $('<input type="file" accept="image/*" />').appendTo(el);
	$('<div class="btn"></div>').appendTo(el);
	
	input.change(function(){
		if (this.files.length === 0)
			return;
		var data = new FormData();
		data.append('user[image]', this.files[0]);
		el.addClass('loading');
		Http.ajax({
			url: '/profile/avatar/',
			method: 'post',
			data: data,
			dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
			success: function(r) {
				el.removeClass('loading');
				$('.user-avatar img').attr('src', '/file/' + r.image + '/');
			}
		});
	});
};