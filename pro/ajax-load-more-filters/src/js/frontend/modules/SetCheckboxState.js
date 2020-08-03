/*
 * setCheckboxState
 * Set checked state of checkbox
 *
 * @param {Array} array Array of selected filter values
 * @param {HTMLElement} setCheckboxState The current checkbox in the loop
 * @since 1.0
 */
let setCheckboxState = (array, checkbox) => {
	let chkVal = checkbox.dataset.value;
	let isStarRating = checkbox.classList.contains("field-starrating");

	// If checkbox value is found in array set as .active
	if (array.indexOf(chkVal) > -1) {
		checkbox.classList.add("active");
		checkbox.setAttribute("aria-checked", true);
	} else {
		// Not found (uncheck)
		checkbox.classList.remove("active");
		checkbox.setAttribute("aria-checked", false);
	}
};
export default setCheckboxState;
