/*
 * setDefaults
 * Set the default state of the filter.
 
 * @return null 
 * @since 1.2
 */ 
 
export function setDefaults(container){
	let found = false;
	let items = container.querySelectorAll('.alm-filter--link');
	[...items].forEach((item, e) => {
		if(item.classList.contains('active')){
			found = true;
	   } 
   });
   
   if(found) return false;
   
   let default_item = container.querySelector('.alm-filter--link[data-selected="true"]');
   if(default_item){
	   default_item.classList.add('active');
   }
	
}


/*
 * restoreDefault
 * Restore the default value of the filter.
 
 * @return null 
 * @since 1.2
 */ 
 
export function restoreDefault(filter){
   
	let fieldtype = filter.dataset.fieldtype; // Filter container
	let selected_value = filter.dataset.selectedValue; // Default selected value
	
	switch(fieldtype) {  	
   	
   	case 'radio' :
   	   
      	let radios = filter.querySelectorAll('.alm-filter--link');
         [...radios].forEach((radio) => {
      		if(radio.dataset.value === selected_value){
               radio.classList.add('active');
      		} else {
         		radio.classList.remove('active');
      		}
         });
         
      break;
         	
   	case 'select':
   	
   		// If (pre)selected value is null, set value to #
	   	selected_value = (selected_value == null || selected_value === '') ? '#' : selected_value;
	   		   	   
   	   let select = filter.querySelector('select');
   	   let value = '';
   	   
   	   [...select.options].forEach((item, index) => {
      	   if(item.value === selected_value){
               select.value = item.value; // Set <select/> value
            }
         });	
      
      break;
         	
   	case 'select_multiple':
   	
   		// If (pre)selected value is null, set value to #
	   	selected_value = (selected_value == null || selected_value === '') ? '#' : selected_value;
	   		   	   
   	   let select_multiple = filter.querySelector('select');
   	   let value_multiple = '';
   	   
   	   [...select_multiple.options].forEach((item, index) => {
      	   if(item.value === selected_value){
               select.value = item.value; // Set <select/> value
            } else {
               item.selected = false;
            }
         });	
      
      break;
      
      case 'checkbox':
      
      	let checkboxes = filter.querySelectorAll('.alm-filter--link');
         [...checkboxes].forEach((checkbox) => {
         	checkbox.classList.remove('active');
         });
      
      break;
      
      default: // textfield
      	
      	let input = filter.querySelector('input');
      	input.value = '';
      
      break;
   	
	}
	
}


/*
 * getDefault
 * Get the default/fallback value of the filter
 
 * @return returnVal string 
 * @since 1.4.1
 */ 
 
export function getDefault(filter){   
   let returnVal = '';	
	let key = filter.dataset.key;
	let defaultValue = filter.dataset.defaultValue;
  
	if(defaultValue){  // If defaultValue is set
   	returnVal = defaultValue;
	}
	
	return returnVal;	
}


