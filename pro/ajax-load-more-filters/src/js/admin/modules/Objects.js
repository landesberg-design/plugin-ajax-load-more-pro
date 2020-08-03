let keys = [
	{ value: "author", text: "Author" },
	{ value: "meta", text: "Custom Fields (Meta Query)" },
	{ value: "post_type", text: "Post Type" },
	{ value: "search", text: "Search" },
	{ value: "taxonomy", text: "Taxonomy" },
	{ value: "#", text: alm_filters_localize.category_parameters },
	{ value: "category", text: "Category" },
	{ value: "category__and", text: "Category AND (category__and)" },
	{ value: "#", text: alm_filters_localize.tag_parameters },
	{ value: "tag", text: "Tag" },
	{ value: "tag__and", text: "Tag AND (tag__and)" },
	{ value: "#", text: alm_filters_localize.ordering_parameters },
	{ value: "order", text: "Order" },
	{ value: "orderby", text: "Orderby" },
	{ value: "sort", text: "Sorting (Order & Orderby)" },
	{ value: "#", text: alm_filters_localize.date_parameters },
	{ value: "day", text: "Day" },
	{ value: "month", text: "Month" },
	{ value: "year", text: "Year" },
];
export { keys };

let taxonomy_operators = [
	{ value: "IN", text: "IN" },
	{ value: "NOT IN", text: "NOT IN" },
	{ value: "AND", text: "AND" },
];
export { taxonomy_operators };

let meta_operators = [
	{ value: "IN", text: "IN" },
	{ value: "NOT IN", text: "NOT IN" },
	{ value: "BETWEEN", text: "BETWEEN" },
	{ value: "NOT BETWEEN", text: "NOT BETWEEN" },
	{ value: "=", text: "= (equals)" },
	{ value: "!=", text: "!= (does NOT equal)" },
	{ value: ">", text: "> (greater than)" },
	{ value: ">=", text: ">= (greater than or equal to)" },
	{ value: "<", text: "< (less than)" },
	{ value: "<=", text: "<= (less than or equal to)" },
	{ value: "LIKE", text: "LIKE" },
	{ value: "NOT LIKE", text: "NOT LIKE" },
	{ value: "EXISTS", text: "EXISTS" },
	{ value: "NOT EXISTS", text: "NOT EXISTS" },
];
export { meta_operators };

let field_types = [
	{ value: "checkbox", text: "Checkbox" },
	{ value: "radio", text: "Radio" },
	{ value: "select", text: "Select" },
	{ value: "select_multiple", text: "Select (Multiple)" },
	{ value: "star_rating", text: "Star Rating" },
	{ value: "text", text: "Textfield" },
	{ value: "#", text: alm_filters_localize.field_type_beta },
	{ value: "date_picker", text: "Date Picker" },
	{ value: "range_slider", text: "Range Slider" },
];
export { field_types };

let meta_types = [
	{ value: "BINARY", text: "BINARY" },
	{ value: "CHAR", text: "CHAR" },
	{ value: "DATE", text: "DATE" },
	{ value: "DATETIME", text: "DATETIME" },
	{ value: "DECIMAL", text: "DECIMAL" },
	{ value: "NUMERIC", text: "NUMERIC" },
	{ value: "SIGNED", text: "SIGNED" },
	{ value: "TIME", text: "TIME" },
	{ value: "UNSIGNED", text: "UNSIGNED" },
];
export { meta_types };
