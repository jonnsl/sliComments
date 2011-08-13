window.addEvent('domready', function(){
	document.id('comments_list').addEvent('click:relay(a.comment-delete)', function(e){
		e.stop();
		var link = this;
		onFailure = function(msg){
				if (typeof msg == 'string') {
					console.log(msg);
				}
			}
		new Request({
			url: this.get('href')+'&format=json',
			method: 'get',
			onRequest: function(){
				
			},
			onSuccess: function(responseText){
				var response = JSON.decode(responseText);
				if (response.success) {
					link.getParent('li.comment').nix(true);
					var counter = document.id('comments_counter');
					counter.set('text', counter.get('text').toInt() - 1);
				} else {
					onFailure(response.error);
				}
			},
			onFailure: onFailure
		}).send();
	})
});