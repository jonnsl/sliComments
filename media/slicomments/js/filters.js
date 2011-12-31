
exports.human_time_diff = function(date){
	return new Date(parseInt(date, 10) * 1000).timeDiffInWords();
}

exports.format = function(date, format){
	return new Date(parseInt(date, 10) * 1000).format(format);
}

exports._ = function(s) {
	return Joomla.JText._(s);
}
