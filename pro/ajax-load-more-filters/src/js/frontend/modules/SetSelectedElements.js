import vars from "../global/Variables";
import triggerChange from "./TriggerChange";
import setCheckboxState from "./SetCheckboxState";
import clearInputs from "./ClearInputs";
import { setHighlightedStars } from "../types/StarRating";

/*
 * setElementStates
 * Get the selected terms and build the data obj
 *
 * @param filter element   The container element for the current filter set
 * @param data object   The data obj for the filter
 * @return data
 *
 * @since 1.0
 */

let setElementStates = (urlArray) => {
	let almFilters =
		vars.almFilters || document.querySelector(".alm-filters-container");
	if (!almFilters) {
		return false;
	}

	vars.alm_filtering_popstate = true;
	let filters = almFilters.querySelectorAll(".alm-filter");

	// Loop all filters
	[...filters].forEach((filter, e) => {
		let fieldtype = filter.dataset.fieldtype;
		let key = filter.dataset.key;
		key = key === "taxonomy" ? filter.dataset.taxonomy : key; // If key is taxonomy, convert key to taxonomy slug
		key = key === "meta" ? filter.dataset.metaKey : key; // If key is meta, convert key to meta key

		switch (fieldtype) {
			case "checkbox": // Checkbox
				let checkboxes = filter.querySelectorAll("div.field-checkbox"); // All checkboxes

				// If key matches URL key
				if (urlArray.hasOwnProperty(key)) {
					let valueArray = urlArray[key].split("+");
					[...checkboxes].forEach((checkbox) => {
						setCheckboxState(valueArray, checkbox);
					});
				} else {
					clearInputs(checkboxes); // Clear all
				}

				break;

			case "radio": // Radios
				let radios = filter.querySelectorAll("div.field-radio"); // All radios

				if (urlArray.hasOwnProperty(key)) {
					let valueArray = urlArray[key].split("+");
					[...radios].forEach((radio) => {
						setCheckboxState(valueArray, radio);
					});
					if (isStarRating) {
						clearRatingClass(radios);
					}
				} else {
					clearInputs(radios); // Clear all
				}

				break;

			case "star_rating": // Star Rating... Duplicate of Radio func
				let stars = filter.querySelectorAll("div.field-starrating"); // All radios
				let feedback = filter.querySelector(".alm-star--feedback");

				if (urlArray.hasOwnProperty(key)) {
					let valueArray = urlArray[key].split("+");
					[...stars].forEach((star) => {
						setCheckboxState(valueArray, star);
					});
					setHighlightedStars(filter, stars);
				} else {
					clearInputs(stars); // Clear all
				}

				break;

			case "select": // Select
				let select = filter.querySelector("select");
				if (urlArray.hasOwnProperty(key)) {
					select.value = urlArray[key];
				} else {
					select.value = "#";
				}

				break;

			case "select_multiple": // Select
				let select_multiple = filter.querySelector("select");
				if (urlArray.hasOwnProperty(key)) {
					select_multiple.value = urlArray[key];
				} else {
					select_multiple.value = "#";
				}

				break;

			case "date_picker": // Select
				let datepicker = filter.querySelector(".flatpickr-input");
				if (urlArray.hasOwnProperty(key)) {
					// Replace `+` with ` | ` for range mode
					let value = urlArray[key].replace("+", " | ");
					datepicker.value = value;
				} else {
					datepicker.value = "";
				}

				break;

			default:
				// Textfield

				let textfield = filter.querySelector("input[type=text]");
				if (urlArray.hasOwnProperty(key)) {
					textfield.value = urlArray[key];
				} else {
					textfield.value = "";
				}

				break;
		}
	});

	triggerChange(almFilters);
};
export default setElementStates;
