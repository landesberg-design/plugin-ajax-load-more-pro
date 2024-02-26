var alm_elementor = {};
(function () { 
	alm_elementor.init = true;
	alm_elementor.paging = false;
	alm_elementor.previousUrl = window.location.href;
	alm_elementor.isAnimating = false;
	alm_elementor.defaultPage = 1;
	alm_elementor.fromPopstate = false;
	alm_elementor.HTMLHead = document.getElementsByTagName("head")[0].innerHTML;
	alm_elementor.timer = null;

	/**
	 * Triggered from core ajax-load-more.js
	 *
	 * @param {Object} alm
	 * @since 1.0
	 */
	window.almElementor = function (alm) {
		// Exit if not Elementor
		if (!alm.addons.elementor) {
			return false;
		}

		// First run only
		if (alm_elementor.init) {
			// Container
			alm_elementor.container = alm.addons.elementor_element;

			// Fwd/Back buttons
			alm_elementor.controls = alm.addons.elementor_controls;

			// Scrolltop
			alm_elementor.scrolltop = alm.addons.elementor_scrolltop;

			// First Element
			alm_elementor.first = alm_elementor.container.querySelector(
				".alm-elementor"
			);

			// Paging (Not yet supported)
			alm_elementor.paging = alm.addons.paging;
		}

		// Delay for effects
		setTimeout(function () {
			alm_elementor.init = false;
		}, 50);
	};

	/**
	 * Update browser URL on scroll
	 *
	 * @since 1.0
	 */
	alm_elementor.onScroll = function () {
		var scrollTop = window.scrollY;

		if (!alm_elementor.isAnimating && !alm_elementor.paging && !alm_elementor.init) {
			if (alm_elementor.timer) {
				window.clearTimeout(alm_elementor.timer);
			}

			alm_elementor.timer = window.setTimeout(function () {
				// Get container scroll position
				var fromTop = scrollTop + alm_elementor.scrolltop;
				var posts = document.querySelectorAll(".alm-elementor");
				var url = window.location.href;

				// Loop all posts
				var current = Array.prototype.filter.call(posts, function (n, i) {
					if (typeof ajaxloadmore.getOffset === "function") {
						var divOffset = ajaxloadmore.getOffset(n);
						if (divOffset.top < fromTop) {
							return n;
						}
					}
				});

				// Get the data attributes of the current element
				var currentPost = current[current.length - 1];
				var permalink = currentPost ? currentPost.dataset.url : "";
				var page = currentPost ? currentPost.dataset.page : "";
				var pageTitle = currentPost ? currentPost.dataset.pageTitle : "";

				// If first page
				if (page === undefined || page === "") {
					page = alm_elementor.first.dataset.page;
					permalink = alm_elementor.first.dataset.url;
					pageTitle = alm_elementor.first.dataset.pageTitle;
				}

				if (url !== permalink) {
					alm_elementor.setURL(page, permalink, pageTitle);
				}
			}, 15);
		}
	};
	window.addEventListener("touchstart", alm_elementor.onScroll);
	window.addEventListener("scroll", alm_elementor.onScroll);

	/**
	 * Set the browser URL to current permalink then scroll user to post
	 *
	 * @param string page
	 * @param string permalink
	 * @param string pageTitle
	 * @since 1.0
	 */
	alm_elementor.setURL = function (page, permalink, pageTitle) {
		var state = {
			page: page,
			permalink: permalink,
		};

		if (permalink !== alm_elementor.previousUrl && !alm_elementor.fromPopstate) {
			if (typeof window.history.pushState === "function") {
				if (alm_elementor.controls) {
					history.pushState(state, "", permalink);
				} else {
					history.replaceState(state, "", permalink);
				}

				// Trigger analytics.
				if (typeof ajaxloadmore.analytics === "function") { 
					ajaxloadmore.analytics("nextpage");
				}
			}

			// Update document title
			document.title = pageTitle;

			// Set Previous URL
			alm_elementor.previousUrl = permalink;
		}

		alm_elementor.fromPopstate = false;
	};

	/**
	 * Fires when users click back or forward browser buttons
	 *
	 * @param object event
	 * @since 1.0
	 */
	alm_elementor.onpopstate = function (event) {
		// if wrapper doesnt have data-elementor="posts" don't fire popstate
		var elmtr = document.querySelector(
			'.alm-listing[data-elementor="posts"]'
		);

		if (elmtr) {
			if (!alm_elementor.paging) {
				// Not Paging
				alm_elementor.fromPopstate = true;
				alm_elementor.getPageState(event.state);
			} else {
				// Paging
				if (
					typeof almSetCurrentPage === "function" &&
					typeof almGetParentContainer === "function" &&
					typeof almGetObj === "function"
				) {
					var current = event.state,
						obj = window.almGetParentContainer(),
						alm = window.almGetObj();

					// Check for null state value
					current =
						current === null ? alm_elementor.defaultPage : event.state.page;

					// Set popstate flag to true - don't trigger pushstate on url update
					alm_elementor.fromPopstate = true;

					if (typeof almSetCurrentPage === "function") {
						// Paging addon function
						window.almSetCurrentPage(current, obj, alm);
					}
				}
			}
		}
	};

	/**
	 * Window popstate eventlistener
	 *
	 * @since 1.0
	 */
	window.addEventListener("popstate", function (event) {
		if (typeof window.history.pushState == "function") {
			alm_elementor.onpopstate(event);
		}
	});

	/**
	 * Get the current page number
	 *
	 * @param object data
	 * @since 1.0
	 */
	alm_elementor.getPageState = function (data) {
		var page;
		if (data === null || data === "") {
			// Will be null with preloaded, so set -1
			page = -1;
		} else {
			page = data.page;
		}

		if (alm_elementor.container) {
			alm_elementor.scrollToPage(page);
		}
	};

	/**
	 * Scroll page to current element wrapper
	 *
	 * @param number page
	 * @since 1.0
	 */
	alm_elementor.scrollToPage = function (page) {
		// Current page
		page =
			page === undefined || page === "" || page === "-1" || page === -1
				? alm_elementor.first.dataset.page
				: page;

		if (alm_elementor.isAnimating) {
			// Exit if animating
			return false;
		}
		alm_elementor.isAnimating = true;

		var target = document.querySelector(
			'.alm-elementor[data-page="' + page + '"]'
		);

		if (target) {
			var offset =
				typeof ajaxloadmore.getOffset === "function"
					? ajaxloadmore.getOffset(target).top
					: target.offsetTop;
			var top = offset - alm_elementor.scrolltop + 5;

			// Move window to position
			setTimeout(function () {
				// Delay fixes browser popstate issues
				window.scrollTo(0, top);
				alm_elementor.fromPopstate = false;
			}, 5);
		}

		setTimeout(function () {
			alm_elementor.isAnimating = false;
		}, 250);
	};
})();
