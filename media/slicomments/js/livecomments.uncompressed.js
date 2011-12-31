(function($){
window.addEvent('domready', function(){
	var comments_section = $('comments'),
		form = comments_section.getElement('form');

	if (!form) return;

	var tmpl = require('./tmpl_comment.js'),
		comments_list = $('comments_list'),
		comments_counter = $('comments_counter'),
		latestComment = comments_list.getFirst(),
		lastCommentTime = latestComment ? latestComment.getElement('.metadata .created').get('data-created') : 0,
		queue = [],
		_slice = [].slice,
		_push = [].push,

		request = new Request({
			url: 'index.php?option=com_slicomments&task=comments.live&item_id='+form.item_id.get('value')+'&extension='+form.extension.get('value'),
			format: 'raw',
			method: 'get',
			noCache: true,
			onSuccess: function(res) {
				if (request.status == 200) {
					var comments = JSON.decode(res),
						temp = new Element('ul', {
							html: comments.map(function(comment) {
								return tmpl.call(sliComments, comment);
							}).join('')
						});
					// add new comments to the end of the queue
					_push.apply(queue, _slice.call(temp.getChildren('li.comment')));
					lastCommentTime = comments[comments.length-1].created;
					if (autoUpdate) {
						showComments();
					} else {
						liveCommentsInfo.show();
						liveCommentsCounter.set('html', (isRunning ? comments : queue).length.toString());
					}
				}
			},
			onFailure: function(xhr){
				if (xhr.status == 400){
					console.log(xhr.responseText);
				}
				// Don't try again
				clearInterval(requestTimer);
			}
		}),

		cc = comments_counter.get('html'), // Total number of comments
		liveCommentsDisable = $('live-comments-disable'),
		liveCommentsInfo = $('live-comments-info'),
		liveCommentsCounter = liveCommentsInfo.getElement('strong'),
		autoUpdate = false,
		isRunning = false;


		/**
		 * Inject in the list the latest comments necessary to fill a page.
		 * Activated when the user click on "show them"
		 */
		var l = 0;
		function showComments(e){
			if (e){
				e.stop();
				liveCommentsInfo.dissolve();
			}
			if (check()) return;
			isRunning = true;
			if (!l) l = queue.length;
			// Inject the comments in the list, one at a time, with a interval of half a second between them.
			var timer = (function(){
				if (l == 0) {
					isRunning = false;
					clearInterval(timer);
					return;
				}
				l--;
				insertComment(queue.shift());
			}).periodical(500);
		};

		/**
		 * Check if showComments is running, if it's, update the size of the queue that need to be shown.
		 */
		function check(){
			if (!isRunning) return false;
			l = queue.length;
			return true;
		};

		function insertComment(comment){
			comments_counter.set('text', ++cc)
			comment.setStyle('opacity', 0)
				.inject(comments_list, 'top')
				.fade();
			comments_list.getLast().nix(true);
		};

		/**
		 * Activate the auto update
		 */
		function activateAutoUpdate(e){
			autoUpdate = true;
			showComments(e);
			liveCommentsDisable.show();
		};

		/**
		 * dectivate the auto update
		 */
		function disableAutoUpdate(e){
			e.stop();
			liveCommentsDisable.hide();
			autoUpdate = false;
		};

		var requestTimer = (function(){
			request.send({
				data: {
					lt: lastCommentTime
				}
			});
		}).periodical(3000);

	// Add events
	liveCommentsDisable.getElement('a').addEvent('click', disableAutoUpdate);
	liveCommentsInfo.getElement('a.update_comments').addEvent('click', activateAutoUpdate);
	liveCommentsInfo.getElement('a.show_comments').addEvent('click', showComments);
});
})(document.id);