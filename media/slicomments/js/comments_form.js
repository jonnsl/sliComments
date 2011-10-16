(function($){
window.addEvent('domready', function(){
	$$('.comments_form.no-js').removeClass('no-js');

	var textarea = $('comments_form_textarea');
	var counter = $('comments-remaining-count');
	var form = $('comments_form');
	var logged = form.get('data-logged') == 1 ? true : false;
	var validator = new Form.Validator.Inline(form, {
		scrollToErrorsOnSubmit: false,
		evaluateFieldsOnChange: false,
		evaluateFieldsOnBlur: false
	});

	var update_counter = function() {
		remaining_chars = 500 - this.value.length;
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
	
	$('comments_form_send').addEvent('click', function(e){
		e.stop();
		if (validator.validate()) {
			new Request.JSON({
				url: form.get('action')+'&format=json',
				method: 'post',
				data: 'article_id='+form.article_id.value+(!logged ? '&name='+form.name.value+'&email='+form.email.value : '')+'&text='+form.text.value+'&'+form.getElement('input[type=hidden]').get('name')+'=1',
				onSuccess: function(response){
					if (response.success) {
						var data = response.data;
						new Element('li.comment', {
							'html': '<div class="comment-body"><div class="profile-image-container"><img class="profile-image" src="//www.gravatar.com/avatar/'+ data.email +'?s=40" alt="'+ data.name +'"></div><div class="content-container"><div class="content"><div class="author">'+data.name+':</div><div class="comment-text">'+data.text+'</div></div></div></div><div class="clr"></div>'
						}).inject($('comments_list'), 'top');
						var counter = $('comments_counter');
						counter.set('text', counter.get('text').toInt() + 1);
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