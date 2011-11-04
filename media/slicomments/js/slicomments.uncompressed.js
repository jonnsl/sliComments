(function($){
window.addEvent('domready', function(){
	$$('.comments_form.no-js').removeClass('no-js');
	var comments_count = $('comments_counter');
	var list = $('comments_list');

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

	list.addEvents({
		'click:relay(a.comment-delete)': 
		req(function(response){
			if (response.success) {
				this.getParent('li.comment').nix(true);
				comments_count.set('text', comments_count.get('text').toInt() - 1);
			}
		}),
		'click:relay(a.comment-like)': req(vote),
		'click:relay(a.comment-dislike)': req(vote)
	});

	var form = $('comments_form');
	if (!form) return;
	var textarea = $('comments_form_textarea');
	var counter = $('comments-remaining-count');
	var logged = form.get('data-logged') == 1 ? true : false;
	var validator = new Form.Validator.Inline(form, {
		scrollToErrorsOnSubmit: false,
		evaluateFieldsOnChange: false,
		evaluateFieldsOnBlur: false
	});

	var update_counter = function() {
		remaining_chars = this.get('data-maxlength') - this.value.length;
		counter.set('text', remaining_chars);
		if (remaining_chars <= 5) {
			counter.setStyle('color', '#900');
			if (remaining_chars < 0) {
				form.getElement('button').set('disabled', true)
			}
		} else {
			form.getElement('button').set('disabled', false)
			counter.setStyle('color', null);
		}
	};
	textarea.addEvents({
		'keypress': update_counter,
		'focus': function() {
			this.addClass('init');
		},
		'blur': function() {
			if (this.value.length == 0) this.removeClass('init');
			update_counter.call(this);
		}
	});
	var placeholder_support = (function () {
		var i = document.createElement('input');
		return 'placeholder' in i;
	})()
	if (placeholder_support) {
		// Hide the labels
		if (!textarea.get('disabled')) $$('.comments_form_inputs li label').setStyle('display', 'none');
	}
	else {
		// Use OverText to simulate placeholders
		OverText.implement({
			attach: function(){
				var element = this.element,
					options = this.options,
					value = options.textOverride || element.get('placeholder') || element.get('alt') || element.get('title');
		
				if (!value) return this;
		
				var text = this.text = (element.getPrevious(options.element) || new Element(options.element).inject(element, 'after'))
					.addClass(options.labelClass)
					.setStyles({
						lineHeight: 'normal',
						position: 'absolute',
						cursor: 'text'
					})
					.set('html', value)
					.addEvent('click', this.hide.pass(options.element == 'label', this));
		
				if (options.element == 'label'){
					if (!element.get('id')) element.set('id', 'input_' + String.uniqueID());
					text.set('for', element.get('id'));
				}
				
				if (options.wrap){
					this.textHolder = new Element('div.overTxtWrapper', {
						styles: {
							lineHeight: 'normal',
							position: 'relative'
						}
					}).grab(text).inject(element, 'before');
				}
		
				return this.enable();
			}
		});
		new OverText(textarea, {
			positionOptions: {
				offset: {
					x: 6,
					y: 6
				}
			}
		});
		if (!logged) {
			new OverText($('comments_form_name'));
			new OverText($('comments_form_email'));
		}
	}

	// Ajax
	
	$('comments_form_send').addEvent('click', function(e){
		e.stop();
		if (validator.validate()) {
			new Request.JSON({
				url: form.get('action')+'&format=json',
				method: 'post',
				data: form,
				onSuccess: function(response){
					if (response.success) {
						new Element('li.comment', {
							'html': response.html
						}).inject(list, form.get('data-position'));
						comments_count.set('text', comments_count.get('text').toInt() + 1);
						form.reset();
						form.text.fireEvent('keypress');
						form.text.removeClass('init');
						OverText.update();
					}
				}
			}).send();
		}
	});
});
})(document.id)
