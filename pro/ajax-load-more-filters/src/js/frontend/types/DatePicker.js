import flatpickr from "flatpickr";
import FlatpickrLanguages from "flatpickr/dist/l10n";


/*
 * SetDatePickers
 * Initiate the Flatpickr date pickers.
 
 * @param filter_id  ID of the filter instance
 * @param datePickers  the items to convert to a datepicker
 * @since 1.8
 */ 
export function setDatePickers(filter_id, datePickers) {
   
   if(!datePickers){ 
      return false;
   }
   
   // Loop all datepickers
	[...datePickers].forEach((datePicker, e) => {
   	let mode = (datePicker.dataset.displayMode) ? datePicker.dataset.displayMode : 'single';
		let display_format = (datePicker.dataset.displayFormat) ? datePicker.dataset.displayFormat : 'Y-m-d';
		let locale = (datePicker.dataset.dateLocale) ? datePicker.dataset.dateLocale : 'en';
		
		let options = {
   		dateFormat: display_format,
   		mode : mode,
   		locale : FlatpickrLanguages[locale] 		
		}
		
		// Get custom config options
		
		let opt_var = (filter_id !== '') ? 'alm_flatpickr_opts_' + filter_id : 'alm_flatpickr_opts'; // Dynamic Variable Name		
		let alm_flatpickr_opts = window[opt_var]; // Get window variable
		
      if(alm_flatpickr_opts){ 
         Object.keys(alm_flatpickr_opts).forEach(function(key) {	// Loop object	to create key:prop			
				options[key] = alm_flatpickr_opts[key];					
			});
		}
		
		
		// Normalize rangeSeparator for `Range` mode 
		options.locale.rangeSeparator = ' | ';
		
		
		// Init Flatpickr
		let fp = flatpickr(datePicker, options);
		
	});
}