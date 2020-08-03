import Vue from "vue/dist/vue.js";
import draggable from "vuedraggable";
import save from "./modules/Save";
import renderfilter from "./modules/RenderFilter";
import { keys } from "./modules/Objects";
import { field_types } from "./modules/Objects";
import { taxonomy_operators } from "./modules/Objects";
import { meta_operators } from "./modules/Objects";
import { meta_types } from "./modules/Objects";
require("../frontend/helpers/polyfills");

/**
 * filterBuilder()
 * Function that initiates the building of filters.
 *
 * @since 1.0
 */
let filterBuilder = () => {
	let data = [
		{
			id: "",
			style: "",
			button_text: "",
		},
	];

	let filters = [];
	let create_btn_text = alm_filters_localize.create_filter;
	let update_btn_text = alm_filters_localize.update_filter;

	// Filter Template component
	Vue.component("filter-template", {
		template: "#filterTemplate",
		props: [
			"filters",
			"filter", // filter data
			"keys",
			"field_types",
			"meta_types",
			"meta_operators",
			"taxonomy_operators",
			"index",
		],

		methods: {
			// Filter onchange
			filterChange: function (event) {
				let target = event.target;
				let id = target.dataset.id;
				let index = target.dataset.index;

				// Set the value
				this.filters[index][id] = target.value;

				// Search
				// If `key` is search, select textfield by default
				if (id === "key" && target.value === "search") {
					this.filters[index]["field_type"] = "text";
				}

				// Star Rating
				// If `field_type` is star_rating, set meta fields
				if (id === "field_type" && target.value === "star_rating") {
					this.filters[index]["key"] = "meta";
					this.filters[index]["meta_operator"] = ">=";
					this.filters[index]["meta_type"] = "NUMERIC";
					this.filters[index]["star_rating_min"] = "1";
					this.filters[index]["star_rating_max"] = "5";
				}

				// Scroll user to top of filter block
				if (id === "field_type" || id === "key") {
					this.scrollToElement(target);
				}
			},

			// Checkbox Filter onchange
			filterCheckboxChange: function (event) {
				let target = event.target;
				let id = target.dataset.id;
				let index = target.dataset.index;

				// Set the value
				this.filters[index][id] = target.checked ? "true" : "";
			},

			// Custom Value Change
			customValueChange: function (event) {
				let target = event.target;
				let parent = event.target.parentNode.parentNode; //.value-fields-wrap
				let id = parent.dataset.id;
				let index = parent.dataset.index;
				let key = parent.dataset.key;

				let customValueRows = parent.querySelectorAll(
					".value-fields-wrap--field"
				); // Get all custom value rows

				let customValueArray = [];

				// Loop each row
				[...customValueRows].forEach((item, e) => {
					let label = item.querySelector(".values-label").value; // Label val
					let value;

					if (key === "sort") {
						// Sort key
						value = item.querySelector(".values-sortvalue").value; // Sort Value val*
					} else {
						// Regular
						value = item.querySelector(".values-value").value; // Value val*
					}

					let array = {};
					array.label = label;
					array.value = value;

					customValueArray.push(array);
				});

				this.filters[index][id] = customValueArray;
			},

			// Add Custom Value
			addCustomValue: function (event) {
				let target = event.target;
				let id = target.dataset.id; // values
				let index = target.dataset.index;

				let customValueArray = this.filters[index][id];

				// Create emepty array
				let array = {
					label: "",
					value: "",
				};

				customValueArray.push(array);
				this.filters[index][id] = customValueArray;

				// Set focus on new custom values
				setTimeout(function () {
					let newElement = target.parentNode.parentNode.querySelector(
						".value-fields-wrap--field:last-child"
					);
					if (newElement) {
						newElement.querySelector("input:first-child").focus();
					}
				}, 100);
			},

			// Move Custom Values Up/down
			customValueMove: function (event) {
				let target = event.currentTarget;
				if (target.classList.contains("disabled")) {
					return false;
				}
				let id = target.dataset.id; // values
				let index = target.dataset.index; // Current value index
				let target_index = "";
				let direction = target.dataset.direction; // Current value index

				// Get current filter index from parent node
				let parent = event.currentTarget.parentNode.parentNode.parentNode;
				let parentIndex = parent.dataset.index; // Current filter index
				let valueArray = this.filters[parentIndex][id];

				let old_index = parseInt(index);

				let current = valueArray[old_index]; // Assign current item to new var
				valueArray.splice(old_index, 1); // Remove from array

				if (direction === "up" && index > 0) {
					target_index = old_index - 1;
				}
				if (direction === "down") {
					target_index = old_index + 1;
				}
				valueArray.splice(target_index, 0, current);
			},

			// Remove Custom Value
			removeCustomValue: function (event) {
				let target = event.currentTarget;
				let id = target.dataset.id; // values
				let index = target.dataset.index; // Current value index

				// Get current filter index from parent node
				let parent = event.currentTarget.parentNode.parentNode.parentNode;
				let parentIndex = parent.dataset.index; // Current filter index

				let valueArray = this.filters[parentIndex][id];
				let valueLength = valueArray.length;
				if (valueLength > 0) {
					valueArray.splice(index, 1); // Remove array item
					this.filters[parentIndex][id] = valueArray;
				}
			},

			// Remove Filter
			removeFilter: function (event) {
				let target = event.currentTarget;
				let index = parseInt(target.dataset.index);
				if (!target.classList.contains("disabled")) {
					this.filters.splice(index, 1); // Remove array item
				}
			},

			// Get currently active select option
			isActive: function (currentVal, testVal, defaultVal) {
				let selected =
					currentVal === testVal ||
					(testVal === "" && currentVal === defaultVal)
						? "selected"
						: "";
				return selected;
			},

			// Show/Hide Exclude filter based on key selection
			checkExclude: function (theKey, index) {
				let show_filter_field = true;

				let hideArray = [
					"",
					"sort",
					"meta",
					"order",
					"orderby",
					"year",
					"month",
					"day",
					"search",
					"post_type",
				];
				var a = hideArray.indexOf(theKey);

				// If key is found in array, hide the field
				if (a !== -1) {
					this.filters[index].exclude = ""; // Set exclude
					show_filter_field = false;
				}

				return show_filter_field;
			},

			// Show/Hide Label filter based on field_type selection
			checkLabel: function (theFieldType, index) {
				let show_filter_field = true;

				let hideArray = ["radio", "checkbox", "star_rating"];
				var a = hideArray.indexOf(theFieldType);

				// If fieldtype is found in array, hide the field
				if (a !== -1) {
					this.filters[index].label = ""; // Set exclude
					show_filter_field = false;
				}

				return show_filter_field;
			},

			// Open/Close a filter group
			toggleFilterGroup: function (event) {
				let target = event.currentTarget;
				let container = target.closest(".alm-filter--wrapper");
				let element = container.querySelector(".collapsible");
				let controls = container.querySelector(
					".collapsible-controls button"
				);

				if (element.classList.contains("closed")) {
					element.classList.remove("closed");
					controls.classList.remove("closed");
				} else {
					element.classList.add("closed");
					controls.classList.add("closed");
				}
			},

			// Open/Close isntructions
			toggleInstructions: function (event) {
				let target = event.currentTarget;
				let parent = target.parentNode;
				let instructions = parent.querySelector(".alm-instructions--copy");
				if (instructions.classList.contains("open")) {
					instructions.classList.remove("open");
				} else {
					instructions.classList.add("open");
				}
			},

			// Scroll to element
			scrollToElement: function (element) {
				let parent = element.closest(".alm-filter--wrapper");
				let position = this.getOffset(parent);
				if (!position) {
					return false;
				}
				window.scrollTo({
					top: position.top - 48,
					behavior: "smooth",
				});
			},

			// Get browser offset
			getOffset: function (el = null) {
				if (!el) {
					return false;
				}
				let rect = el.getBoundingClientRect(),
					scrollLeft =
						window.pageXOffset || document.documentElement.scrollLeft,
					scrollTop =
						window.pageYOffset || document.documentElement.scrollTop;
				return { top: rect.top + scrollTop, left: rect.left + scrollLeft };
			},
		},
	});

	new Vue({
		el: "#app",

		components: {
			draggable,
		},

		data() {
			return {
				data: data,
				filters: filters,
				keys: keys,
				field_types: field_types,
				meta_types: meta_types,
				meta_operators: meta_operators,
				taxonomy_operators: taxonomy_operators,
				create_btn_text: create_btn_text,
				update_btn_text: update_btn_text,
			};
		},

		watch: {
			data: function () {
				// nothing yet
			},
			filters: function () {
				// nothing yet
			},
		},

		methods: {
			// Draggable onEnd [https://github.com/SortableJS/Vue.Draggable#events]
			onEnd: function () {
				this.setFilterOrder();
			},

			// Set the order of filters
			setFilterOrder: function () {
				let rows = document.querySelectorAll(".alm-filter--wrapper");
				[...rows].forEach((row, i) => {
					let target = row.querySelector("input.filter-order");
					let id = target.dataset.id;
					let index = target.dataset.index;
					let oldindex = parseInt(target.dataset.oldindex);

					this.filters[oldindex][id] = target.value;
					target.dataset.oldindex = index;
				});

				//this.sortFiltersObj('order'); // removed in 1.4.1
			},

			// Sort filters by 'order'
			sortFiltersObj: function (param) {
				this.filters.sort(function (a, b) {
					//console.log(a.order, b.order);
					//return parseInt(a.order) - parseInt(b.order);
				});
			},

			// Expand all filters
			expandFilters: function (event) {
				let rows = document.querySelectorAll(".alm-filter--wrapper");
				[...rows].forEach((row, e) => {
					let collapsible = row.querySelector(".collapsible");
					let collapsible_controls = row.querySelector(
						".collapsible-controls button"
					);
					collapsible_controls.classList.remove("closed");
					collapsible.classList.remove("closed");
				});
			},

			// Collapse all filters
			collapseFilters: function (event) {
				let rows = document.querySelectorAll(".alm-filter--wrapper");
				[...rows].forEach((row, e) => {
					let collapsible = row.querySelector(".collapsible");
					let collapsible_controls = row.querySelector(
						".collapsible-controls button"
					);
					collapsible_controls.classList.add("closed");
					collapsible.classList.add("closed");
				});
			},

			// Options onchange
			optionsChange: function (event) {
				let target = event.target;
				let id = target.dataset.id;

				// Set Value
				this.data[0][id] = target.value;
			},

			// Add an empty filter block with defaults
			addFilter: function () {
				let new_array = {
					uniqueid: this.getUniqueID(),
					order: this.filters.length + 1,
					key: "",
					field_type: "",
					author_role: "",
					meta_key: "",
					meta_operator: "IN",
					meta_type: "CHAR",
					taxonomy: "",
					taxonomy_operator: "IN",
					values: [],
					exclude: "",
					label: "",
					placeholder: "",
					title: "",
					description: "",
					button_label: "",
					classes: "",
				};

				this.filters.push(new_array);

				// Set focus on new filter group
				setTimeout(function () {
					let newElement = document.querySelector(
						".alm-filter--wrapper:last-child"
					);
					newElement.focus();
				}, 100);
			},

			// Delete a filter
			deleteFilter: function (event) {
				let target = event.currentTarget;
				let id = target.dataset.id;
				if (id) {
					let confirm_delete = confirm(
						alm_filters_localize.delete_filter + " " + id + "?"
					);
					if (confirm_delete == true) {
						window.location =
							alm_filters_localize.base_url + "&delete_filter=" + id;
					} else {
						event.preventDefault();
						return false;
					}
				}
			},

			// Save the filter
			saveFilter: function (event) {
				let target = event.target;
				target.disabled = true; // disable button

				let baseurl = target.dataset.baseurl;
				baseurl = `${baseurl}&filter=${this.data[0].id}`; // set baseurl

				save(this.data, this.filters, target, baseurl);
			},

			// Prevent empty saving
			isEmpty: function () {
				// Check Key and Style fields
				let is_empty =
					this.data[0].id.trim() === "" || this.data[0].style.trim() === ""
						? true
						: false;

				return is_empty;
			},

			// Restrict the input keys for the filter ID
			restrictIDChars: function (event) {
				let target = event.target;
				let charCode = event.which ? event.which : event.keyCode;

				// CHars to exclude
				let excludeArray = [
					32,
					45,
					61,
					93,
					46,
					44,
					59,
					47,
					43,
					125,
					123,
					34,
					58,
					59,
					39,
					91,
				];
				let found = excludeArray.indexOf(charCode);

				if (found !== -1) {
					// if found, dont return
					event.preventDefault();
				} else {
					return true;
				}
			},

			// Render filter code in modal
			renderFilterCode: function (code) {
				renderfilter(code);
			},

			// Fade Element Out
			fadeOut: function (event) {
				var s = event.style;
				s.opacity = 1;
				(function fade() {
					(s.opacity -= 0.15) < 0
						? (s.display = "none")
						: setTimeout(fade, 40);
				})();
			},

			// Parse the inital filter
			parseFilter: function (obj) {
				obj = JSON.parse(obj);

				this.data[0].id = obj["id"];
				this.data[0].style = obj["style"];
				this.data[0].button_text = obj["button_text"];

				// Loop the filters and push into filter array
				let the_filters = obj["filters"];

				for (let index in the_filters) {
					let obj = {};

					obj = the_filters[index];
					obj.order = parseInt(index) + 1; // Auto set order on load.

					// Check for undefined Custom Values obj.
					// If undefined, create an empty array object otherwise we can't append arrays.
					if (typeof the_filters[index].values === "undefined") {
						the_filters[index].values = [];
					}

					// Set uniqueid (for Vue key)if not defined
					if (typeof the_filters[index].uniqueid === "undefined") {
						the_filters[index].uniqueid = this.getUniqueID();
					}

					filters.push(obj);
				}
			},

			// Generate a Unique ID
			getUniqueID: function () {
				return Math.random().toString(36).substr(2, 9);
			},

			// Show Generated PHP Modal
			showOutput: function (event) {
				let popup = document.querySelector("#alm-filter-pop-up");
				if (popup.classList.contains("show")) {
					popup.classList.remove("show");
				} else {
					popup.classList.add("show");
				}
			},

			// Close PHP Output Modal
			closeModal: function (event) {
				let popup = document.querySelector("#alm-filter-pop-up");
				popup.classList.remove("show");
			},
		},

		mounted: function () {
			// Fadeout the deleted alert message
			let deleted = document.getElementById("filter-deleted");
			if (deleted) {
				let _self = this;
				setTimeout(function () {
					_self.fadeOut(deleted);
				}, 2000);
			}

			// Render filter as PHP
			if (alm_filter_id) {
				this.renderFilterCode(alm_filter_id);
			}

			// Parse the filters for display
			if (alm_filters) {
				// Viewing filter, parse JSON.
				this.parseFilter(alm_filters);
			} else {
				// If empty, create a filter on load
				this.addFilter();
			}
		},
	});
};

// Start the App
let filter_app = document.getElementById("app");
if (filter_app) {
	filterBuilder();
}

/**
 * almToggleExports()
 * Toggle the checkbox for filter export.
 *
 * @since 1.0
 */
let almToggleExports = () => {
	let exportFilters = document.querySelectorAll('input[name="filter_keys[]"');

	toggleExportBtn.addEventListener("change", function (e) {
		let target = e.target;
		for (let i = 0; i < exportFilters.length; ++i) {
			if (target.checked) {
				exportFilters[i].checked = true;
			} else {
				exportFilters[i].checked = false;
			}
		}
	});
};
let toggleExportBtn = document.getElementById("toggle-all-filters");
if (toggleExportBtn) {
	almToggleExports();
}
