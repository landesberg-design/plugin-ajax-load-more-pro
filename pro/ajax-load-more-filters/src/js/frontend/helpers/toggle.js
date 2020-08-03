/**
 * Set the state of the `Toggle All` button
 *
 * @param {String} fieldtype the current field type (checkbox, radio etc)
 * @param {Array} items Array of items
 * @param {HTMLElement} parent The parent `.alm-filter` div
 * @since 1.8.1
 */
export function toggleAll(fieldtype, items = '', parent){
   
	let allElement = parent.querySelector('[data-type=all]');	
	if(fieldtype === 'checkbox' && allElement){  
			
		// Get all standard checkboxes not 'ALL'
		let checkboxItems = [...items].filter(function(item){
			return (item.dataset.type === 'checkbox');
		});
		
		let allChecked = true; 
		if(checkboxItems){
			for (let item of checkboxItems) {
   			if(!item.classList.contains('active')){
      			allChecked = false;
      			break;
   			}     			
         } 
         if(allChecked){
            allElement.classList.add('active');
            allElement.setAttribute('aria-checked', true);
         } else {
            allElement.classList.remove('active');
            allElement.setAttribute('aria-checked', false);              
         }
      }
   }
}



/**
 * Set the active state of Toggle All buttons on page load
 *
 * @param {HTMLElement} filters The 1.alm-filters` container
 * @since 1.8.1
 */
export function toggleSelect(filters = null){
	
	if(!filters){
		return false;
	}
	
	// Get checkbox filters ONLY
	let checkboxFilters = filters.querySelectorAll('.alm-filter[data-fieldtype=checkbox]');
		
	// Loop checkbox filters
	[...checkboxFilters].forEach((filter, e) => {
		
		// Select All link
		let selectAll = filter.querySelector('.alm-filter--link[data-type=all]');
		if(!selectAll){
			return false;	
		}
		
		// Get all checkbox items
		let items = filter.querySelectorAll('.alm-filter--link[data-type=checkbox]');
		if(!items){
			return false;
		}
		
		// Filter unchecked items
		let unchecked = [...items].filter(function(item){
			return !item.classList.contains('active');
		});
		
		// If no items left unchecked, check All option
		if(unchecked.length < 1){
			selectAll.classList.add('active');
		}
		
	});
	
}
