(function($){
window.addEvent('domready', function(){
	var list = $('emoticons');
	var n = $$('.emoticon').length - 1;
	var root = list.get('data-root');

	var update_preview = function(){
		this.getNext('img')
		.set('src', root+'/'+this.get('value'))
		.setStyle('display', 'block')
		.addEvent('error', function(){
			this.setStyle('display', 'none');
		});
	},
	add_emoticon = function(emoticon, li){
		n = ++n;
		li = li || list.getFirst().clone();
		var input = li.getElement('input').set('value', emoticon ? ':'+emoticon+':' : '').set('name', 'jform[emoticons]['+n+'][emoticon]');
		var select = li.getElement('select');
		if (typeOf(emoticon) === 'null') {
			select.options[0].selected = true;
		} else {
			Array.from(select.options).filter(function(option){
				return option.get('text') == emoticon;
			})[0].selected = true;
		}
		update_preview.call(select);
		select.fireEvent('onChange');
		select.set('name', 'jform[emoticons]['+n+'][file]');
		li.inject(list, 'top');
	}

	$('add-emoticon').addEvent('click', function(e){
		e.stop();
		add_emoticon();
	});

	$('add-all-emoticons').addEvent('click', function(e){
		e.stop();
		var emoticons = list.getFirst().getElement('select').getChildren().map(function(item){return item.get('text');});
		var added = list.getElements('select').map(function(item){return item.getSelected()[0].get('text');});
		added.diff(emoticons).each(function(emoticon){
			add_emoticon(emoticon);
		});
	});

	$('remove-all-emoticons').addEvent('click', function(e){
		e.stop();
		var li = list.getFirst().clone();
		list.empty();
		n = 0;
		add_emoticon(null, li);
	});

	list.addEvent('change:relay(select)', update_preview);

	list.addEvent('click:relay(.delete)', function(e){
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