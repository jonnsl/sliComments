(function($){
window.addEvent('domready', function(){
	var list = $('emoticons-list'),
		n = list.childNodes.length - 1,
		root = list.get('data-root'),
		joomla3 = (typeof(jQuery) !== 'undefined');

	var template = list.getFirst().clone();
	var input = template.getElement('input');
	var select = template.getElement('select');
	function Emoticon(emoticon){
		n = ++n;

		// Update names
		input.set('name', 'jform[emoticons]['+n+'][emoticon]');
		select.set('name', 'jform[emoticons]['+n+'][file]');

		// Update selected value
		if (typeof emoticon == 'undefined') {
			select.options[0].selected = true;
		} else {
			Array.from(select.options).some(function(option){
				if (option.get('text') == emoticon) {
					option.selected = true;
					return true;
				}
			});
		}
		input.set('value', emoticon ? ':'+emoticon+':' : '')
		update_preview.call(select);

		return template.clone();
	}

	function update_preview(){
		var value = this.get('value');
		var img = this.getNext('.emoticon-preview');
		if (value) {
			img
				.set('src', root+'/'+value)
				.setStyle('display', null)
				.addEvent('error', function(){
					this.setStyle('display', 'none')
				});
		} else {
			img.setStyle('display', 'none');
		}
	}

	$('add-emoticon').addEvent('click', function(e){
		e.stop();
		var item = new Emoticon();
		list.grab(item, 'top');
		if (joomla3) {
			jQuery(item.getElement('select')).chosen({
				disable_search_threshold : 10,
				allow_single_deselect : true
			}).change(update_preview);
		}
	});

	$('add-all-emoticons').addEvent('click', function(e){
		e.stop();
		var emoticons = select.getChildren().map(function(item){return item.get('text');});
		var added = list.getElements('select').map(function(item){return item.getSelected()[0].get('text');});
		var items = [];
		added.diff(emoticons).each(function(emoticon){
			items.push(new Emoticon(emoticon));
		});
		list.adopt(items);
		if (joomla3) {
			jQuery(list.getElements('select')).chosen({
				disable_search_threshold : 10,
				allow_single_deselect : true
			}).change(update_preview);
		}
	});

	$('remove-all-emoticons').addEvent('click', function(e){
		e.stop(); n = 0;
		list.empty();
	});

	list.addEvent('change:relay(select)', update_preview);

	list.addEvent('click:relay(.delete)', function(){
		this.getParent().nix(true);
	});

	Array.implement({
		diff: function(c, m){
			var d = [], e = -1, h, i, j, k;
			for(i = c.length, k = this.length; i--;){
				for(j = k; j && (h = c[i] !== this[--j]););
				h && (d[++e] = m ? i : c[i]);
			}
			return d;
		}
	});
});
})(document.id);
