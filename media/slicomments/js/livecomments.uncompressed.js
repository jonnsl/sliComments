(function($){
window.addEvent('domready', function(){
	var comments_section = $('comments'),
		form = comments_section.getElement('form');

	if (!form) return;

	var tmpl = require('./tmpl_comment.js'),
		comments_list = $('comments_list'),
		comments_counter = $('comments_counter'),
		liveCommentsDisable = $('live-comments-disable'),
		liveCommentsInfo = $('live-comments-info'),
		latestComment = comments_list.getFirst(),
		lastCommentTime = latestComment ? latestComment.getElement('.metadata .created').get('data-created') : 0,
		currentComments = comments_list.getChildren().length,
		maxComments = Math.floor(comments_list.get('data-limit') * 1.5) || 30,
		queue = [],
		_slice = [].slice,
		_push = [].push,
		cc = comments_counter.get('html'),
		liveCommentsCounter = liveCommentsInfo.getElement('strong'),
		autoUpdate = false,
		isRunning = false;

	var request = new Request({
		url: 'index.php?option=com_slicomments&task=comments.live&item_id='+form.item_id.get('value')+'&extension='+form.extension.get('value'),
		format: 'raw',
		method: 'get',
		noCache: true,
		onSuccess: function(res){
			if (request.status == 200) {
				var comments = JSON.decode(res),
					temp = new Element('ul', {
						html: comments.map(function(comment){
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
			if (xhr.status == 400) {
				console.log(xhr.responseText);
			}
			// Don't try again
			clearInterval(requestTimer);
		}
	});

	/**
	 * The number of comments that will be removed from the queue.
	 *
	 * @type {number}
	 */
	var l = 0;

	/**
	 * Show the comments currently in the queue
	 *
	 * @param {Event=} e
	 */
	function showComments(e){
		if (e) {
			e.stop();
			liveCommentsInfo.dissolve();
		}
		if (check()) return;
		isRunning = true;
		if (!l) l = queue.length;
		var timer = (function(){
			if (l == 0) {
				isRunning = false;
				clearInterval(timer);
			} else {
				l--;
				insertComment(queue.shift());
			}
		}).periodical(500);
	};

	/**
	 * Check if showComments is running.
	 * if it's, update the size of the queue that need to be shown.
	 *
	 * @return {boolean}
	 */
	function check(){
		if (isRunning) {
			l = queue.length;
		}
		return isRunning;
	};

	/**
	 * Insert a comment in the comments list
	 *
	 * @param {Element} comment The comment to be inserted
	 */
	function insertComment(comment){
		comments_counter.set('text', ++cc)
		comment.setStyle('opacity', 0)
			.inject(comments_list, 'top')
			.fade();
		if (currentComments >= maxComments) {
			var toDestroy = comments_list.getChildren()[maxComments];
			if (toDestroy) {
				toDestroy.nix(true);
			}
		} else {
			currentComments++;
		}
	};

	/**
	 * Activate the auto update
	 *
	 * @param {Event} e
	 */
	function activateAutoUpdate(e){
		autoUpdate = true;
		showComments(e);
		liveCommentsDisable.show();
	};

	/**
	 * dectivate the auto update
	 *
	 * @param {Event} e
	 */
	function disableAutoUpdate(e){
		e.stop();
		liveCommentsDisable.hide();
		autoUpdate = false;
	};

	/**
	 * Check for new comments every 3sec
	 *
	 * @type {number} IntervalID
	 */
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