import sortString from '../helpers/sortAlphaStr';

/*
 * almFiltersBuildURL
 * Send the final output to the almFilter() function in core ALM
 *
 * @param target string   The target ALM container 
 * @param data object   The data obj for the filter
 * @return null   ddispatch global almFilter() function call
 *
 * @since 1.0 
 */

let buildURL = (filter, currentURL) => {
	
	let url = '';
	let key = filter.dataset.key;
	let fieldtype = filter.dataset.fieldtype;
	let taxonomy = filter.dataset.taxonomy;
	let metaKey = filter.dataset.metaKey;
	let metaOperator = filter.dataset.metaOperator; 
	let metaType = filter.dataset.metaType;	
		
	let title = (key === 'taxonomy') ? `${taxonomy}` : `${key}`; // Convert type to taxonomy slug
	title = (key === 'meta') ? `${metaKey}` : title; // Convert type to custom field slug
	
	// If current URL is empty, prepend ? for the querystring
	title = (currentURL === '') ? `?${title}` : `&${title}`;	
	
	// Get preselected value
	let preselected = filter.dataset.selectedValue;	
	
	switch (fieldtype){  	
		
		case 'select_multiple' : 
		
		   let mSelect = filter.querySelector('select');
		   let options = mSelect && mSelect.options;
		   let mSelectVal = '';	
			let mSelectCount = 0;	 
			
			if(!mSelect){
				break; // exit if empty
			}
			
			// Loop all <option/> elements to build URL
         [...options].forEach((option, e) => {
            if (option.selected) {			
					mSelectVal += (mSelectCount > 0) ? '+' : '';
					if(option.value !== ''){ // Confirm option has a value
						mSelectVal += option.value;
						mSelectCount++;
					}
				}		   
		   });
		   url += (mSelectCount > 0) ? `${title}=${mSelectVal}` : url;
			
			break;
		
		case 'select' : 
			
			let select = filter.querySelector('select');
			if(!select){
				break; // exit if empty	
			}
			
			url += (select.value === '#') ? '' : `${title}=${select.value}`;
			
			break;
		
		case 'text' : 
		case 'range_slider' : 
			let textfield = filter.querySelector('input[type=text]');
			if(!textfield){
				break; // exit if empty	
			}
			
			url += (textfield.value === '') ? '' : `${title}=${textfield.value}`;
			
			break;
			
		case 'date_picker' :  
			let datepicker = filter.querySelector('input.flatpickr-input');
			if(!datepicker){
				break; // exit if empty
			}
			
			if(datepicker.value){
   			// Replace ` | ` with `+` for range mode
				let value = datepicker.value.replace(' | ', '+');
				url += (datepicker.value === '') ? '' : `${title}=${value}`;
			}
			
			break;
		
		default :  
			
			let items = filter.querySelectorAll('.alm-filter--link'); // Get all inputs
			let checkedVal = '';	
			let count = 0;
			
			if(!items.length){
				break; // exit if empty
			}
			
			[...items].forEach((item, e) => {
   			
				if(item.classList.contains('active')){					
					if(count > 0 && item.dataset.value !== ''){
						checkedVal += '+';
					}
					if(item.dataset.value !== ''){
						// Confirm checked has a value
						checkedVal += item.dataset.value;
						count++;
					}					
				}
				
			});
				
		
		// If has selection and doesn't equal the preselected value
		// Remove the preselected value as it shouldn't be shown in the URL
		url += (count > 0 && sortString(preselected) !== sortString(checkedVal)) ? `${title}=${checkedVal}` : url;
		
	}
	
	return url;
	
};

export default buildURL;