import parseQuerystring from './ParseQuerystring';
import getKeyValue from '../modules/currentFilters/getKeyValue';

/*
 * setCurrentFilters
 * Set the currently selected filters into list format
 *
 * @param url string   The current url to build the selected options.
 * @return null
 *
 * @since 1.4
 */
let setCurrentFilters = (url) => {
	let selected_filters_wrap = document.getElementById('alm-selected-filters');
	if(selected_filters_wrap){
		if(url && url.indexOf('?') !== -1){ // confirm URL contains a querystring
			let selected_filters = currentFilters(url);
			if(selected_filters){
				// Set selected filters
				selected_filters_wrap.innerHTML = selected_filters;
			} else {
				// Clear filters
				selected_filters_wrap.innerHTML = '';
			}
		} else{
			// Clear filters
			selected_filters_wrap.innerHTML = '';
		}

		/*
	    * almFiltersChange
	    * Callback function is dispatched when a filter change event is triggered.
	    *
	    */
		if (typeof window.almFiltersSelected === "function") {
			window.almFiltersSelected(selected_filters_wrap);
		}

		// Append total filters as a data att
		selected_filters_wrap.dataset.total = selected_filters_wrap.querySelectorAll('li').length;
	}
}
export default setCurrentFilters;



/*
 * currentFilters
 * Set the current filters into list
 *
 * @param url string   The current url to build the selected options.
 * @return new_url object
 *
 * @since 1.4
 */

function currentFilters(url) {
	url = url.replace('?', ''); // remove '?' from URL
	let new_url = parseQuerystring(url); // parse URL into object

	return buildSelections(new_url);
}



/*
 * buildSelections
 * Set the current filters into list
 *
 * @param obj object   The current url object for parsing
 * @return items string
 *
 * @since 1.4
 */
function buildSelections(obj) {
	
	let items = '';
	for (var key in obj) {
		if (obj.hasOwnProperty(key)) {	
					
			if(obj[key] === undefined || obj[key] === ''){
				continue; // Exit current iteration if empty
			}
			
			// Split multiple key values
			var values = obj[key].split('+');
			for(var n = 0; n < values.length; n++){
				
				let value = getKeyValue(key , values[n]); // Get text of the filter, not the slug
				if(value){ // Confirm value exits
					items += '<li>';
						items += '<a href="javascript:void(0);" onClick="window.removeSelectedFilter(this);" data-key="' + key + '" data-value="' +  values[n] + '">';
							items += value;
						items += '</a>';
					items += '</li>';
				}

			}

		}
	}

	return items;
}
