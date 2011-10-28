(function($){
window.addEvent('domready', function(){
	var req = function(onSuccess){
		return function(e)
		{
			e.stop();
			new Request.JSON({
				'url': this.get('href')+'&format=json',
				'method': 'get',
				'onSuccess': onSuccess.bind(this)
			}).send();
		}
	}

	var vote = function(response){
		if (response.success){
			var meta = this.getParent('.content-container').getElement('.metadata');
			var rating = meta.getElement('.rating') || new Element('span.rating').inject(meta);
			var total = parseInt(rating.get('text') || 0, 10) + response.delta;
			rating.set('text', total > 0 ? '+'+total : total)
				.removeClass('(?:positive|negative)')
				.addClass(total > 0 ? 'positive' : 'negative')
		} else {
			alert(response.error);
		}
	};

	$('comments_list').addEvents({
		'click:relay(a.comment-delete)': 
		req(function(response){
			if (response.success) {
				this.getParent('li.comment').nix(true);
				var counter = $('comments_counter');
				counter.set('text', counter.get('text').toInt() - 1);
			}
		}),
		'click:relay(a.comment-like)': req(vote),
		'click:relay(a.comment-dislike)': req(vote)
	});

});
})(document.id)