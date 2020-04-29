import getKeyObject from './getKeyObject';
import getKeyElement from './getKeyElement';

/*
 * getKeyValue
 * Get the actual filter value by key (taxonomy, meta, standard(order, tag))
 * Need to get the type, because keys are different when using tax and meta query
 *
 * @param key string
 * @param value string
 * @return type value
 */
let getKeyValue = function( key, value ) {    
	      
   let obj = getKeyObject( key, value );	
      
   if(obj) { // Confirm obj exist
 
		let el = getKeyElement(obj.target, value, obj.fieldType);  
		if(!el){ // Exit if does not exist
			return '';
		}
		
	   let filterVal = '';		
	   
		switch (obj.fieldType){
			
			case 'select' :
				
				// If has selected value
				if(el.selectedOptions && el.selectedOptions.length){
					filterVal = el.selectedOptions[0].text;
				} else {
					filterVal = '';				
				}
				break;
			
			case 'text' : 
				filterVal = el.value;
				break;
			
			default : 
				filterVal = el.innerHTML;
			 
		}
	    
	   return filterVal;
   }
   
};
export default getKeyValue;