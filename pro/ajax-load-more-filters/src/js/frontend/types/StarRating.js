/*
 * StarRating
 * Initiate the StarRating field type.
 *
 * @param {*} stars
 * @since 1.10.0
 */
const starRating = (stars = "") => {
	if (!stars) {
		return false;
	}

	// Loop Stars, Add Click, Keyup event Event
	for (let i = 0; i < stars.length; i++) {
		stars[i].addEventListener("mouseover", function () {
			starHover(i, stars);
		});
		stars[i].addEventListener("mouseout", function () {
			starOut(stars);
		});
		stars[i].addEventListener("click", function () {
			starClick(this, stars);
		});
		stars[i].addEventListener("keyup", function (event) {
			if (event.keyCode === 13 || event.keyCode === 32) {
				// Enter/return click || spacebar
				starClick(this, stars);
			}
		});
	}
};
export default starRating;

/**
 * Star click/Keyup event handler
 * @param {*} el
 * @param {*} stars
 */
function starClick(el, stars) {
	let value = el.dataset.value;
	let feedback = el.parentNode.parentNode.querySelector(".alm-star--feedback");

	clearRatingClass(stars);

	if (el.classList.contains("active")) {
		for (let j = value - 1; j >= 0; j--) {
			stars[j].classList.toggle("highlight");
		}
		if (feedback) {
			setFeedback(feedback, el.dataset.text);
		}
	} else {
		if (feedback) {
			setFeedback(feedback, "");
		}
	}
}

function setFeedback(el, value) {
	el.innerHTML = value;
}

/**
 * Star mouseover event handler
 * @param {*} index
 * @param {*} stars
 */
function starHover(index, stars) {
	for (let i = 0; i < stars.length; i++) {
		if (i <= index) {
			stars[i].classList.add("hover");
		} else {
			stars[i].classList.remove("hover");
			stars[i].classList.add("none");
		}
	}
}

/**
 * Star mouseout event handler
 * @param {*} stars
 */
function starOut(stars) {
	for (let i = 0; i < stars.length; i++) {
		stars[i].classList.remove("hover");
		stars[i].classList.remove("none");
	}
}

/**
 * Clear the stars `highlight` class
 * @param {*} stars
 */
function clearRatingClass(stars) {
	if (!stars) {
		return false;
	}

	for (let i = 0; i < stars.length; i++) {
		stars[i].classList.remove("highlight");
	}
}

/**
 * Set the highlighed stars when user initiates a popstate
 * @param {*} filter
 * @param {*} stars
 */
export function setHighlightedStars(filter, stars) {
	// Get active star
	let active = filter.querySelector("div.active");
	if (!active) {
		return false;
	}

	let value = active.dataset.value;
	clearRatingClass(stars);
	setFeedback(
		filter.querySelector(".alm-star--feedback"),
		active.dataset.text
	);

	/// Add highlight class
	for (let j = value - 1; j >= 0; j--) {
		stars[j].classList.toggle("highlight");
	}
}

/**
 * Clear all highlighted star filters
 */
export function clearHighlightedStars() {
	// Get all links
	let stars = document.querySelectorAll(".alm-filter--link.field-starrating");
	if (stars) {
		[...stars].forEach((star) => {
			star.classList.remove("active");
			star.classList.remove("highlight");
			star.classList.remove("hover");
		});
	}
	/// Get all feedback
	let feedback = document.querySelectorAll(".alm-star--feedback");
	if (stars) {
		[...feedback].forEach((el) => {
			el.innerHTML = "";
		});
	}
}
