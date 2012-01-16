(function($){
window.addEvent('domready', function(){
	var form = $('adminForm');
	var editComment = function(event, clicked){
		event.stop();
		var td = this.get('tag') == 'td' ? this : this.getParent('td');
		if (td.hasClass('editing')) return;
		td.addClass('editing');
		var span = td.getElement('.text');
		var text = span.get('text');
		span.setStyle('display', 'none');
		var textarea = new Element('textarea', {'html': text.trim(), 'class': 'edit'}).inject(td);
		var dt = new DynamicTextarea(textarea, {'offset': 10});
		textarea.setStyle('padding', 5);
		dt.getLineHeight();
		dt.checkSize(true)
		new Element('div', {'class': 'comments-post'}).adopt(
			new Element('a', {'text': 'Cancel', 'href':'#', 'class': 'cancel-button'}),
			new Element('span', {'text': ' or '}),
			new Element('input', {'type': 'button', 'value': 'save', 'class': 'save-button'})
		).inject(td);
	};
	form.addEvent('dblclick:relay(.text)', editComment);
	form.addEvent('click:relay(.edit-comment)', editComment);

	form.addEvent('click:relay(.cancel-button)', function(event, clicked){
		var td = this.getParent('td');
		td.removeClass('editing');
		td.getElement('.text').setStyle('display', 'block');
		td.getElement('textarea').destroy();
		td.getElement('.comments-post').destroy();
		td.getElement('div').destroy();
		event.stop();
	});

	form.addEvent('click:relay(.save-button)', function(event, clicked){
		var td = this.getParent('td');
		var textarea = td.getElement('textarea');
		var data = {
			id: td.getParent('tr').getElement('input[type=checkbox]').get('value'),
			text: textarea.get('value')
		};
		var token = form.getElements('input[type=hidden]').filter(function(e){
				return e.get('name').match(/[a-z0-9]{32}/i) && e.get('value') == '1';
		})[0].get('name');
		data[token] = 1;
		new Request({
			url: 'index.php?option=com_slicomments&task=comments.edit',
			format: 'raw',
			data: data,
			onSuccess: function(response){
				console.log(response);
				td.removeClass('editing');
				td.getElement('.text')
					.set('html', response)
					.setStyle('display', 'block');
				textarea.getParent('div').destroy();
				td.getElement('.comments-post').destroy();
			},
			onFailure: function(xhr){
				alert(xhr.responseText);
			}
		}).send();
	});

	// Auto complete for filter_author and filter_article
	['author', 'article'].each(function(a){
		new Meio.Autocomplete(form['filter_' + a], 'index.php?option=com_slicomments&task=comments.get' + a.capitalize() + 's&format=raw', {
			filter: {
				filter: function(text, data){return true;},
				formatMatch: function(text, data){return data;},
				formatItem: function(text, data, i){
					return data.replace(new RegExp('^(' + text.escapeRegExp() + ')', 'gi'), '<strong>$1</strong>');
				}
			},
			urlOptions: {
				max: 10
			},
			requestOptions: {
				method: 'get'
			},
			onSelect: function(){
				updateWithDelay.call(form['filter_' + a]);
			}
		});
	});

	// Transform filter_category into 'chosen'
	var chosen = new Chosen(form.filter_category, {allow_single_deselect: true});

	// Toogle check behaviour
	$('toggle-check').addEvent('click', function(){
		this.form.getElements('input[type=checkbox]:not(#toggle-check)').each(function(e){
			e.checked = this.checked;
		}.bind(this));
	});

	// Filter buttons behaviour
	var filterBar = $('filter-bar'),
		filters = filterBar.getElements('.filter-select'),
		closeFilters = function(e){
			if (!e || !e.target.getParent('.filter-select')) {
				filters.removeClass('open');
				document.body.removeEvent('click', closeFilters);
			}
		};

	filterBar.addEvent('click:relay(.filter-button)', function(e){
		e.stop();
		var parent = this.getParent();
		if (parent.hasClass('open')) {
			filters.removeClass('open');
		} else {
			closeFilters();
			parent.addClass('open');
			if (parent.hasClass('filter-text')) parent.getElement('.filter-container .filter-input').focus();
			document.body.addEvent('click', closeFilters);
		}
	})

	var comments = $('comments');

	// Filter tracker
	var filterTracker = [],
		addFilter = function(filter){
			if (filterTracker.length == 0){
				form.addClass('hasFilters');
				filterTracker.push(filter);
			}
			else if (!filterTracker.contains(filter)){
				filterTracker.push(filter);
			}
		},
		removeFilter = function(filter){
			var i = filterTracker.indexOf(filter);
			if (i != -1) {
				filterTracker.splice(i, 1);
			}
			if (filterTracker.length == 0){
				// Hide "reset filters" link
				form.removeClass('hasFilters');
			}
		},
		resetFilters = function(){
			// Clean the tracker
			filterTracker = [];
			// Reset values
			['search', 'author', 'article', 'order', 'order_Dir'].each(function(name){
				form['filter_' + name].set('value', '');
			});
			// Special case for filter_category
			if (form.filter_category.get('value') != '') {
				chosen.results_reset();
			}
			filter_status.getElements('input').each(function(e){
				if (e.get('value') == 0 || e.get('value') == 1) {
					e.checked = true;
				} else {
					e.checked = false;
				}
			});
			// Hide "reset filters" link
			form.removeClass('hasFilters');
		}

	// Update function
	var update = function(){
		new Request.HTML({
			method: 'get',
			url: 'index.php?option=com_slicomments&task=comments.display',
			format: 'raw',
			data: form.toQueryString(),
			update: comments,
			onRequest: showSpinner,
			onSuccess: hideSpinner,
			onFailure: function(xhr){
				alert('error');
				console.log(xhr);
			}
		}).send();
	};

	// Search, Filter by article, Filter by author
	var timeout,
	updateWithDelay = function(){
		if (timeout) {
			clearTimeout(timeout);
		}
		if (this.get('value') != '') {
			addFilter(this.get('name'));
		} else {
			removeFilter(this.get('name'));
		}
		timeout = (function(){
			update();
		}).delay(1000);
	};
	filterBar.addEvent('input:relay(input)', updateWithDelay);

	// Filter by category
	$('filter-category').addEvent('change', function(){
		if (this.get('value') != '') {
			addFilter('filter-category');
		} else {
			removeFilter('filter-category');
		}
		update();
		closeFilters();
	});

	// Filter by status
	var filter_status = $('filter-status'),
		timeout2;
	filter_status.addEvent('change:relay(input)', function(e){
		var checked = filter_status.getElements('input:checked');
		// At least one checkbox must be checked
		if (checked.length === 0) {
			this.checked = true;
			return;
		}
		if (timeout2) {
			clearTimeout(timeout2);
		}
		if (checked.filter(function(e){return e.get('value') !=1 && e.get('value') !=0;}).length == 0){
			removeFilter('filter_status');
		} else {
			addFilter('filter_status');
		}
		timeout2 = (function(){
			update();
		}).delay(800);
	});

	form.getElements('.sort-column').addEvent('click', function(e){
		e.stop();
		var oldValue = form.filter_order.get('value');
		if (oldValue != this.get('data-sort')) {
			form.filter_order.set('value', this.get('data-sort'));
			form.filter_order_Dir.set('value', 'DESC');
			form.getElements('.icon-sort').removeClass('sort-dir-asc|sort-dir-desc')
			this.getElement('.icon-sort').addClass('sort-dir-desc');
		} else {
			oldValue = form.filter_order_Dir.get('value');
			var newValue = oldValue == 'DESC' ? 'ASC' : 'DESC';
			form.filter_order_Dir.set('value', newValue);
			this.getElement('.icon-sort').removeClass('sort-dir-asc|sort-dir-desc').addClass('sort-dir-' + newValue.toLowerCase());
		}
		if (form.filter_order_Dir.get('value') !== 'DESC'){
			addFilter('filter_order_Dir')
		} else {
			removeFilter('filter_order_Dir')
		}
		if (form.filter_order.get('value') !== 'created'){
			addFilter('filter_order')
		} else {
			removeFilter('filter_order')
		}
		update();
	});

	var clear_filters;
	if (clear_filters = $('clear-filters')){
		clear_filters.addEvent('click', function(e){
			e.stop();
			resetFilters();
			form.getElements('.icon-sort').removeClass('sort-dir-asc|sort-dir-desc');
			form.getElements('.sort-column').filter(function(a){return a.get('data-sort') == 'created';})[0].getElement('.icon-sort').addClass('sort-dir-desc');
			update();
		});
	}
});
})(document.id);
