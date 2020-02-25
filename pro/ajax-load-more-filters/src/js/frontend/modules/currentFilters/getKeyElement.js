/*
 * getKeyElement
 * Get the actual element of a filter target
 *
 * @param target element
 * @param value string
 * @param fieldType string
 * @return el element
 */
let getKeyElement = function( target, value, fieldType ) {
    		
   let el = '';
      
	switch (fieldType){
		
		case 'select' :
			el = target.querySelector('select.alm-filter--item');
			break;
		
		case 'text' : 
			el = target.querySelector('input.alm-filter--textfield');
			break;
		
		default : 
			el = target.querySelector('a.alm-filter--link[data-value="'+ value +'"]');
		
	}
    
   return el;
   
};
export default getKeyElement; 