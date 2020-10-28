import * as a11yarrows from "a11yarrows";
import vars from "./global/Variables";
import triggerChange from "./modules/TriggerChange";
import setElementStates from "./modules/SetSelectedElements";
import parseQuerystring from "./modules/ParseQuerystring";
import buildDataObj from "./modules/BuildDataObj";
import dispatch from "./modules/Dispatch";
import { setDefaults, restoreDefault } from "./modules/Defaults";
import setCurrentFilters from "./modules/CurrentFilters";
import getKeyObject from "./modules/currentFilters/getKeyObject";
import getKeyElement from "./modules/currentFilters/getKeyElement";
import onScroll from "./modules/Scroll";
import { setDatePickers } from "./types/DatePicker";
import { setRangeSliders } from "./types/RangeSliders";
import starRating, { clearHighlightedStars } from "./types/StarRating";
import { toggleAll, toggleSelect } from "./helpers/toggle";
import { uiToggle } from "./global/UI";
require("./helpers/polyfills");

/*
 * Initiate the filter objects.
 *
 * @param almFilters element   The container element for the almFilters
 * @since 1.0
 */

let almFiltersInit = (almFilters) => {
	let style = almFilters.dataset.style; // change, button

	// Click/Change Event
	let almFiltersClick = (event) => {
		if (vars.alm_filtering) return false; // exit if animating/loading

		triggerChange(almFilters);
	};

	// Radio + Checkbox Click Event
	let almFilterChange = (event) => {
		if (vars.alm_filtering) return false; // exit if animating/loading

		event.preventDefault();

		let target = event.currentTarget;
		let fieldtype = target.dataset.type;
		let current_id = target.id;
		let parent = target.closest(".alm-filter"); // <div .alm-filter />// All items in filter
		let items = parent.querySelectorAll(".alm-filter--link"); // All items in filter

		if (fieldtype === "all") {
			if (target.classList.contains("active")) {
				// Uncheck All
				[...items].forEach((item, e) => {
					item.classList.remove("active");
					item.setAttribute("aria-checked", false);
				});
			} else {
				// Check All
				[...items].forEach((item, e) => {
					item.classList.add("active");
					item.setAttribute("aria-checked", true);
				});
			}
		} else {
			if (fieldtype === "radio" || fieldtype === "star_rating") {
				// Exit if active and preselected value set
				if (
					parent.classList.contains("alm-filter--preselected") &&
					target.classList.contains("active")
				) {
					event.preventDefault();
					return false;
				}

				// Loop all radios
				[...items].forEach((item, e) => {
					if (item.id !== current_id) {
						item.classList.remove("active");
						item.setAttribute("aria-checked", false);
					}
				});
			}

			// Set active state
			if (target.classList.contains("active")) {
				target.classList.remove("active");
				target.setAttribute("aria-checked", false);
			} else {
				target.classList.add("active");
				target.setAttribute("aria-checked", true);
			}

			// Check for `toggle All` button
			toggleAll(fieldtype, items, parent);
		}

		// Trigger Change Event
		if (style === "change") {
			triggerChange(almFilters);
		}
	};

	// Radio + Checkbox Event listeners
	let almFilterLinks = document.querySelectorAll(".alm-filter--link");
	if (almFilterLinks) {
		[...almFilterLinks].forEach((item, e) => {
			item.addEventListener("click", almFilterChange);
			item.addEventListener("keyup", function (event) {
				if (event.keyCode === 13 || event.keyCode === 32) {
					// Enter/return click || spacebar
					almFilterChange(event);
				}
			});
			item.addEventListener("keydown", function (event) {
				if (event.keyCode === 32) {
					//  Prevent the default scrollbar action of scrolling the page
					event.preventDefault();
					event.stopPropagation();
					return false;
				}
			});
		});
	}

	// Radio + Checkbox - a11yarrows controls
	let radioInputs = document.querySelectorAll(
		"div.alm-filter[data-fieldtype=radio]"
	);
	if (radioInputs) {
		[...radioInputs].forEach((item, e) => {
			let target = item.querySelector("ul");
			a11yarrows.init(target, {
				// options
				selector: ".alm-filter--link",
			});
		});
	}

	// Star Rating
	let starRatings = document.querySelectorAll(
		"div.alm-filter[data-fieldtype=star_rating]"
	);
	if (starRatings) {
		[...starRatings].forEach((rating, e) => {
			let stars = rating.querySelectorAll("li div.field-starrating");
			if (stars) {
				starRating(stars);
			}
		});
	}

	// Textfield Button Event listeners
	let almFiltertextButtons = document.querySelectorAll(
		".alm-filter--text-wrap.has-button button"
	);
	if (almFiltertextButtons) {
		[...almFiltertextButtons].forEach((button, e) => {
			button.addEventListener("click", almFiltersClick);
		});
	}

	// Change Event (Select)
	if (style === "change") {
		// Loop all items and add the event listener
		let almFilterItems = document.querySelectorAll(".alm-filter--item");
		if (almFilterItems) {
			[...almFilterItems].forEach((item, e) => {
				item.addEventListener("change", almFiltersClick);
			});
		}
	}

	// Button
	if (style === "button") {
		let almFilterButton = almFilters.querySelector(".alm-filters--button");
		if (almFilterButton) {
			almFilterButton.addEventListener("click", almFiltersClick);
		}
	}

	// Attach enter click listener for textfields
	let almFilterTextfields = document.querySelectorAll(
		".alm-filter--textfield"
	);
	if (almFilterTextfields) {
		[...almFilterTextfields].forEach((item, e) => {
			item.addEventListener("keyup", function (e) {
				if (e.keyCode === 13) {
					// Enter/return click
					almFiltersClick();
				}
			});
		});
	}

	// Init Datepickers
	let datePickers = document.querySelectorAll("input.alm-flatpickr");
	if (datePickers) {
		setDatePickers(almFilters.dataset.id, datePickers);
	}

	// Init rangeSliders
	let rangeSliders = document.querySelectorAll("div.alm-range-slider");
	if (rangeSliders) {
		setRangeSliders(almFilters.dataset.id, rangeSliders, style);
	}

	// Toggle Filter Event Handlers
	let filterToggles = document.querySelectorAll(
		"div.alm-filter--title .alm-filter--toggle"
	);
	if (filterToggles) {
		uiToggle(filterToggles);
	}

	// Set currently selected filters
	setCurrentFilters(window.location.search);
};

