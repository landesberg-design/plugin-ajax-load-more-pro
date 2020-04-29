import axios from 'axios';

let renderfilter = (data) => {
	
   // REST API URL
   let url = alm_filters_localize.root + 'alm-filters/renderfilter/';
      
	axios.post(url, {
		data: data
	})
	.then(function (response) {
		
		if(response.data.success){
						
			let pre = document.querySelector('.output');			
			let code = response.data.code;
			
         code = code.replace(/[:]/g, " => ");
         code = code.replace(/[[]/g, "array(");
         code = code.replace(/[{]/g, "array(");
         code = code.replace(/[}]/g, ")");
         code = code.replace(/[]]/g, ")");
         code = code.replace(/[%5D]/g, ")");
         code = code.replace(']', ")");// One more, not sure why
         code = code.replace(']', ")");// One more, not sure why
         
         let output = `$filter_array = ${code};`;   
         output += "<br/><br/>echo alm_filters($filter_array, '{your_alm_id}');";
         
         // Set the value 
			pre.innerHTML = output;			


		} else {
   		
   		console.warn(response.data.msg);
			
		} 
		
	})
	.catch(function (error) {
		console.log(error);
	});
	
}

export default renderfilter;