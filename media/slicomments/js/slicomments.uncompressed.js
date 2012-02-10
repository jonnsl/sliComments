(function($){

var charCount = new Class({

	options: {
		maxLength: 500
	},

	textarea: null,
	counter: null,

	initialize: function(textarea, counter, options) {
		this.textarea = $(textarea);
		this.counter = $(counter);

		this.textarea.store('charCount', this);

		this.options.maxLength = textarea.get('data-maxlength') || 500;

		var update = this.update_counter.bind(this);

		if (Browser.chrome || Browser.safari) {
			this.textarea.addListener('input', update);
		} else {
			this.textarea.addEvents({
				'blur': update,
				'change': update,
				'focus': update,
				'keydown': update,
				'keypress': update,
				'keyup': update,
				'paste': update
			});
			this.textarea.addListener('input', update);
		}

		update();
	},

	update_counter: function() {
		var remaining_chars = this.options.maxLength - this.textarea.value.length;
		this.counter.set('text', remaining_chars);
		if (remaining_chars <= 5) {
			this.counter.setStyle('color', '#900');
		} else {
			this.counter.setStyle('color', null);
		}
		if (remaining_chars < 0) {
			this.textarea.form.getElement('button').set('disabled', true)
		} else {
			this.textarea.form.getElement('button').set('disabled', false)
		}
	}
});

window.addEvent('domready', function(){
	var section = $('comments');
	section.removeClass('no-js');
	var comments_count = $('comments_counter');
	var list = $('comments_list');

	var req = function(onSuccess, onFailure){
		return function(e)
		{
			e.stop();
			new Request({
				url: this.get('href'),
				format: 'raw',
				method: 'get',
				onSuccess: onSuccess.bind(this),
				onFailure: function(xhr){
					alert(xhr.responseText);
				}
			}).send();
		}
	}

	var vote = req(function(response){
		var meta = this.getParent('.content-container').getElement('.metadata');
		var rating = meta.getElement('.rating') || new Element('span.rating').inject(meta);
		var total = (rating.get('text').toInt() || 0) + response.toInt();
		rating.set('text', total > 0 ? '+'+total : total)
			.removeClass('(?:positive|negative)')
			.addClass(total > 0 ? 'positive' : 'negative')
	});

	section.addEvents({
		'click:relay(.comment-delete)':
		req(function(response){
			this.getParent('li.comment').nix(true);
			comments_count.set('text', comments_count.get('text').toInt() - 1);
		}),
		'click:relay(.comment-like)': vote,
		'click:relay(.comment-dislike)': vote,
		'click:relay(.comment-flag)':
		req(function(response){
			alert(response);
		}),
		'click:relay(.slicomments-spoiler button)': function (){
			var p = this.getParent();
			if (p.hasClass('spoiler-hide')) {
				p.removeClass('spoiler-hide');
				this.set('text', this.get('data-hide'));
			} else {
				p.addClass('spoiler-hide');
				this.set('text', this.get('data-show'));
			}
		},
		'click:relay(.cancel-reply)': function (e){
			e.stop();
			this.getParent('.comment').getElement('.comment-reply').getParent().setStyle('display', null);
			this.getParent('form').destroy();
		}
	});

	var form = section.getElement('form');
	if (!form) return;
	var textarea = form.getElement('textarea');
	new charCount(textarea, form.getElement('.chars-count'));
	var logged = form.get('data-logged') == 1 ? true : false;
	var lang = document.getElement('html').get('lang');

	// Make sure that the language is in the format xx-XX
	lang = lang.split('-');
	lang[1] = lang[1].toUpperCase();
	lang = lang.join('-');

	if (Locale.list().contains(lang)) Locale.use(lang);

	var validate = function(form){
		var validator = form.retrieve('validator');
		if (!validator){
			validator = new Form.Validator.Inline(form, {
				scrollToErrorsOnSubmit: false,
				evaluateFieldsOnChange: false,
				evaluateFieldsOnBlur: false
			});
			form.store('validator', validator);
		}
		return validator.validate();
	}

	var originalHeight = textarea.getDimensions().y;
	textarea.addEvents({
		focus: function() {
			if (this.value.length == 0) this.tween('height', originalHeight, 100);
		},
		blur: function() {
			if (this.value.length == 0) this.tween('height', originalHeight);
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
			new OverText(form.name);
			new OverText(form.email);
		}
	}

	// Ajax

	section.addEvent('click:relay(.comment-submit)', function(e){
		e.stop();
		if (validate(this.form)) {
			new Request.HTML({
				url: this.form.get('action'),
				format: 'raw',
				method: 'post',
				data: this.form,
				onSuccess: function(responseTree){
						responseTree[0].inject(list, this.form.get('data-position'));
						comments_count.set('text', comments_count.get('text').toInt() + 1);
						if (this.form.hasClass('reply-form')){
							this.form.destroy();
						} else {
							this.form.reset();
							this.form.text.retrieve('charCount').update_counter();
							this.form.text.fireEvent('blur');
						}
						OverText.update();

				}.bind(this),
				onFailure: function(xhr){
					alert(xhr.responseText);
				}
			}).send();
		}
	});

	section.addEvent('click:relay(.comment-reply)', function(e){
		e.stop();
		var replyForm = form.clone();
		replyForm.addClass('reply-form');
		replyForm.getElements('.validation-advice').destroy();
		var comment = this.getParent('.comment');
		var name = comment.getElement('.metadata .author').get('text').trim();
		replyForm.inject(comment.getElement('.clr'), 'before');
		var textarea = replyForm.getElement('textarea').addClass('init');
		textarea.set('value', '@'+name+' ').setCaretPosition("end");
		new charCount(textarea, replyForm.getElement('.chars-count'));
		this.getParent().setStyle('display', 'none');
	});
});
})(document.id);
