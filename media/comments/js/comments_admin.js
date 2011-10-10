window.addEvent('domready', function(){
	var form = document.id('adminForm');
	form.addEvent('click:relay(.edit-comment)', function(event, clicked){
		var td = this.getParent();
		td.addClass('editing');
		var span = td.getElement('span');
		var text = span.get('text');
		span.set('text', '')
		var textarea = new Element('textarea', {'html': text.trim(), 'class': 'edit'}).inject(td);
		var dt = new DynamicTextarea(textarea, {'offset': 10});
		textarea.setStyle('padding', 5);
		dt.getLineHeight();
		dt.checkSize(true)
		new Element('div', {'class': 'actions'}).adopt(
			new Element('a', {'text': 'Cancel', 'href':'#', 'class': 'cancel-button'}),
			new Element('span', {'text': ' or '}),
			new Element('input', {'type': 'button', 'value': 'save', 'class': 'save-button'})
		).inject(td);
	});

	form.addEvent('click:relay(.cancel-button)', function(event, clicked){
		var td = this.getParent('td');
		td.removeClass('editing');
		var span = td.getElement('span');
		var textarea = td.getElement('textarea');
		span.set('text', textarea.get('text'));
		textarea.destroy();
		td.getElement('.actions').destroy();
		td.getElement('div').destroy();
		event.stop();
	});

	form.addEvent('click:relay(.save-button)', function(event, clicked){
		var td = this.getParent('td');
		var textarea = td.getElement('textarea');
		var id = td.getParent('tr').getElement('input[type=checkbox]').get('value');
		var text = textarea.get('value');
		var token = form.getElements('input[type=hidden]').filter(function(e){
			return e.get('name').match(/[a-z0-9]{32}/i) && e.get('value') == '1';
		})[0].get('name');
		console.log(id);
		console.log(text);
		console.log(token);
		new Request.JSON({
			url: 'index.php?option=com_comments&task=comments.edit&format=json',
			data: 'text='+text+'&id='+id+'&'+token+'=1',
			onSuccess: function(response){
				if (response.success) {
					console.log(response);
					td.removeClass('editing');
					var span = td.getElement('span');
					span.set('text', response.data);
					textarea.destroy();
					td.getElement('.actions').destroy();
					td.getElement('div').destroy();
				}
			}
		}).send();
	});
});