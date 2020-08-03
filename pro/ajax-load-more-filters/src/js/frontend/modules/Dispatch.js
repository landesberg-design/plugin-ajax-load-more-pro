import analytics from './Analytics';
import setCurrentFilters from './CurrentFilters';


/*
 * dispatch
 * Send the final output to the almFilter() function in core ALM
 *
 * @param target string   The target ALM container
 * @param data object   The data obj for the filter
 * @param url string   The current URL
 * @return null   ddispatch global almFilter() function call
 *
 * @since 1.0
 */

let dispatch = (target, data, url) => {

	// Get the target .ajax-load-more element
	let alm = document.querySelectorAll('.ajax-load-more-wrap[data-id="'+ target +'"] .alm-listing.alm-ajax');

	if(typeof(alm) != 'undefined' && alm != null){

		alm = alm[0];
		let transition = (alm.dataset.transition == null) ? 'fade' : alm.dataset.transition;
		let speed = (alm.dataset.speed == null) ? '250' : alm.dataset.speed;

		// Analytics
		if(alm.dataset.filtersAnalytics === 'true'){
   		analytics();
      }

		// Debug Info
		if(alm.dataset.filtersDebug === 'true'){
			console.log('ALM Filters Debug:', data);
		}
		
		// Dispatch filters to core ALM
		if(typeof ajaxloadmore.filter === 'function'){
			ajaxloadmore.filter(transition, speed, data);
		}		
		
		// Set currently selected filters
		setCurrentFilters(url);		

	}

};

export default dispatch;
