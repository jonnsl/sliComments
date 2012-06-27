module.exports = new Class({

	options: {
		maxLength: 500
	},

	textarea: null,
	counter: null,

	initialize: function(textarea, counter) {
		this.textarea = $(textarea);
		this.counter = $(counter);

		this.textarea.store('charCount', this);

		this.options.maxLength = textarea.get('data-maxlength') || 500;

		var update = this.update_counter.bind(this);

		if (!Browser.chrome && !Browser.safari) {
			this.textarea.addEvents({
				'blur': update,
				'change': update,
				'focus': update,
				'keydown': update,
				'keypress': update,
				'keyup': update,
				'paste': update
			});
		}
		this.textarea.addListener('input', update);

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