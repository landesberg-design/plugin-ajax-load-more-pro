/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/alm-paging.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/alm-paging.js":
/*!******************************!*\
  !*** ./src/js/alm-paging.js ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _setHref = __webpack_require__(/*! ./functions/setHref */ "./src/js/functions/setHref.js");

var _setHref2 = _interopRequireDefault(_setHref);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var almPaging = {}; /**
                     * Ajax Load More - Paging
                     * connekthq.com/plugins/ajax-load-more/paging/
                     * Copyright Connekt Media - http://connekthq.com
                     * Author: Darren Cooney
                     * Twitter: @KaptonKaos, @connekthq
                     */

(function () {
	"use strict";

	almPaging.init = true;
	almPaging.height = 0;
	almPaging.urlType = "default"; // default `/3` or querystring `?pg=3`
	var init = true;

	/**
  * Build the pagination for the menu.
  *
  * @param {string} total             The total number of pages.
  * @param {object} alm               The ALM object.
  * @param {boolean} dispatchCallback Whether to dispatch the callback.
  */
	window.almBuildPagination = function (total, alm) {
		var dispatchCallback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;

		var posts_per_page = alm.orginal_posts_per_page;
		var start = parseInt(alm.start_page);
		var obj = alm.listing;
		var alm_paging_controls = alm.addons.paging_controls;

		almPaging.urlType = alm.content.dataset.nextpageBreak === "true" ? "querystring" : "default";

		var show_at_most = alm.addons.paging_show_at_most || 6;

		// Next Page, set posts_per_page to 1
		if (alm.addons.nextpage) {
			start = alm.addons.nextpage_startpage;
			posts_per_page = 1;
		}

		// Filters, set start position
		if (alm.addons.filters) {
			start = parseInt(alm.addons.filters_startpage);
			start = start == 0 ? 1 : start;
		}

		var pages = Math.ceil(parseInt(total) / posts_per_page);
		var showFirstLastBtns = pages > parseInt(show_at_most);

		var ul = document.createElement("ul");
		ul.setAttribute("class", "alm-paging " + alm.addons.paging_classes);
		ul.setAttribute("data-current-page", start);
		ul.setAttribute("data-total-pages", pages);
		ul.style.opacity = "0";

		if (pages < 2) {
			// Zero pages. Hide navigation.
			ul.classList.add("empty");
			alm.btnWrap[0].innerHTML = "";
		} else {
			// First / Previous
			if (alm_paging_controls) {
				// First Button
				var firstBtnLabel = alm.addons.paging_first_label || "";
				if (firstBtnLabel !== "" && showFirstLastBtns) {
					var first = document.createElement("li");
					first.setAttribute("class", "first");

					var firstLink = document.createElement("a");
					firstLink.dataset.page = 1;
					firstLink.href = (0, _setHref2.default)(alm, 1, almPaging.urlType);
					firstLink.role = "button";

					var firstSpan = document.createElement("span");
					firstSpan.innerHTML = firstBtnLabel;

					firstLink.appendChild(firstSpan);
					first.appendChild(firstLink);
					ul.appendChild(first);

					// Next Click Event
					firstLink.addEventListener("click", function (e) {
						e.preventDefault();
						var parent = this.parentNode;
						if (!parent.classList.contains("disabled") && !obj.classList.contains("loading")) {
							window.almSetCurrentPage(this.dataset.page, obj, alm);
						}
					});
				}

				// Previous Button
				var prevBtnLabel = alm.addons.paging_previous_label || "";
				var prev = document.createElement("li");
				prev.setAttribute("class", "prev");

				var prevLink = document.createElement("a");
				prevLink.dataset.page = "prev";
				prevLink.href = "#";
				prevLink.role = "button";

				var prevSpan = document.createElement("span");
				prevSpan.innerHTML = prevBtnLabel;

				prevLink.appendChild(prevSpan);
				prev.appendChild(prevLink);
				ul.appendChild(prev);

				// Prev Click Event
				prevLink.addEventListener("click", function (e) {
					e.preventDefault();
					if (!this.parentNode.classList.contains("disabled") && !obj.classList.contains("loading")) {
						var current = ul.dataset.currentPage;
						current--;
						window.almSetCurrentPage(current, obj, alm);
					}
				});
			}

			// Loop Pages
			for (var i = 0; i < pages; i++) {
				var pageNum = i + 1;

				var li = document.createElement("li");
				li.setAttribute("class", "num");
				li.dataset.pageNumber = pageNum;

				var btn = document.createElement("a");
				btn.dataset.page = pageNum;
				btn.href = (0, _setHref2.default)(alm, pageNum, almPaging.urlType);
				btn.role = "button";

				var span = document.createElement("span");
				span.innerHTML = pageNum;

				btn.appendChild(span);

				btn.addEventListener("click", function (e) {
					e.preventDefault();
					var parent = this.parentNode;
					if (!parent.classList.contains("active") && !obj.classList.contains("loading")) {
						var page = this.dataset.page;
						window.almSetCurrentPage(page, obj, alm);
					}
				});

				li.appendChild(btn);
				ul.appendChild(li);
			}

			// Next / Last
			if (alm_paging_controls) {
				// Next Button
				var nextBtnLabel = alm.addons.paging_next_label || "";
				var next = document.createElement("li");
				next.setAttribute("class", "next");

				var nextLink = document.createElement("a");
				nextLink.dataset.page = "next";
				nextLink.href = "#";
				nextLink.role = "button";

				var nextSpan = document.createElement("span");
				nextSpan.innerHTML = nextBtnLabel;

				nextLink.appendChild(nextSpan);
				next.appendChild(nextLink);
				ul.appendChild(next);

				// Next Click Event
				nextLink.addEventListener("click", function (e) {
					e.preventDefault();
					if (!this.parentNode.classList.contains("disabled") && !obj.classList.contains("loading")) {
						var current = ul.dataset.currentPage;
						current++;
						window.almSetCurrentPage(current, obj, alm);
					}
				});

				// Last Button
				var lastBtnLabel = alm.addons.paging_last_label || "";
				if (lastBtnLabel !== "" && showFirstLastBtns) {
					var last = document.createElement("li");
					last.setAttribute("class", "last");

					var lastLink = document.createElement("a");
					lastLink.dataset.page = pages;
					lastLink.href = (0, _setHref2.default)(alm, pages, almPaging.urlType);
					lastLink.role = "button";

					var lastSpan = document.createElement("span");
					lastSpan.innerHTML = lastBtnLabel;

					lastLink.appendChild(lastSpan);
					last.appendChild(lastLink);
					ul.appendChild(last);

					// Last Click Event
					lastLink.addEventListener("click", function (e) {
						e.preventDefault();
						var parent = this.parentNode;
						if (!parent.classList.contains("disabled") && !obj.classList.contains("loading")) {
							var page = this.dataset.page;
							window.almSetCurrentPage(page, obj, alm);
						}
					});
				}
			}

			// Clear existing content
			alm.btnWrap[0].innerHTML = "";
		}

		// Append paging to `.btnWrap`
		alm.btnWrap[0].appendChild(ul);

		// Set current page
		window.almSetCurrentPage(start, obj, alm, dispatchCallback);
	};

	/**
  * Update the next and previous links.
  *
  * @param {object} alm The ALM object.
  * @param {Element} container The HTML container.
  */
	almPaging.updateNextPrevLinks = function (alm, container) {
		var alm_paging_controls = alm.addons.paging_controls;
		if (alm_paging_controls && container) {
			var current = container.dataset.currentPage;
			var total = container.dataset.totalPages;

			var next = container.querySelector("li.next a");
			if (next) {
				var upNext = current < total ? parseInt(current) + 1 : total;
				next.href = (0, _setHref2.default)(alm, upNext, almPaging.urlType);
			}

			var prev = container.querySelector("li.prev a");
			if (prev) {
				var upPrev = current > 1 ? parseInt(current) - 1 : 1;
				prev.href = (0, _setHref2.default)(alm, upPrev, almPaging.urlType);
			}
		}
	};

	/**
  * Determine whether to show/hide the first and last buttons.
  *
  * @param {object}  alm        The ALM object.
  * @param {Element} pagingWrap The wrapper HTML element.
  */
	almPaging.showFirstLast = function (alm, pagingWrap) {
		var alm_paging_controls = alm.addons.paging_controls;
		var alm_show_at_most = parseInt(alm.addons.paging_show_at_most);

		if (alm_paging_controls && pagingWrap) {
			var totalPages = parseInt(pagingWrap.dataset.totalPages);
			var firstBtn = pagingWrap.querySelector("li.first");
			var lastBtn = pagingWrap.querySelector("li.last");
			var firstPage = pagingWrap.querySelector('li[data-page-number="1"]');
			var lastPage = pagingWrap.querySelector('li[data-page-number="' + totalPages + '"]');

			if (alm_show_at_most > totalPages) {
				// Hide buttons if show is greater than total pages
				if (firstBtn) {
					firstBtn.style.display = "none";
				}
				if (lastBtn) {
					lastBtn.style.display = "none";
				}
			} else {
				// Display the first button
				if (firstBtn) {
					firstBtn.style.display = firstPage.style.display === "none" ? "inline-block" : "none";
				}
				if (lastBtn) {
					// Display the last button
					lastBtn.style.display = lastPage.style.display === "none" ? "inline-block" : "none";
				}
			}
		}
	};

	/**
  * Fade in element.
  *
  * @param {Element} element Target HTML element.
  * @param {Number} speed    Transition speed.
  */
	almPaging.fadeIn = function (element, speed) {
		speed = speed / 10;
		var op = 0; // initial opacity
		var timer = setInterval(function () {
			if (op > 0.9) {
				element.style.opacity = 1;
				clearInterval(timer);

				// Callback: Paging Complete
				if (typeof almPagingComplete === "function") {
					window.almPagingComplete();
				}
			}
			element.style.opacity = op;
			op += 0.1;
		}, speed);
	};

	/**
  * Set current navigation item (Click Event)
  *
  * @param {string}      current          Current page number.
  * @param {HTMLElement} obj              The main ALM element `.alm-listing`.
  * @param {Object}      alm              The ALM object.
  * @param {boolean}     dispatchCallback Whether to dispatch the callback.
  */
	window.almSetCurrentPage = function (current, obj, alm) {
		var dispatchCallback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : true;

		var page = parseInt(current) - 1;
		var _alm$addons = alm.addons,
		    preloaded = _alm$addons.preloaded,
		    seo = _alm$addons.seo,
		    filters = _alm$addons.filters;


		var pagingWrap = alm.btnWrap[0].querySelector(".alm-paging");
		var totalPages = parseInt(pagingWrap.dataset.totalPages); // get total pages

		// No pages, hide navigation
		if (totalPages < 2) {
			window.almPagingEmpty(alm);
			alm.btnWrap[0].style.display = "none"; // Hide pagination if empty
			//return false;
		} else {
			alm.btnWrap[0].style.display = "";
		}

		// Add 1 page if preloaded and SEO because start_page = 0;
		current = preloaded && seo && almPaging.init ? current + 1 : current;

		// Set current page data attribute
		pagingWrap.dataset.currentPage = current;

		// First/Prev Buttons
		var firstBtn = pagingWrap.querySelector(".first");
		var prevBtn = pagingWrap.querySelector(".prev");
		if ((prevBtn || firstBtn) && parseInt(current) === 1) {
			if (firstBtn) {
				firstBtn.classList.add("disabled");
			}
			if (prevBtn) {
				prevBtn.classList.add("disabled");
			}
		} else {
			if (firstBtn) {
				firstBtn.classList.remove("disabled");
			}
			if (prevBtn) {
				prevBtn.classList.remove("disabled");
			}
		}

		// Next/Last Buttons
		var nextBtn = pagingWrap.querySelector(".next");
		var lastBtn = pagingWrap.querySelector(".last");
		if ((nextBtn || lastBtn) && parseInt(current) === totalPages) {
			if (nextBtn) {
				nextBtn.classList.add("disabled");
			}
			if (lastBtn) {
				lastBtn.classList.add("disabled");
			}
		} else {
			if (nextBtn) {
				nextBtn.classList.remove("disabled");
			}
			if (lastBtn) {
				lastBtn.classList.remove("disabled");
			}
		}

		// Preloaded
		if (preloaded) {
			if (almPaging.init) {
				// if almPaging.init, add 1 to page to select the correct nav item
				almPaging.init = false;
				page = seo ? page + 1 : page; // If SEO, add 1 page;
			}
		}

		// Loop all, remove `.active`
		var numbers = pagingWrap.querySelectorAll("li.num"); // All Paging links
		if (numbers.length > 0) {
			// Loop Numbered links
			for (var i = 0; i < numbers.length; i++) {
				numbers.item(i).classList.remove("active");
			}

			// Add `.active` class
			numbers.item(page).classList.add("active");
		}

		// Trigger callback
		if (dispatchCallback && typeof almUpdateCurrentPage === "function") {
			window.almUpdateCurrentPage(page, obj, alm); // Update current page
		}

		// Filters Add-on
		if (filters && typeof almFiltersPaged === "function") {
			almFiltersPaged(alm, init);
		}

		// Position paging nav
		almPaging.positionPager(alm, totalPages, current);

		// Update Next/Prev links
		almPaging.updateNextPrevLinks(alm, pagingWrap);

		init = false;
	};

	/**
  * Fade in pagination after content is loaded.
  *
  * @param {HTMLElement} nav The navigation HTML element
  */
	window.almFadePageControls = function (controls, speed) {
		if (!controls) {
			return;
		}
		var almPagingWrap = controls[0].querySelector(".alm-paging");
		if (almPagingWrap) {
			almPaging.fadeIn(almPagingWrap, speed);
		}
	};

	/**
  * Display paging buttons in proper location
  *
  * @param {object} alm The ALM object.
  * @param {string} totalPages Total pages.
  * @param {string} current Current page number.
  * @return null
  */
	almPaging.positionPager = function (alm, totalPages, current) {
		setTimeout(function () {
			var show_at_most = parseInt(alm.addons.paging_show_at_most);

			if (show_at_most && show_at_most < totalPages) {
				var c = current ? current : 1;
				var start = 0;
				var pagingWrap = alm.btnWrap[0].querySelector(".alm-paging");

				// Hide all buttons
				var numbers = pagingWrap.querySelectorAll("li.num"); // All Paging links
				for (var i = 0; i < numbers.length; i++) {
					numbers[i].style.display = "none";
				}

				if (c <= Math.round(show_at_most / 2)) {
					start = 0;
				} else {
					start = c - Math.round(show_at_most / 2);
				}

				if (c > alm.alm_show_at_most) {
					start = c - Math.round(show_at_most / 2);
				}

				if (totalPages - c < Math.round(show_at_most / 2)) {
					start = totalPages - show_at_most;
				}

				// Loop pages to show pages in view
				for (var a = start; a < show_at_most + start; a++) {
					numbers[a].style.display = "inline-block";
				}
			}

			// Show/Hide first and last buttons
			almPaging.showFirstLast(alm, pagingWrap);
		}, alm.speed);
	};

	/**
  * Paging Empty - Set the height to 0 and remove loading class
  *
  * @param {object} alm The ALM object.
  */
	window.almPagingEmpty = function (alm) {
		alm.listing.style.height = "auto";
		alm.main.classList.remove("loading");
		alm.main.classList.remove("alm-is-filtering");
	};

	/**
  * Paging Complete.
  * After paging has completed and posts have been loaded.
  *
  * @param {object} alm The ALM object.
  */
	window.almOnPagingComplete = function (alm) {
		var container = alm.main.querySelector(".alm-paging-content");
		if (!container) {
			return; // Exit if container does not exist.
		}

		var almDoScroll = false;
		var almScrollTop = 100;

		if (alm.addons.paging_scroll === "true") {
			almDoScroll = true;
			almScrollTop = alm.addons.paging_scrolltop ? alm.addons.paging_scrolltop : almScrollTop;
		}

		setTimeout(function () {
			if (almDoScroll) {
				var offset = typeof ajaxloadmore.getOffset === "function" ? ajaxloadmore.getOffset(container).top : container.offsetTop;
				var top = offset - almScrollTop + 1;

				// Scroll window to position
				if (typeof ajaxloadmore.almScroll === "function") {
					ajaxloadmore.almScroll(top);
				} else {
					window.scrollTo({
						top: top,
						behavior: "smooth"
					});
				}

				// Manually trigger browser resize
				setTimeout(function () {
					var event = document.createEvent("Event");
					event.initEvent("resize", false, true);
					window.dispatchEvent(event);
				}, alm.speed);
			}

			// Callback: Paging Complete
			if (typeof almPagingComplete === "function") {
				window.almPagingComplete();
			}
		}, alm.speed);
	};

	/**
  * Callback dispatched from core ALM to initiate the resize observer on the paging div.
  *
  * @param {Element} container Container to watch for height changes.
  */
	window.almPagingSetHeight = function (container) {
		resizeObserver.observe(container);
	};

	/**
  * Resize observer for the ALM element height resize.
  *
  * @see https://developer.mozilla.org/en-US/docs/Web/API/Resize_Observer_API
  */
	var resizeObserver = new ResizeObserver(function (entries) {
		var _iteratorNormalCompletion = true;
		var _didIteratorError = false;
		var _iteratorError = undefined;

		try {
			for (var _iterator = entries[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
				var entry = _step.value;
				var target = entry.target;
				var height = entry.contentRect.height;


				if (height === almPaging.height) {
					return;
				}

				if (target && height) {
					almPaging.height = height;
					var parent = entry.target.parentNode;

					// Get padding of parent (alm-listing) element.
					var s = window.getComputedStyle(parent);
					var pTop = s.getPropertyValue("padding-top").replace("px", "");
					var pBtm = s.getPropertyValue("padding-bottom").replace("px", "");

					// Set element height.
					var h = parseInt(height) + parseInt(pTop) + parseInt(pBtm);
					parent.style.height = h + "px";
				}
			}
		} catch (err) {
			_didIteratorError = true;
			_iteratorError = err;
		} finally {
			try {
				if (!_iteratorNormalCompletion && _iterator.return) {
					_iterator.return();
				}
			} finally {
				if (_didIteratorError) {
					throw _iteratorError;
				}
			}
		}
	});
})();

/***/ }),

