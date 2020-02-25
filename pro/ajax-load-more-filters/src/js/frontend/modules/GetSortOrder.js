import getTerms from './GetTerms';

/*
 * getSortOrder
 * Set the sort parameters for order and orderby.
 * If this is a custom field sort, check the orderby param and match against the array of possibilities
 *
 * @param filter element   The container element for the current filter set
 * @param data object   The data obj for the filter
 * @return data
 *
 * @since 1.7.2
 */ 

let getSortOrder = (filter, data) => {
	let sortValue = getTerms(filter, data);
	if(sortValue){

		let sortArray = sortValue.split(':');
		
		// Array must have a length of 2
		if(sortArray.length == 2){
			let order = sortArray[0];
			let orderby = sortArray[1].toLowerCase();
			
			let orderArray = ['id', 'author', 'title', 'name', 'type', 'date', 'modified', 'parent', 'rand', 'relevance', 'menu_order', 'post__in', 'post__name_in', 'post_parent__in'];
			
			// Find value in `ordering` array
			let in_array = orderArray.indexOf(orderby);
			
			if(in_array != -1){
				// Standard Ordering
				data['order'] = order;
				data['orderby'] = orderby;
				data['metaKey'] = ''; // reset if previous set
			} else {
				// Order by Custom Field
				data['order'] = order;
				data['orderby'] = 'meta_value';
				data['metaKey'] = orderby;			
			}
			
		}
	}
	
	return data;
};

export default getSortOrder;