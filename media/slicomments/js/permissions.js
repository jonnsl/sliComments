window.addEvent('domready', function(){
	var groups = document.id('groups');
	groups.getFirst().addClass('selected')
	var actions = document.id('actions');
	var elements = {};
	actions.getChildren().each(function(item){
		elements[item.get('data-id')] = item;
	});
	document.id('groups').addEvent('click:relay(.group)', function(e, clicked){
		groups.getElements('.selected').removeClass('selected');
		this.addClass('selected');
		var id = this.get('data-id');
		var scroll = actions.getScroll();
		var position = Object.map(elements[id].getPosition(actions), function(value, axis){
			return value + scroll[axis];
		});
		actions.scrollTo(position.x, position.y);
	});
});