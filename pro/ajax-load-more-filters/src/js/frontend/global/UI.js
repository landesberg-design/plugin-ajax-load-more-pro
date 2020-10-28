/*
 * Expand/Collapse filters.
 *
 * @since 1.10.1
 */
export function uiToggle(elements) {
	if (!elements) {
		return false;
	}
	[...elements].forEach((item, e) => {
		item.addEventListener("click", function (e) {
			toggleFilter(this);
		});
		// Return/Enter.
		item.addEventListener("keyup", function (e) {
			// Number 13 is the "Enter" key on the keyboard
			if (e.keyCode === 13) {
				// Cancel the default action, if needed
				e.preventDefault();
				// Trigger the button element with a click
				toggleFilter(e.target);
			}
		});
	});
}

/**
 * Toggle a filter
 *
 * @param {*} el
 * @since 1.10.1
 */
const toggleFilter = (el) => {
	if (!el) {
		return false;
	}
	const parent = el.parentNode.parentNode;
	const target = parent.querySelector(".alm-filter--inner");
	if (!target) {
		return false;
	}
	if (target && el.getAttribute("aria-expanded") === "true") {
		target.style.display = "none";
		target.setAttribute("aria-hidden", true);
		el.setAttribute("aria-expanded", false);
	} else {
		target.style.display = "block";
		target.setAttribute("aria-hidden", false);
		el.setAttribute("aria-expanded", true);
	}
};
