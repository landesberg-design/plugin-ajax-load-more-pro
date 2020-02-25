import getTerms from './GetTerms';
import getSortOrder from './GetSortOrder';
import ReplaceUnderscore from '../helpers/ReplaceUnderscore'; 


/*
 * almFiltersBuildDataObj
 * Get the selected terms and build the data obj
 *
 * @param filter element   The container element for the current filter set
 * @param data object   The data obj for the filter
 * @return data
 *
 * @since 1.0
 */ 

let buildDataObj = (filter, data) => {
	
	let key = filter.dataset.key;
	let isArchive = filter.dataset.isArchive;
	let fieldtype = filter.dataset.fieldtype;
	let taxonomy = filter.dataset.taxonomy;
	let taxonomyOperator = filter.dataset.taxonomyOperator; 
	let metaKey = filter.dataset.metaKey;
	let metaCompare = filter.dataset.metaCompare; 
	let metaType = filter.dataset.metaType; 	
	
	// Convert date and author queries to abbv because 404 occurs otherwise
	if(key === '_year'){
		key = 'year';
	}
	if(key === '_month'){
		key = 'month';
	}
	if(key === '_day'){
		key = 'day';
	}
	if(key === '_author'){
		key = 'author';
	}
	
	// Convert category_and and tag_and for data params
	if(key === 'category_and'){
		key = 'categoryAnd';
	}
	if(key === 'tag_and'){
		key = 'tagAnd';
	}
	if(key === '_tag'){
		key = 'tag';
	}
	
		
	let terms = '';
	
	// Taxonomy
	if(taxonomy){ 
		
		if(isArchive){			
			taxonomy = ReplaceUnderscore(taxonomy);
		}
		
		// If data obj already has a taxonomy value.
		// Need to prepend : to add multiple values
		if (!data.hasOwnProperty('taxonomy')){
			
			terms = getTerms(filter, data);
			
			data.taxonomy = '';
			
			if(terms){
				data.taxonomy = taxonomy;
            data.taxonomyOperator = taxonomyOperator;
				data.taxonomyTerms = terms;	
			}
			
		} else {
			
			terms = getTerms(filter, data);
				
			let oldTaxonomy = (data.taxonomy !== '' && typeof data.taxonomy !== 'undefined') ? `${data.taxonomy}:` : '';
			let oldTaxonomyTerms = (data.taxonomyTerms !== '' && typeof data.taxonomyTerms !== 'undefined') ? `${data.taxonomyTerms}:` : '';
			let oldtaxonomyOperator = (data.taxonomyOperator !== '' && typeof data.taxonomyOperator !== 'undefined') ? `${data.taxonomyOperator}:` : '';
			
			if(terms){					
            data.taxonomy = `${oldTaxonomy}${taxonomy}`;
            data.taxonomyOperator = `${oldtaxonomyOperator}${taxonomyOperator}`;
				data.taxonomyTerms = `${oldTaxonomyTerms}${terms}`;										
			} 
			
		}					
	}
	
	// Meta_Query(Custom fields)
	else if(metaKey){    	
		
		// If data obj already has a meta value.
		// Need to prepend : to add multiple values
		if (!data.hasOwnProperty('metaKey')){
			
			terms = getTerms(filter, data);
			
			data.metaKey = '';
			
			if(terms){
				data.metaKey = metaKey;
				data.metaValue = terms;	
            data.metaCompare = metaCompare;
            data.metaType = metaType;
			}
			
		} else {
			
			terms = getTerms(filter, data);
			let oldMetaKey = (data.metaKey !== '' && typeof data.metaKey !== 'undefined') ? `${data.metaKey}:` : '';
			let oldMetaValue = (data.metaValue !== '' && typeof data.metaValue !== 'undefined') ? `${data.metaValue}:` : '';
			let oldMetaCompare = (data.metaCompare !== '' && typeof data.metaCompare !== 'undefined') ? `${data.metaCompare}:` : '';	
			let oldMetaType = (data.metaType !== '' && typeof data.metaType !== 'undefined') ? `${data.metaType}:` : '';				
			
			if(terms){	  				
				data.metaKey = `${oldMetaKey}${metaKey}`;
				data.metaValue = `${oldMetaValue}${terms}`;	
            data.metaCompare = `${oldMetaCompare}${metaCompare}`;
            data.metaType = `${oldMetaType}${metaType}`;	
			}
		}					
	}
	
	else{
		
		if(key === 'sort'){ 
			// Sort Key
			data = getSortOrder(filter, data);
		} else {
			// Regular Key
			data[key] = getTerms(filter, data);
		}
		
	}
	
	
	return data;
	
};

export default buildDataObj;
