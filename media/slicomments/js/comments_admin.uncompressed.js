var Chosen = require('./chosen'),
	DynamicTextarea = require('./DynamicTextarea'),
	Meio = require('./meio.autocomplete');

window.addEvent('domready', function(){
	var form = $('adminForm');

	var token = form.getElements('input[type=hidden]').filter(function(e){
				return e.get('name').match(/[a-z0-9]{32}/i) && e.get('value') == '1';
		})[0].get('name');

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

	form.addEvent('click:relay(.actions li:not(.delete-comment, .edit-comment) a)', function(e){
		e.stop();
		new Request({
			url: this.get('href'),
			format: 'raw',
			onSuccess: function(){
				var comment = this.getParent('.comment').removeClass('approved|unapproved|spam|trashed'),
					action = this.getParent('li');
				if (action.hasClass('approve-comment') || action.hasClass('not-spam-comment') || action.hasClass('restore-comment')){
					comment.addClass('approved');
				} else if (action.hasClass('unapprove-comment')) {
					comment.addClass('unapproved');
				} else if (action.hasClass('spam-comment')) {
					comment.addClass('spam');
				} else if (action.hasClass('trash-comment')) {
					comment.addClass('trashed');
				}
			}.bind(this),
			onFailure: function(xhr){
				alert(xhr.responseText);
			}
		}).send();
	});

	form.addEvent('click:relay(.actions li.delete-comment a)', function(e){
		e.stop();
		new Request({
			url: this.get('href'),
			format: 'raw',
			onSuccess: function(){
				this.getParent('tr').nix(true);
			}.bind(this),
			onFailure: function(xhr){
				alert(xhr.responseText);
			}
		}).send();
	});

	var toolbar = $('toolbar');
	toolbar.addEvent('click:relay(.toolbar, .btn)', function(e){
		e.stop();
		var ids = [],
			comments = new Elements,
			task = this.get('data-task');
		form.getElements('input:[type=checkbox]').each(function(checkbox){
			if(checkbox.get('name') == 'id[]' && checkbox.checked){
				ids.push(checkbox.get('value'));
				comments.push(checkbox.getParent().getNext('.comment'));
			}
		});
		new Request({
			url: 'index.php?option=com_slicomments&' + token + '=1',
			data: {
				id: ids,
				task: task
			},
			format: 'raw',
			onSuccess: function(){
				if (task == 'comments.delete'){
					comments.each(function(comment){
						comment.getParent('tr').nix(true);
					});
					update.delay(250);
				} else {
					comments.removeClass('approved|unapproved|spam|trashed');
					switch (task)
					{
						case 'comments.approve':
							comments.addClass('approved');
							break;
						case 'comments.unapprove':
							comments.addClass('unapproved');
							break;
						case 'comments.spam':
							comments.addClass('spam');
							break;
						case 'comments.trash':
							comments.addClass('trashed');
							break;
					}
				}
			},
			onFailure: function(xhr){
				alert(xhr.responseText);
			}
		}).send();
	});

	// Auto complete for filter_author and filter_item
	['author', 'item'].each(function(a){
		if (!form['filter_' + a]) return;
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
				var filter = 'filter_' + a;
				if (form[filter].get('value') != '') {
					addFilter(filter);
				} else {
					removeFilter(filter);
				}
				update();
				closeFilters();
			}
		});
	});

	// Transform filter_category into 'chosen'
	if (form.filter_category) {
		var chosen_category = new Chosen(form.filter_category, {allow_single_deselect: true});
	}
	var chosen_extension = new Chosen(form.filter_extension, {allow_single_deselect: true});

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
			['search', 'author', 'item'].each(function(name){
				if (name = form['filter_' + name]) name.set('value', '');
			});
			// Special case for filter_category
			if (form.filter_category && form.filter_category.get('value') != '') {
				chosen_category.results_reset();
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
		};

	// Update function
	var table = form.getElement('.adminlist'),
		request = new Request({
			method: 'get',
			url: 'index.php?option=com_slicomments&task=comments.display',
			format: 'raw',
			headers: {
				Accept: 'text/html, */*'
			},
			link: 'cancel',
			onRequest: function(){
				console.log('loading')
			},
			onSuccess: function(response){
				console.log('loaded');
				table.getElements('tbody,tfoot').destroy();
				table.adopt(new Element('table').set('html', response).getChildren());
			},
			onCancel: function(){
				console.log('canceled');
			},
			onFailure: function(xhr){
				alert('error');
				console.log(xhr);
			}
		}),
		update = function(){
			request.send({data: form.toQueryString()});
		},
		updateDebounced = update.debounce();

	// Search, Filter by item, Filter by author
	filterBar.addEvent('input:relay(input)', function(){
		if (this.get('value') != '') {
			addFilter(this.get('name'));
		} else {
			removeFilter(this.get('name'));
		}
		updateDebounced();
	});

	// Filter by category
	try
	{
		$('filter-category').addEvent('change', function(){
			if (this.get('value') != '') {
				addFilter('filter-category');
			} else {
				removeFilter('filter-category');
			}
			update();
			closeFilters();
		});
	} catch(e){}

	// Filter by extension
	$('filter-extension').addEvent('change', function(){
		form.submit();
	});

	// Filter by status
	var filter_status = $('filter-status');
	filter_status.addEvent('change:relay(input)', function(e){
		var checked = filter_status.getElements('input:checked');
		// At least one checkbox must be checked
		if (checked.length === 0) {
			this.checked = true;
			return;
		}

		if (checked.filter(function(e){return e.get('value') !=1 && e.get('value') !=0;}).length == 0){
			removeFilter('filter_status');
		} else {
			addFilter('filter_status');
		}

		updateDebounced();
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
		update();
	});

	var clear_filters;
	if (clear_filters = $('clear-filters')){
		clear_filters.addEvent('click', function(e){
			e.stop();
			resetFilters();
			update();
		});
	}

	window.Joomla.submitform = function noop(){};

	table.addEvent('click:relay(.pagination a)', function(e){
		e.stop();
		window.scrollTo(0, form.getPosition().y);
		update();
	});

	table.addEvent('change:relay(#limit)', function(){
		window.scrollTo(0, form.getPosition().y);
		update();
	});
});

Function.implement({
	debounce: function(threshold) {
		var func = this, timeout;

		return function debounced() {
			var obj = this, args = arguments;
			function delayed() {
				func.apply(obj, args);
				timeout = null;
			};

			if (timeout) {
				clearTimeout(timeout);
			}

			timeout = setTimeout(delayed, threshold || 500);
		};
	}
});
