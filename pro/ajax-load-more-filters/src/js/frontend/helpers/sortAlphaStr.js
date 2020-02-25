/**
 * Sort a string alphabetically
 *
 * @param {String} str The string to sort
 * @since 1.7.4
 */
let sortString = function(str = ''){
	if(str === ''){
		return str;
	}
	var arr = str.split('');
	var sorted = arr.sort();
	return sorted.join('');
}
export default sortString;