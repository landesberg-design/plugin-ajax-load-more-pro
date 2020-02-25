let parseQueryString = function( queryString, removeQuestionMark = false ) {
    var params = {}, queries, temp, i, l;
    
    // Remove Question mark
    queryString = (removeQuestionMark) ? queryString.replace('?', '') : queryString;
    
    // Split into key/value pairs
    queries = queryString.split("&");
    
    // Convert the array of strings into an object
    for ( i = 0, l = queries.length; i < l; i++ ) {
        temp = queries[i].split('=');
        params[temp[0]] = temp[1];
    }
    
    // Return object
    return params;
};
export default parseQueryString;