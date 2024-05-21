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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/next-page.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/functions/getElementByPage.js":
/*!**********************************************!*\
  !*** ./src/js/functions/getElementByPage.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
exports.default = getElementByPage;
/**
 * Get Next Page element by page number.
 *
 * @param {String|Number} page Current page number.
 * @return {Element}           The Next Page element.
 */
function getElementByPage() {
	var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";

	if (!page) {
		return page;
	}
	var target = document.querySelector('.alm-listing[data-nextpage="true"] .alm-nextpage[data-page="' + parseInt(page) + '"]');
	return target ? target : "";
}

/***/ }),

/***/ "./src/js/functions/getPageTitle.js":
/*!******************************************!*\
  !*** ./src/js/functions/getPageTitle.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
exports.default = getPageTitle;
/**
 * Get the page title.
 *
 * @param {string} postTitle The title of the post.
 * @param {string} page      Current page.
 * @param {string} total     The total number of pages.
 * @param {string} template  The title template.
 * @return {string}          The page title.
 * @since 1.0
 */
function getPageTitle(postTitle, page, total, template) {
	var title = document.title;
	if (template) {
		var str = template;
		str = str.replace("{site-title}", alm_localize ? alm_localize.site_title : ""); // Replace site title
		str = str.replace("{tagline}", alm_localize ? alm_localize.site_tagline : ""); // Replace tagline
		str = str.replace("{post-title}", postTitle); // Replace Post Title
		str = str.replace("{page}", page); // Replace Page
		str = str.replace("{total}", total); // Replace Total
		title = str;
	}

	return title;
}

/***/ }),

/***/ "./src/js/functions/scrollToPosition.js":
/*!**********************************************!*\
  !*** ./src/js/functions/scrollToPosition.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
exports.default = scrollToPosition;
/**
 * Dispatch scroll event.
 *
 * @param {Number} top The top position of the element.
 */
function scrollToPosition() {
	var top = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

	if (!top) {
		return false;
	}

	// Scroll window to position
	if (typeof ajaxloadmore.almScroll === "function") {
		ajaxloadmore.almScroll(top);
	} else {
		window.scrollTo({
			top: top,
			behavior: "smooth"
		});
	}
}

/***/ }),

/***/ "./src/js/next-page.js":
/*!*****************************!*\
  !*** ./src/js/next-page.js ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _getElementByPage = __webpack_require__(/*! ./functions/getElementByPage */ "./src/js/functions/getElementByPage.js");

var _getElementByPage2 = _interopRequireDefault(_getElementByPage);

var _getPageTitle = __webpack_require__(/*! ./functions/getPageTitle */ "./src/js/functions/getPageTitle.js");

var _getPageTitle2 = _interopRequireDefault(_getPageTitle);

var _scrollToPosition = __webpack_require__(/*! ./functions/scrollToPosition */ "./src/js/functions/scrollToPosition.js");

var _scrollToPosition2 = _interopRequireDefault(_scrollToPosition);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var almNextpage = {};

