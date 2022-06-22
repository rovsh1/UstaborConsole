$.fn.cardgrid = function(options){
	var $el = $(this).addClass('card-grid'),
		$body = $el.find('div.card-body'),
		empty = $body.html() === '';
	if (empty || false === options.expanded)
		$el.addClass('collapsed');
	$el.find('div.card-title').click(function(){
		if (empty) {
			empty = false;
			load(options.url);
		}
		$el.toggleClass('collapsed');
	});
	function onlink(e) {
		e.preventDefault();
		load($(this).attr('href'));
	}
	function load(url) {
		$body.addClass('loading');
		Http.get(url, function(html){
			$body.html(html).removeClass('loading');
			$body.find('div.paginator a,th a').click(onlink);
		});
	}
	
};