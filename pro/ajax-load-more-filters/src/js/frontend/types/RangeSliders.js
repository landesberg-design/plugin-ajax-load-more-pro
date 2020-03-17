import noUiSlider from "nouislider";

/*
 * rangeSliders
 * Initiate the Flatpickr date pickers.
 
 * @param filter_id  ID of the filter instance
 * @param randeSliders  the items to convert to a range
 * @since 1.8
 */ 
export function setRangeSliders(filter_id, rangeSliders, style = 'button') {   
   
   if(!rangeSliders){ 
      return false;
   }
   
   // Loop all range sliders
	[...rangeSliders].forEach((slider, e) => {
   	
   	let target = slider.querySelector('.alm-range-slider--target');
   	let label = slider.querySelector('.alm-range-slider--label');
   	let input = slider.parentNode.querySelector('input');
   	
   	let min = slider.dataset.min ? parseInt(slider.dataset.min) : 0;
   	let max = slider.dataset.max ? parseInt(slider.dataset.max) : 100;     	
   	let start = slider.dataset.start ? parseInt(slider.dataset.start) : min;
   	let end = slider.dataset.end ? parseInt(slider.dataset.end) : max;     	
   	let steps = slider.dataset.steps ? parseInt(slider.dataset.steps) : 1; 
   	let display_label = slider.dataset.label ? slider.dataset.label : '{start} - {end}'; 
   	let orientation = slider.dataset.orientation ? slider.dataset.orientation : 'horizontal';  
   	let decimals = slider.dataset.decimals ? slider.dataset.decimals : 'true'; 
   	decimals = (decimals === 'true') ? true : false;  	   	
   	
   	// Range Slider Options
   	let options = {
			start: [start, end],
			step: steps,
			connect: true,
			behaviour: 'tap',
			orientation: orientation,
			range: {
				'min': min,
				'max': max
			}
   	}   	
		
		// Custom config options		
		let opt_var = (filter_id !== '') ? 'alm_nouislider_opts_' + filter_id : 'alm_nouislider_opts'; // Dynamic Variable Name		
		let alm_nouislider_opts = window[opt_var]; // Get window variable
      if(alm_nouislider_opts){ 
         Object.keys(alm_nouislider_opts).forEach(function(key) {	// Loop object	to create key:prop			
				options[key] = alm_nouislider_opts[key];					
			});
		}
   	
   	// Initiate noUiSlider
   	noUiSlider.create(target, options);
      
      // Update
      target.noUiSlider.on('update', function () { 
	      let value = this.get();
	      label.innerHTML = parseRangeValue(value, min, max, display_label, decimals);	      
	   });
	      
	   // Change
      target.noUiSlider.on('change', function () {
	      	      
	      let value = this.get();
			label.innerHTML = parseRangeValue(value, min, max, display_label, decimals);
			input.value = value;
			
			// If style is change, submit the form on change
			if(style === 'change'){
				let ev = document.createEvent('Event');
				ev.initEvent('keyup');
				ev.which = ev.keyCode = 13;
				input.dispatchEvent(ev);
			}
      });

		
	});
	
}


// Parse the display label
function parseRangeValue(value, min, max, display_label, decimals){
	if(!value && value.length !== 2){
		return false;
	}
	
	let returnVal = display_label;	
	let start_val = value[0] ? value[0] : min;
	let end_val = value[1] ? value[1] : max;
	
	start_val = (!decimals) ? Math.round(start_val) : start_val;
	end_val = (!decimals) ? Math.round(end_val) : end_val;
	
   returnVal = returnVal.replace('{start}', `<span class="alm-range-start">${start_val}</span>`);
   returnVal = returnVal.replace('{end}', `<span class="alm-range-end">${end_val}</span>`);
	
	return returnVal;
}