/**
 * Trigger click event on selected filter.
 *
 * @param element   The clicked element
 * @since 1.0
 */

window.removeSelectedFilter = (element) => {
	let almFilters = vars.almFilters;
	let key = element.dataset.key;
	let value = element.dataset.value;
	let obj = getKeyObject(key, value); // Return the el container (.alm-filter)
	let el = getKeyElement(obj.target, value, obj.fieldType);

	switch (obj.fieldType) {
		case "select":
			// if has a selected value
			el.value = obj.target.dataset.selectedValue
				? obj.target.dataset.selectedValue
				: "#";
			triggerChange(almFilters);
			break;

		case "text":
			el.value = "";
			triggerChange(almFilters);
			break;

		default:
			el.click();
			if (almFilters.dataset.style === "button") {
				triggerChange(almFilters);
			}
			break;
	}
};

/**
 * Trigger click event on selected filter when enter clicked.
 *
 * @param element   The clicked element
 * @since 1.0
 */

window.removeSelectedFilterEnter = (event) => {
	if (!event) {
		return false;
	}

	if (event.keyCode === 13) {
		// Enter/return click

		let element = event.target;

		let almFilters = vars.almFilters;
		let key = element.dataset.key;
		let value = element.dataset.value;
		let obj = getKeyObject(key, value); // Return the el container (.alm-filter)
		let el = getKeyElement(obj.target, value, obj.fieldType);

		switch (obj.fieldType) {
			case "select":
				// if has a selected value
				el.value = obj.target.dataset.selectedValue
					? obj.target.dataset.selectedValue
					: "#";
				triggerChange(almFilters);
				break;

			case "text":
				el.value = "";
				triggerChange(almFilters);
				break;

			default:
				el.click();
				if (almFilters.dataset.style === "button") {
					triggerChange(almFilters);
				}
				break;
		}
	}
};

