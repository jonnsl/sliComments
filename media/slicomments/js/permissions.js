(function($){
window.addEvent('domready', function(){
	var groups = $('groups');
	groups.getFirst().addClass('selected')
	var actions = $('actions');
	var elements = {};
	actions.getChildren().each(function(item){
		elements[item.get('data-id')] = item;
	});
	$('groups').addEvent('click:relay(.group)', function(e, clicked){
		groups.getElements('.selected').removeClass('selected');
		this.addClass('selected');
		var id = this.get('data-id');
		var scroll = actions.getScroll();
		var position = Object.map(elements[id].getPosition(actions), function(value, axis){
			return value + scroll[axis];
		});
		actions.scrollTo(position.x, position.y);
	});
	actions.addEvent('change:relay(select)', function(){
		var value = !!this.get('value').toInt();
		this.removeClass(value ? 'denied' : 'allowed')
			.addClass(value ? 'allowed' : 'denied');
	});
});
})(document.id)