/***/ "./src/js/functions/setHref.js":
/*!*************************************!*\
  !*** ./src/js/functions/setHref.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
exports.default = setHref;
/**
 * Create the href destination of the pagination links.
 *
 * @param {object} alm The ALM object.
 * @param {Number} page Page number.
 */
function setHref(alm, page) {
	var urlType = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "default";
	var base_url = alm.canonical_url,
	    addons = alm.addons;

	// Filters

	if (addons.filters) {
		var querystring = window.location.search;
		if (querystring.indexOf("pg=") > -1) {
			// If querystring contains pg=x, replace it.
			querystring = querystring.replace(/pg=\d+/g, "pg=" + page);
		} else {
			querystring = querystring ? querystring + "&pg=" + page : "?pg=" + page;
		}

		return base_url + querystring;
	}

	// Nextpage
	if (addons.nextpage) {
		var _window$alm_nextpage_ = window.alm_nextpage_localize,
		    leading_slash = _window$alm_nextpage_.leading_slash,
		    trailing_slash = _window$alm_nextpage_.trailing_slash;

		if (urlType === "querystring") {
			return base_url + leading_slash + "?pg=" + page; // website.com?pg=3
		} else {
			return base_url + leading_slash + page + trailing_slash; // website.com/3/
		}
	}

	// SEO
	if (addons.seo) {
		return base_url + addons.seo_leading_slash + "page/" + page + addons.seo_trailing_slash;
	}

	// Default
	return "#";
}

/***/ })

/******/ });
//# sourceMappingURL=alm-paging.js.map