/**
 * start
 * Initiate ALM filters
 * Public JS function
 *
 * @since 1.7.5
 */

let start = function () {
	let almFilters = document.querySelector(".alm-filters-container");

	if (!almFilters) {
		return false;
	}

	almFiltersInit(almFilters);

	let almListing = document.querySelector(".alm-listing");

	// Set scroll & touch listeners if required
	// Exit if paging addon active.
	// Do not want scrolling to update the URL

	if (almListing && almListing.dataset.paging !== "true") {
		let doScroll = almListing.dataset.filtersScroll;
		if (doScroll) {
			window.addEventListener("touchstart", function () {
				onScroll(almFilters, almListing);
			});
			window.addEventListener("scroll", function () {
				onScroll(almFilters, almListing);
			});
		}
	}

	toggleSelect(almFilters);
};
export { start };

// Init Filters on page load
window.addEventListener("load", function () {
	if (vars.almFilters) {
		start(vars.almFilters);
	}
});

/**
 * Fires when users click back or forward browser buttons.
 */
window.addEventListener("popstate", function (event) {
	// Safari popstate fix
	// Safari triggers a popstate anytime the back button is pressed.
	// This flag prevents execution from articles or other pages.
	if (!vars.pushstate) {
		return false; // Exit if pushstate was never initiated
	}

	let almFilters =
		vars.almFilters || document.querySelector(".alm-filters-container");

	if (!almFilters) {
		// If element exists
		return false;
	}

	let querystring = window.location.search; // get Querystring
	let url = event.state ? event.state.permalink : querystring; // Get state or querystring
	url = url.replace("?", ""); // remove `?` param

	// Empty URL or empty querystring
	if (url === "" || url === null || querystring === "") {
		almFiltersClear(false);
	} else {
		let urlArray = parseQuerystring(url); // [helpers/helpers.js]
		setElementStates(urlArray); // [modules/setSelectedElements.js]
	}
});

/**
 * Created paged URL parameters. Triggered from Paging add-on.
 *
 * @param alm element   Core ALM object
 * @param init boolen
 * @since 1.0
 */

window.almFiltersPaged = (alm, init = true) => {
	let nested = alm.main.dataset.nested === "true" ? true : false; // Nested ALM instance

	// Exit if finished
	if (alm.finished || nested) {
		return false;
	}

	let page = alm.page + 1;
	page = alm.preloaded === "true" ? page + 1 : page; // Add 1 for preloaded

	let querystring = window.location.search.substring(1);
	let obj = {};

	if (querystring) {
		obj = parseQuerystring(querystring);
		if (Object.keys(obj).length) {
			obj.pg = page;
		}
	} else {
		// Empty querystring
		obj.pg = page;
	}

	let url = "?";
	let count = 0;

	// Loop the object and build the querystring
	Object.keys(obj).forEach(function (key) {
		count++;
		if (count > 1) {
			url += "&";
		}
		url += `${key}=${obj[key]}`;
	});

	// Confirm URL
	url = page == 1 ? url.replace(/\A?pg=[^&]+&*/g, "") : url; // Regex to check for ?pg=1 parmaeters
	url =
		url[url.length - 1] === "?" || url[url.length - 1] === "&"
			? url.substring(0, url.length - 1)
			: url; // Remove orphan ? || & querystring params
	url = url === "" ? alm.canonical_url : url; // If empty, set to alm.canonical_url

	// Set the URL - use replaceState to prevent bck/fwd interactions
	if (alm.addons.filters_paging) {
		let state = { permalink: url };
		history.replaceState(state, null, url);
	}

	// Scroll User to top of listing
	let scroll = alm.addons.filters_scroll;
	if (typeof ajaxloadmore.almScroll === "function" && scroll && !init) {
		let target = alm.listing;
		if (!target) {
			return false;
		}
		let offset =
			typeof ajaxloadmore.getOffset === "function"
				? ajaxloadmore.getOffset(target).top
				: target.offsetTop;
		let scrolltop = alm.addons.filters_scrolltop
			? parseInt(alm.addons.filters_scrolltop)
			: 30;
		let top = offset - scrolltop + 1;
		ajaxloadmore.almScroll(top);
	}
};

