import { polyfill } from 'es6-promise'; 
polyfill();
import axios from 'axios'; 

let save = (options, filters, target, baseurl) => {
	
	let spinner = document.querySelector('.saving-filter');
	spinner.classList.add('saving');
	
   // REST API URL
   let url = alm_filters_localize.root + 'alm-filters/save/';
      
	axios.post(url, {
		'options': JSON.stringify(options),
		'filters': JSON.stringify(filters)
	})
	.then(function (response) {
		
		if(response.data.success){
			
			setTimeout(function(){				
				spinner.classList.add('saved');    	 
	         spinner.classList.remove('saving');     
	         target.innerText = alm_filters_localize.saved_filter;      	            
            setTimeout(function(){		         
	            window.location = `${baseurl}&filter_updated`;
	         }, 500);
			}, 1000);	

		} else {
   		
   		console.warn(response.data.msg);
   		setTimeout(function(){
   			spinner.classList.remove('saving');
   			target.disabled = false;
			}, 500);
			
		} 
		
	})
	.catch(function (error) {
		console.log(error);
	});
	
}

export default save;