/*
 * getKeyObject
 * Get the filter key and fieldtype (taxonomy, meta, standard(order, tag))
 * Need to get the type, because keys are different when using tax and meta query
 *
 * @param key string
 * @param value string
 * @return obj object
 */
let getKeyObject = function( key, value ) { 
	
	let target = '';
    
   if(document.querySelector('.alm-filter[data-taxonomy="'+ key +'"]')){  
	   // Taxonomy
	   target = document.querySelector('.alm-filter[data-taxonomy="'+ key +'"]');
	   
	} else if(document.querySelector('.alm-filter[data-meta-key="'+ key +'"]')){ 
		// Meta Query
		target = document.querySelector('.alm-filter[data-meta-key="'+ key +'"]');
		
	} else {	 
		// Standard
		target = document.querySelector('.alm-filter[data-key="'+ key +'"]');
		
	}
	
	
	if(!target) return false; // Exit if target does not exist
	
	let obj = {
		fieldType : target.dataset.fieldtype,
		target : target
	}
    
   return obj;
      
};
export default getKeyObject;