/**
 * Reset all filters back to default state.
 * Public JS function
 *
 * @param reset boolean
 * @since 1.7.5
 */

let reset = function (reset = true) {
	let almFilters =
		vars.almFilters || document.querySelector(".alm-filters-container");
	if (!almFilters) {
		return false;
	}

	let target = almFilters.dataset.target; // Get target
	let filters = almFilters.querySelectorAll(".alm-filter"); // Get all filters
	let data = {}; // Define data object

	// Loop all filters
	[...filters].forEach((filter, e) => {
		restoreDefault(filter);
		data = buildDataObj(filter, data);
	});

	if (reset) {
		// Trigger change events
		triggerChange(vars.almFilters);
	} else {
		// Dispatch filter change
		dispatch(target, data);
	}
};
export { reset };

/**
 * Legacy public function.
 *
 * @deprecated 1.7.5
 * @since 1.0
 */
window.almFiltersClear = function (reset = true) {
	clearHighlightedStars();
	almfilters.reset(reset);
};

/**
 * resetFilter
 * Reset an individual filter group `almfilters.restoreDefault()`
 * Public JS function
 *
 * @param filter HTML element
 * @since 1.7.5
 */

let resetFilter = function (filter) {
	if (filter) {
		restoreDefault(filter);
	}
};
export { resetFilter };

/**
 * Scroll user to page on page load.
 * Fires from core Ajax Load More [core/src/js/ajax-load-more.js]
 *
 * @param {number} page
 * @since 1.7
 */

window.almFiltersOnload = function (alm = null) {
	if (!alm) {
		return false;
	}
	// Delay to give posts time to load
	setTimeout(function () {
		let page = parseInt(alm.page) + 1;
		if (page > 1) {
			let target = alm.listing.querySelector(
				'.alm-filters[data-page="' + page + '"]'
			);
			if (target) {
				let offset =
					typeof ajaxloadmore.getOffset === "function"
						? ajaxloadmore.getOffset(target).top
						: target.offsetTop;
				let scrolltop = alm.addons.filters_scrolltop
					? parseInt(alm.addons.filters_scrolltop)
					: 30;
				let top = offset - scrolltop + 1;
				window.scrollTo(0, top);
			}
		}
	}, 150);
};

/**
 * Filters Complete function.
 * Fires from core Ajax Load More [core/src/js/modules/filtering.js]
 *
 * @param el element   The alm element
 * @since 1.0
 */

window.almFiltersAddonComplete = function (el = null) {
	let target = el.querySelector(".alm-listing");
	let scroll = false;
	let scrolltop = 30;

	if (target) {
		scroll = target.dataset.filtersScroll === "true" ? true : false;
		scrolltop = target.dataset.filtersScrolltop
			? parseInt(target.dataset.filtersScrolltop)
			: 30;
	}

	setTimeout(function () {
		// Delay re-initialization

		let almFilters =
			vars.almFilters || document.querySelector(".alm-filters-container");

		if (!almFilters) {
			return false;
		}

		vars.alm_filtering = false;
		almFilters.classList.remove("filtering");

		/*
		 * almFiltersComplete
		 * Callback function dispatched after the filters have completed their magic
		 *
		 */
		if (typeof window.almFiltersComplete === "function") {
			window.almFiltersComplete();
		}

		// Scroll User to top of listing
		if (typeof ajaxloadmore.almScroll === "function" && scroll) {
			if (!el) {
				return false;
			}

			let offset =
				typeof ajaxloadmore.getOffset === "function"
					? ajaxloadmore.getOffset(el).top
					: el.offsetTop;
			let top = offset - scrolltop + 1;
			ajaxloadmore.almScroll(top);
		}
	}, 100);
};
