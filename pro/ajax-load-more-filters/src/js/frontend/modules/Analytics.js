/*
 * analytics
 * Send pageviews, filters to Google Analytics
 
 * @return null   dispatch global GA function call
 * @since 1.0
 */ 

let analytics = () => {	   
   let path = `/${window.location.pathname}${window.location.search}`;		
   if (typeof ajaxloadmore.tracking === 'function') {
      ajaxloadmore.tracking(path);      
   } else {
      // Gtag GA Tracking
   	if (typeof gtag === 'function') {
   	   gtag('event', 'page_view', { 'page_path': path });
   	}
   	
      // Deprecated GA Tracking
   	if (typeof ga === 'function') {
   	   ga('send', 'pageview', path);
   	}
   	
   	// Monster Insights
   	if (typeof __gaTracker === 'function') {
   	   __gaTracker('send', 'pageview', path);
   	}   	
	}   
}
export default analytics;