/*
 * clearInputs
 * Clear the input field
 *
 * @param inputs array   Array of inputs
 * @since 1.0
 */
 
let clearInputs = (inputs) => {
   [...inputs].forEach((input) => {
      input.classList.remove('active');
   });
};

export default clearInputs;