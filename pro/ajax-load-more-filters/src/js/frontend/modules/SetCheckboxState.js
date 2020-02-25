/*
 * setCheckboxState
 * Set checked state of checkbox
 *
 * @param array array   Array of selected filter values
 * @param setCheckboxState element   The current checkbox in the loop
 * @since 1.0
 */
 // 
let setCheckboxState = (array, checkbox) => {
	
	console.log(checkbox);
	
   let chkVal = checkbox.dataset.value;
   
   // If checkbox value is found in array set as .active
   if(array.indexOf(chkVal) > -1){ 
      checkbox.classList.add('active'); 
   } else { 
	   // Not found (uncheck)
      checkbox.classList.remove('active'); 
   }
   
};

export default setCheckboxState;