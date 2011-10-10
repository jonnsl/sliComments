/*
---
description: DynamicTextarea

license: MIT-style

authors:
- Amadeus Demarzi (http://amadeusamade.us)

requires:
 core/1.3: [Core/Class, Core/Element, Core/Element.Event, Core/Element.Style, Core/Element.Dimensions]

provides: [DynamicTextarea]
...
*/

(function(){

// Prevent the plugin from overwriting existing variables
if (this.DynamicTextarea) return;

var DynamicTextarea = this.DynamicTextarea = new Class({

	Implements: [Options, Events],

	options: {
		value: '',
		minRows: 1,
		delay: true,
		lineHeight: null,
		offset: 0,
		padding: 0

		// AVAILABLE EVENTS
		// onCustomLineHeight: (function) - custom ways of determining lineHeight if necessary

		// onInit: (function)

		// onFocus: (function)
		// onBlur: (function)

		// onKeyPress: (function)
		// onResize: (function)

		// onEnable: (function)
		// onDisable: (function)

		// onClean: (function)
	},

	textarea: null,

	initialize: function(textarea,options) {
		this.textarea = document.id(textarea);
		if (!this.textarea) return;

		this.setOptions(options);

		this.parentEl = new Element('div',{
			styles:{
				padding:0,
				margin:0,
				border:0,
				height:'auto',
				width:'auto'
			}
		})
			.inject(this.textarea,'after')
			.adopt(this.textarea);

		// Prebind common methods
		['focus','delayCheck','blur','scrollFix','checkSize','clean','disable','enable','getLineHeight']
			.each(function(method){
				this[method] = this[method].bind(this);
			},this);

		// Firefox and Opera handle scroll heights differently than all other browsers
		if (window.Browser.firefox || window.Browser.opera) {
			this.options.offset =
				parseInt(this.textarea.getStyle('padding-top'),10) +
				parseInt(this.textarea.getStyle('padding-bottom'),10) +
				parseInt(this.textarea.getStyle('border-bottom-width'),10) +
				parseInt(this.textarea.getStyle('border-top-width'),10);
		} else {
			this.options.offset =
				parseInt(this.textarea.getStyle('border-bottom-width'),10) +
				parseInt(this.textarea.getStyle('border-top-width'),10);
			this.options.padding =
				parseInt(this.textarea.getStyle('padding-top'),10) +
				parseInt(this.textarea.getStyle('padding-bottom'),10);
		}

		// Disable browser resize handles, set appropriate styles
		this.textarea.set({
			'rows': 1,
			'styles': {
				'resize': 'none',
				'-moz-resize': 'none',
				'-webkit-resize': 'none',
				'position': 'relative',
				'display': 'block',
				'overflow': 'hidden',
				'height': 'auto'
			}
		});

		this.getLineHeight();
		this.fireEvent('customLineHeight');

		// Set the height of the textarea, based on content
		this.checkSize(true);
		this.textarea.addEvent('focus',this.focus);
		this.fireEvent('init',[textarea,options]);
	},

	// This is the only crossbrowser method to determine ACTUAL lineHeight in a textarea (that I am aware of)
	getLineHeight: function(){
		var backupValue = this.textarea.value;
		this.textarea.value = 'M';
		this.options.lineHeight = this.textarea.getScrollSize().y - this.options.padding;
		this.textarea.value = backupValue;
		this.textarea.setStyle('height', this.options.lineHeight * this.options.minRows);
	},

	// Stops a small scroll jump on some browsers
	scrollFix: function(){
		this.textarea.scrollTo(0,0);
	},

	// Add interactive events, and fire focus event
	focus: function(){
		this.textarea.addEvents({
			'keydown': this.delayCheck,
			'keypress': this.delayCheck,
			'blur': this.blur,
			'scroll': this.scrollFix
		});
		return this.fireEvent('focus');
	},

	// Clean out extraneaous events, and fire blur event
	blur: function(){
		this.textarea.removeEvents({
			'keydown': this.delayCheck,
			'keypress': this.delayCheck,
			'blur': this.blur,
			'scroll': this.scrollFix
		});
		return this.fireEvent('blur');
	},

	// Delay checkSize because text hasn't been injected into the textarea yet
	delayCheck: function(){
		if (this.options.delay === true)
			this.options.delay = this.checkSize.delay(1);
	},

	// Determine if it needs to be resized or not, and resize if necessary
	checkSize: function(forced) {
		var oldValue = this.options.value,
			modifiedParent = false;

		this.options.value = this.textarea.value;
		this.options.delay = false;

		if (this.options.value === oldValue && forced!==true)
			return this.options.delay = true;

		if (!oldValue || this.options.value.length < oldValue.length || forced) {
			modifiedParent = true;
			this.parentEl.setStyle('height',this.parentEl.getSize().y);
			this.textarea.setStyle('height', this.options.minRows * this.options.lineHeight);
		}

		var tempHeight = this.textarea.getScrollSize().y,
			offsetHeight = this.textarea.offsetHeight,
			cssHeight = tempHeight - this.options.padding,
			scrollHeight = tempHeight + this.options.offset;

		if (scrollHeight !== offsetHeight && cssHeight > this.options.minRows * this.options.lineHeight){
			this.textarea.setStyle('height',cssHeight);
			this.fireEvent('resize');
		}

		if(modifiedParent) this.parentEl.setStyle('height','auto');

		this.options.delay = true;
		if (forced !== true)
			return this.fireEvent('keyPress');
	},

	// Clean out this textarea's event handlers
	clean: function(){
		this.textarea.removeEvents({
			'focus': this.focus,
			'keydown': this.delayCheck,
			'keypress': this.delayCheck,
			'blur': this.blur,
			'scroll': this.scrollFix
		});
		return this.fireEvent('clean');
	},

	// Disable the textarea
	disable: function(){
		this.textarea.blur();
		this.clean();
		this.textarea.set(this.options.disabled,true);
		return this.fireEvent('disable');
	},

	// Enables the textarea
	enable: function(){
		this.textarea.addEvents({
			'focus': this.focus,
			'scroll': this.scrollFix
		});
		this.textarea.set(this.options.disabled,false);
		return this.fireEvent('enable');
	}
});

})();