(function () {
	// Defaults.
	almNextpage.init = true;
	almNextpage.urls = true;
	almNextpage.animating = false;
	almNextpage.scroll = false;
	almNextpage.popstate = false;
	almNextpage.firstpage = 1;
	almNextpage.offset = 30;
	almNextpage.active = false;
	almNextpage.paging = true;
	almNextpage.previousUrl = window.location.href;
	almNextpage.fromPopstate = false;
	almNextpage.timer = null;
	almNextpage.titleTemplate = "";
	almNextpage.first = document.querySelector('.alm-listing[data-nextpage="true"] .alm-nextpage');
	almNextpage.wrap = document.querySelector('.alm-listing[data-nextpage="true"]');

	/**
  * Initial vars setup for Next Page;
  *
  * @param {Element} el The `.alm-listing` element.
  * @since 1.0
  */
	almNextpage.setup = function (el) {
		// Get alm data attributes.
		var alm = el.dataset;

		// Setup the title template.

		almNextpage.titleTemplate = alm.nextpageTitleTemplate ? alm.nextpageTitleTemplate : "";

		// Run
		if (alm.nextpage === "true" && alm.paging === "true") {
			return; // Exit if paging is active.
		}
		almNextpage.paging = false;
		almNextpage.active = true;

		// Scroll & Offset.
		almNextpage.scrollOptions = alm.nextpageScroll;
		almNextpage.scrollOptions = almNextpage.scrollOptions.split(":");
		almNextpage.scroll = almNextpage.scrollOptions[0] === "false" || almNextpage.scrollOptions[0] === "0" ? false : true; // Convert to boolean
		almNextpage.offset = almNextpage.scrollOptions[1] ? parseInt(almNextpage.scrollOptions[1]) : almNextpage.offset;

		// URLs.
		almNextpage.urls = alm.nextpageUrls;
		almNextpage.urls = almNextpage.urls == "true"; // convert to boolean

		// If startpage > 1.
		var startPage = parseInt(alm.nextpageStartpage);

		// If paged, move to current page on page load.
		if (startPage > 1) {
			almNextpage.popstate = almNextpage.fromPopstate = true;

			// Scroll target.
			var target = document.querySelector('.alm-nextpage[data-page="' + parseInt(startPage) + '"]');
			if (target) {
				var offset = typeof ajaxloadmore.getOffset === "function" ? ajaxloadmore.getOffset(target).top : target.offsetTop;

				var top = offset - parseInt(almNextpage.offset) + 1;

				if (almNextpage.fromPopstate) {
					// From Popstate.
					window.scrollTo(0, top);
					almNextpage.fromPopstate = false;
				} else {
					// Standard.
					(0, _scrollToPosition2.default)(top);
				}

				// Delay until user is moved to page.
				setTimeout(function () {
					almNextpage.popstate = false;
				}, 250);
			}
		}
	};

	// If nextpage, run initial set up.
	if (almNextpage.first) {
		// Get closest Ajax Load More object (Temp hack).
		var almListing = document.querySelector('.alm-listing[data-nextpage="true"]');
		if (almListing) {
			almNextpage.setup(almListing);
		}
	}

	/**
  * Set initial vars - triggered from core ajax-load-more.js
  *
  * @param {Object} alm Ajax Load More object
  * @since 1.0
  */
	window.almSetNextPageVars = function (alm) {
		almNextpage.paging = alm.addons.paging; // paging
		if (alm.listing.dataset.nextpage === "true") {
			almNextpage.active = true;
		}
	};

	/**
  * Update browser URL on scroll.
  *
  * @since 1.0
  */
	almNextpage.onScroll = function () {
		var scrollTop = window.scrollY;

		if (almNextpage.active && !almNextpage.popstate && scrollTop > 1 && !almNextpage.paging) {
			if (almNextpage.timer) {
				window.clearTimeout(almNextpage.timer);
			}

			almNextpage.timer = window.setTimeout(function () {
				// Get container scroll position
				var fromTop = scrollTop + almNextpage.offset;
				var posts = document.querySelectorAll(".alm-nextpage");
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
				var total = currentPost ? currentPost.dataset.pages : "";
				var title = currentPost ? currentPost.dataset.title : "";

				if (page === undefined || page === "") {
					page = almNextpage.first.dataset.page;
					permalink = almNextpage.first.dataset.url;
					title = almNextpage.first.dataset.title;
					total = almNextpage.first.dataset.totalPosts;
				}

				// Urls don't match, update browser URL.
				if (url !== permalink) {
					almNextpage.setURL(page, permalink, title, total, false);
				}
			}, 10);
		}
	};
	window.addEventListener("touchstart", almNextpage.onScroll);
	window.addEventListener("scroll", almNextpage.onScroll);

	/**
  * Main NextPage function.
  * Dispatched from core Ajax Load More to trigger Next Page functionality.
  *
  * @param {Object} alm Ajax Load More object
  * @since 1.0
  */
	window.almSetNextPage = function (alm) {
		almNextpage.active = true;
		almNextpage.paging = alm.addons.paging; // Paging add-on.
		almNextpage.btnWrap = alm.btnWrap[0];
		almNextpage.titleTemplate = alm.addons.nextpage_title_template;

		// If Paging.
		if (alm.addons.paging) {
			almNextpage.firstpage = alm.listing.dataset.nextpageStartpage;
		}

		// First Run ONLY.
		if (almNextpage.init) {
			// URLS
			almNextpage.urls = alm.addons.nextpage_urls == "true"; // URL Updates
			almNextpage.canonical_url = alm.canonical_url; // canonical url

			// Post Title
			almNextpage.post_title = alm.addons.nextpage_postTitle;

			// Startpage
			almNextpage.startpage = parseInt(alm.addons.nextpage_startpage); // The starting page.

			// Scroll & Offset
			almNextpage.scrollOptions = alm.addons.nextpage_scroll;
			almNextpage.scrollOptions = almNextpage.scrollOptions.split(":");
			almNextpage.scroll = almNextpage.scrollOptions[0] === "false" || almNextpage.scrollOptions[0] === "0" ? false : true; // convert to boolean
			almNextpage.offset = almNextpage.scrollOptions[1] ? parseInt(almNextpage.scrollOptions[1]) : almNextpage.offset;

			// Init
			almNextpage.init = false;
		}

		// Scroll to post
		if (almNextpage.scroll && !almNextpage.paging) {
			almNextpage.fromPopstate = almNextpage.popstate = false;
			almNextpage.scrollToPage(alm.page);
		}

		// Paging - Set URL
		if (almNextpage.paging) {
			almNextpage.setURL(parseInt(alm.page) + 1, almNextpage.canonical_url, almNextpage.post_title, alm.totalpages ? alm.totalpages : 0, true);
		}
	};

	/**
  * Fires when users click back or forward browser buttons.
  *
  * @since 1.0
  */
	almNextpage.onpopstate = function (event) {
		var page = void 0;

		// Exit if not active.
		if (!almNextpage.active) {
			return false; // Safari fix - only fire when active
		}

		// Set popstate flags.
		almNextpage.popstate = true;
		almNextpage.fromPopstate = true;

		if (event.state) {
			page = event.state.pageID;
			page = !page || page === null ? 1 : page;
		} else {
			if (almNextpage.paging) {
				page = almNextpage.firstpage;
			} else {
				page = almNextpage.first.dataset.page;
			}
		}

		if (almNextpage.paging) {
			// Paging: Trigger Paging Navigation.
			var button = almNextpage.btnWrap.querySelector('li.num a[data-page="' + parseInt(page) + '"]');
			if (button) {
				button.click();
			}
		} else {
			// Standard.
			var target = (0, _getElementByPage2.default)(page);
			if (target) {
				var offset = typeof ajaxloadmore.getOffset === "function" ? ajaxloadmore.getOffset(target).top : target.offsetTop;
				var top = offset - almNextpage.offset + 1;

				// Delay fixes browser popstate issues
				setTimeout(function () {
					window.scrollTo(0, top);
					almNextpage.previousUrl = event.permalink; // Restore previous URL.

					// Popstate reset flags.
					almNextpage.popstate = false;
					almNextpage.fromPopstate = false;
				}, 10);
			}
		}
	};

	/**
  * Window popstate eventlistener.
  *
  * @since 1.0
  */
	window.addEventListener("popstate", function (event) {
		if (typeof window.history.pushState == "function") {
			almNextpage.onpopstate(event);
		}
	});

	/**
  * Set the browser URL to current permalink.
  *
  * @param {string}  page      Current page number.
  * @param {string}  permalink Current URL.
  * @param {string}  title     Current post title.
  * @param {string}  total     Total # of posts.
  * @param {boolean} is_paging Is this paging, true/false.
  */
	almNextpage.setURL = function (page, permalink, title, total, is_paging) {
		if (!almNextpage.urls) {
			return false; // Exit if urls are disabled.
		}

		var _window$alm_nextpage_ = window.alm_nextpage_localize,
		    leading_slash = _window$alm_nextpage_.leading_slash,
		    trailing_slash = _window$alm_nextpage_.trailing_slash;


		var urlType = almNextpage.wrap.dataset.nextpageBreak === "true" ? "querystring" : "default";

		// Paging add-on.
		if (is_paging && page > 1) {
			if (urlType === "querystring") {
				permalink = permalink + leading_slash + "?pg=" + page; // website.com?pg=3
			} else {
				permalink = permalink + leading_slash + page + trailing_slash; // website.com/3/
			}
		}

		// Page Title.
		var pageTitle = (0, _getPageTitle2.default)(title, page, total, almNextpage.titleTemplate);

		// Confirm URLs don't match and not from popstate.
		if (permalink !== almNextpage.previousUrl && !almNextpage.fromPopstate) {
			if (typeof window.history.pushState === "function") {
				var state = {
					pageID: page,
					permalink: permalink,
					pageTitle: pageTitle
				};

				history.pushState(state, pageTitle, permalink);

				// Trigger analytics.
				if (typeof ajaxloadmore.analytics === "function") {
					ajaxloadmore.analytics("nextpage");
				}
			}
			almNextpage.previousUrl = permalink;
		}

		// Set page title.
		document.title = pageTitle;

		// Reset flags.
		almNextpage.fromPopstate = false;
		almNextpage.popstate = false;
	};

	/**
  * Scroll user to current page.
  *
  * @param {Number} page Current page number.
  */
	almNextpage.scrollToPage = function (page) {
		// Get current page number
		page = almNextpage.paging ? parseInt(page) + 1 : page + almNextpage.startpage + 1;

		// Get scroll target
		// If paging, send user to top of listing
		var target = almNextpage.paging ? almNextpage.wrap : (0, _getElementByPage2.default)(page);

		if (target) {
			var offset = typeof ajaxloadmore.getOffset === "function" ? ajaxloadmore.getOffset(target).top : target.offsetTop;
			var top = offset - almNextpage.offset + 1;

			if (almNextpage.paging) {
				// Paging
				(0, _scrollToPosition2.default)(top);
			} else {
				// Standard
				if (almNextpage.fromPopstate) {
					// Popstate
					setTimeout(function () {
						window.scrollTo(0, top);
						almNextpage.fromPopstate = false;
					}, 5);
				} else {
					// Standard
					(0, _scrollToPosition2.default)(top);
				}
			}
		}
	};

	/**
  * On DOM loaded.
  */
	window.addEventListener("DOMContentLoaded", function () {
		// Nothing yet.
	});
})();

/***/ })

/******/ });
//# sourceMappingURL=alm-next-page.js.map