import vars from "../global/Variables";
import analytics from "./Analytics";

let almFiltersTimer = "";

/**
 * almFiltersScroll
 * Update browser URL on scroll
 *
 * @parem {Object} almFilters
 * @parem {Object} almListing
 * @since 1.7
 */

let onScroll = function (almFilters, almListing) {
	if (vars.alm_filtering) {
		return false;
	}

	let almFiltersFirst = document.querySelector(
		'.alm-listing[data-filters="true"] .alm-filters:first-child'
	);

	if (!almFiltersFirst) {
		return false; // Exit if not found
	}

	// Window offset
	let scrollTop = window.pageYOffset;

	// Get ALM target
	let target = almFilters.dataset.target ? almFilters.dataset.target : "";

	// Get ALM instance
	let alm = document.querySelectorAll(
		'.ajax-load-more-wrap[data-id="' + target + '"] .alm-listing.alm-ajax'
	);

	// Get the Scroll Top
	let filters_scrolltop = almListing.dataset.filtersScrolltop;
	filters_scrolltop = filters_scrolltop ? parseInt(filters_scrolltop) : 30;

	// Nested
	let nested = alm[0].parentNode.dataset.nested === "true" ? true : false; // Nested ALM instance

	if (nested) {
		return false; // exit if nested
	}

	// Scroll Delay
	almFiltersTimer = window.setTimeout(function () {
		// Get container scroll position
		let fromTop = scrollTop + filters_scrolltop;
		let posts = document.querySelectorAll(".alm-filters");
		let url = window.location.href;

		// Loop all posts
		let current = Array.prototype.filter.call(posts, function (n, i) {
			if (typeof ajaxloadmore.getOffset === "function") {
				var divOffset = ajaxloadmore.getOffset(n);
				if (divOffset.top < fromTop) {
					return n;
				}
			}
		});

		// Get the data attributes of the current element
		let currentPost = current[current.length - 1];
		let permalink = currentPost ? currentPost.dataset.url : "";
		let page = currentPost ? currentPost.dataset.page : "";

		if (page === undefined || page === "") {
			page = almFiltersFirst.dataset.page;
			permalink = almFiltersFirst.dataset.url;
		}

		if (url !== permalink) {
			let state = {
				page: page,
				permalink: permalink,
			};

			// Update URL
			if (typeof window.history.pushState === "function" && !vars.isIE) {
				history.replaceState(state, window.location.title, permalink);
			}

			// Analytics
			if (alm[0].dataset.filtersAnalytics === "true") {
				analytics();
			}
		}
	}, 15);
};

export default onScroll;
