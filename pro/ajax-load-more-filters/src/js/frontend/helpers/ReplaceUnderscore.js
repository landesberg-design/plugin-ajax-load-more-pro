/**
 * ReplaceUnderscore
 * Remove the first _ from a string
 *
 * @since 1.8.1
 */
let ReplaceUnderscore = function(string = '') {
	let output = string;
	if(output && output[0] === '_'){
		output = output.substring(1)
	}
	return output;
};
export default ReplaceUnderscore;