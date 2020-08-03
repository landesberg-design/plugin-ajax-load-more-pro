/*
 * clearInputs
 * Clear the input field
 *
 * @param inputs array   Array of inputs
 * @since 1.0
 */

let clearInputs = (inputs) => {
	[...inputs].forEach((input) => {
		input.classList.remove("active");
		input.classList.remove("highlight");
		input.setAttribute("aria-checked", false);
	});
};

export default clearInputs;
