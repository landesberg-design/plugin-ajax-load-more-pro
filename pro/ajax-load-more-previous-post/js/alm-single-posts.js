/*
 * Ajax Load More - Single Post
 * https://connekthq.com/plugins/ajax-load-more/add-ons/single-posts/
 * Copyright Connekt Media - https://connekthq.com
 * Author: Darren Cooney
 * Twitter: @KaptonKaos, @connekthq
 */
var almSinglePosts = {};

(function() {


   /**
    * onload
    * Initial function loaded on page load.
    *
    * @since 2.0
    */
   almSinglePosts.onload = function() {
      almSinglePosts.init = true;
      almSinglePosts.timer = null;
      almSinglePosts.initPageTitle = document.title;
      almSinglePosts.titleTemplate = '';
      almSinglePosts.pageview = true;
      almSinglePosts.animating = false;
      almSinglePosts.scroll = true;
      almSinglePosts.offset = 30;
      almSinglePosts.popstate = false;
      almSinglePosts.is_disqus = false;
      almSinglePosts.active = true;
      almSinglePosts.first = document.querySelector('.alm-single-post');
      almSinglePosts.isIE = (navigator.appVersion.indexOf("MSIE 10") !== -1) ? true : false;     
      almSinglePosts.showProgressBar = false;
   };
   
   if (document.querySelector('.alm-single-post')) {
      almSinglePosts.onload();
   }


   /**
    * onScroll
    * Scroll and touchstart events for addon
    *
    * @since 2.0
    */
   almSinglePosts.onScroll = function() {

      var scrollTop = window.pageYOffset;

      if (almSinglePosts.active && !almSinglePosts.popstate && scrollTop > 1) {

         //almSinglePosts.timer = window.setTimeout(function() {

            // Get container scroll position
            var fromTop = scrollTop + almSinglePosts.offset;
            var posts = document.querySelectorAll('.alm-single-post');
            var url = window.location.href;

            // Loop all posts
            var current = Array.prototype.filter.call(posts, function(n, i) {
	            if (typeof ajaxloadmore.getOffset === 'function') {
	               var divOffset = ajaxloadmore.getOffset(n);
						if (divOffset.top < fromTop){
							return n;
						}
					}
            });

            // Get the data attributes of the current element
            var currentPost = current[current.length - 1];
            var id = (currentPost) ? currentPost.dataset.id : undefined;
            var permalink = (currentPost) ? currentPost.dataset.url : '';
            var title = (currentPost) ? currentPost.dataset.title : '';
            var page = (currentPost) ? currentPost.dataset.page : '';

            // If undefined, use the first post data
            if (id === undefined) {
               id = almSinglePosts.first.dataset.id;
               permalink = almSinglePosts.first.dataset.url;
               title = almSinglePosts.first.dataset.title;
            }
            
            // Set the reading progress bar
            if(almSinglePosts.showProgressBar){
               almSinglePosts.almSetProgressBar(id);
            }

            // Set URL, if applicible.               
            if (url !== permalink) {
               almSinglePosts.setURL(id, permalink, title, page);
            }
            
         //}, 10);

      }
   };
   window.addEventListener('touchstart', almSinglePosts.onScroll);
   window.addEventListener('scroll', almSinglePosts.onScroll);
   
   
   
   /**
    * almSetSinglePost
    * Main Previous Post function
    * triggered from core ajax-load-more.js
    *
    * @since 1.0
    */
   window.almSetSinglePost = function(alm, id, permalink, title) {
      almSinglePosts.titleTemplate = alm.addons.single_post_title_template; // Title Template
      if (almSinglePosts.init) { // Is init
         almSinglePosts.siteTitle = alm.addons.single_post_siteTitle; // Site Title
         almSinglePosts.siteTagline = alm.addons.single_post_siteTagline; // Site Tagline
         almSinglePosts.pageview = alm.addons.single_post_pageview; // Send pageviews
         almSinglePosts.scroll = alm.addons.single_post_scroll; // Scroll
         almSinglePosts.offset = parseInt(alm.addons.single_post_scroll_top); // Scroll Top
         almSinglePosts.controls = alm.addons.single_post_controls; // Enable back/fwd button controls
         almSinglePosts.controls = (almSinglePosts.controls === '1') ? true : false;
         almSinglePosts.scroll = (almSinglePosts.scroll === 'true') ? true : false;
         almSinglePosts.progress_bar = alm.addons.single_post_progress_bar; // Progress Bar
         
         // Initiate Progress Bar         
         if(almSinglePosts.progress_bar !== ''){
            almSinglePosts.almCreateProgressBar(almSinglePosts.progress_bar)
         }
      }
      
      // Move to post
      if (almSinglePosts.scroll && !almSinglePosts.init) {
         almSinglePosts.scrollToPost(id);
      }

      almSinglePosts.init = false;
   };
   
   
   
   /**
    * almSetProgressBar
    * Set the width of the reader progress bar
    *
    * @param {*} ID 
    * @since 1.4.2
    */
   almSinglePosts.almSetProgressBar = function(id) {
	   
	   if(!id || !almSinglePosts.showProgressBar){
		   return false; // Exit if ID null
	   }
	   
	   var progressDiv = document.querySelector('.alm-reveal.alm-single-post[data-id="'+ id +'"]');
            
      if(progressDiv){
         
         var elHeight = Math.round(progressDiv.offsetHeight);
         var wHeight = Math.round(window.outerHeight);
         var scrollT = Math.round(document.documentElement.scrollTop);
			var progressOffset = ajaxloadmore.getOffset(progressDiv);
			var pTop = Math.round(progressOffset.top);
			
			if(scrollT > parseInt(pTop - almSinglePosts.offset)){ 
   			// Get Percentage
				var pageEnd = Math.round(wHeight/1.5);
				var percentage = (parseInt(scrollT - pTop + almSinglePosts.offset) / parseInt(elHeight - pageEnd - almSinglePosts.offset)) * 100;
				
				// Set Width
				// console.log(Math.floor(percentage));
				almSinglePosts.progress.style.width = Math.floor(percentage) + '%';
			} 
			else { 
   			// Reset
				almSinglePosts.progress.style.width = '0%';
			}
		}		
	};
	
	
	
	/**
    * almCreateProgressBar
    * Create the reading progress bar and append to DOM
    *
    * @param {*} ID 
    * @since 1.4.2
    */
   almSinglePosts.almCreateProgressBar = function(style) {
      
      if(!style){
         return false; // Exit if empty
      }      
      
      var barStyle = style.split(':'); // Split shortcode value to access settings
      if(barStyle.length < 3){
         return false; // Exit, not the correct amount of parameters
      }
      
      var transition = 'all 0.3s linear';
      var transition2 = 'all 0.15s linear';
      var body = document.body;
      
      almSinglePosts.progressWrap = document.createElement('div');
      almSinglePosts.progressWrap.classList.add('alm-reading-progress-wrap'); 
      
      almSinglePosts.progress = document.createElement('div');
      almSinglePosts.progress.classList.add('alm-reading-progress');  
      
      almSinglePosts.progressWrap.style.transition = transition;
      almSinglePosts.progress.style.transition = transition2;
      
      if(barStyle[3]){ // Background Color
         almSinglePosts.progressWrap.style.backgroundColor = '#'+ barStyle[3];
      }
      
      almSinglePosts.progressWrap.style.position = 'fixed';
      almSinglePosts.progressWrap.style.zIndex = '999999'; 
      almSinglePosts.progressWrap.style.width = '100%'; 
      almSinglePosts.progressWrap.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.2)'; 
      almSinglePosts.progressWrap.style.opacity = '0'; 
      
        
      // Position Progress Bar
      if(barStyle[0] === 'bottom'){
         almSinglePosts.progressWrap.style.bottom = '0';
      } else {
         almSinglePosts.progressWrap.style.top = '0';
      }      
      almSinglePosts.progressWrap.style.left = '0';
      
      // Height
      almSinglePosts.progressWrap.style.height = barStyle[1] + 'px';
      almSinglePosts.progress.style.height = barStyle[1] + 'px';
      
      almSinglePosts.progress.style.width = '0';
      
      // Foreground Color
      almSinglePosts.progress.style.backgroundColor = '#' + barStyle[2];
      
      // Append to body
      body.appendChild(almSinglePosts.progressWrap);
      almSinglePosts.progressWrap.appendChild(almSinglePosts.progress);
      
      /*
       * Callback
       * Dispatched after element attached to DOM
       *
       */
      if (typeof almReadingProgressAttached === 'function') {
         almReadingProgressAttached(almSinglePosts.progressWrap);
      } 
      
      // Fade In
      setTimeout(function(){
         almSinglePosts.progressWrap.style.opacity = '1';
      }, 250);
      
      // Set flag
      almSinglePosts.showProgressBar = true;
      
   };



   /**
    * onpopstate
    * Fires when users click back or forward browser buttons
    *
    * @since 1.0
    */
   almSinglePosts.onpopstate = function(event) {
      if (!almSinglePosts.init && almSinglePosts.active) {
         almSinglePosts.popstate = true;
         var id;
         if (event.state) {
            id = event.state.postID;
            almSinglePosts.setPageTitle(event.state.title);
         } else {
            id = almSinglePosts.first.dataset.id;
            document.title = almSinglePosts.initPageTitle;
         }
			
         // Move to post
         almSinglePosts.popstate = true;
         almSinglePosts.scrollToPost(id);
      }
   };



   /**
    * popstate
    * Window popstate eventlistener
    *
    * @since 1.0
    */
   window.addEventListener('popstate', function(event) {
      if (typeof window.history.pushState == 'function') {
         var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
	      document.body.scrollTop = scrollTop;
         almSinglePosts.onpopstate(event);
      }
   });



   /**
    * setURL
    * Set the browser URL to current permalink
    *
    * @id string
    * @permalink string
    * @title string
    * @page string
    * @return null
    * @since 1.0
    */
   almSinglePosts.setURL = function(id, permalink, title, page) {	  
	   
      var state = {
         postID: id,
         permalink: permalink,
         title: title
      };

      // If pushstate & not IE10 is enabled
      if (typeof window.history.pushState === 'function' && !almSinglePosts.isIE) {
	      
	      almSinglePosts.setPageTitle(title);
	      
         if (almSinglePosts.controls) {
            history.pushState(state, title, permalink);
         } else {
            history.replaceState(state, title, permalink);
         }

	      // almUrlUpdate (Core ALM Callback)
	      if (typeof almUrlUpdate === 'function') {
	         window.almUrlUpdate(permalink, 'single-post');
	      }
	      
      }

      // Disqus comments
      if (almSinglePosts.is_disqus) {
         almSinglePosts.disqusLoad(id, permalink, title, page);
      }

      // Google Analytics - send pageview
      if (almSinglePosts.pageview === 'true') { // Send pageviews to Google Analytics
         var path = '/' + window.location.pathname;
         if (typeof ajaxloadmore.tracking === 'function') {
            ajaxloadmore.tracking(path);
         } else {
            // Gtag GA Tracking
            if (typeof gtag === 'function') {
               gtag('event', 'page_view', {
                  'page_path': path
               });
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
   };



   /**
    * scrollToPost
    * Smooth scroll user to current post
    * @since 1.0
    */
   almSinglePosts.scrollToPost = function(id) {
      var target = document.querySelector('.alm-reveal.alm-single-post.post-' + id);
      if (target) {
	      
	      // Confirm target has children, if not move to top of page. (offset fix_
	      target = (target.hasChildNodes()) ? target : document.querySelector('body');
	      
	      var offset = (typeof ajaxloadmore.getOffset === 'function') ? ajaxloadmore.getOffset(target).top : target.offsetTop;
         var top = offset - almSinglePosts.offset + 1;         
         if(!top){
	      	return false;
         }   
               
         // Scroll window to position
         
         if(almSinglePosts.popstate){
            
	         // From Popstate
	         setTimeout(function(){
   	         // Delay fixes browser popstate issues				
					window.scrollTo(0, top);
				}, 5);
	         
         } else {     
            
	         // Standard Scroll  
            if (typeof ajaxloadmore.almScroll === 'function') {
               ajaxloadmore.almScroll(top);
            } else {
               window.scrollTo({
                  top: top,
                  behavior: 'smooth'
               });
            }
            
         }    
              
         // Set popstate flag to false after transition is done
         setTimeout(function() {
            almSinglePosts.popstate = false;
         }, 250);
         
      }
   };



   /**
    * setPageTitle
    * Set the page title
    * @since 1.0
    */
   almSinglePosts.setPageTitle = function(title) {
      if (almSinglePosts.titleTemplate === '') {
         document.title = document.title;
      } else {
         var str = almSinglePosts.titleTemplate;
         str = str.replace('{site-title}', almSinglePosts.siteTitle); // Replace site title
         str = str.replace('{tagline}', almSinglePosts.siteTagline); // Replace tagline
         str = str.replace('{post-title}', title); // Replace Post Title
         document.title = str;
      }
   };



   /**
    * disqusInit
    * Load Disqus comments on page init
    *
    * @param disqus_container   object
    * @since 1.2
    */
   almSinglePosts.disqusInit = function(disqus_container) {

      var disqus_shortname = disqus_container.dataset.disqusShortname; // get Disqus shortname from container

      if (disqus_shortname) {

         // Append #disqus_thread to container
         var disqus = document.createElement('div');
         disqus.id = 'disqus_thread';
         disqus_container.appendChild(disqus);

         // Load the Disqus JS file
         var file = document.createElement('script');
         file.setAttribute('type', 'text/javascript');
         file.setAttribute('src', '//' + disqus_shortname + '.disqus.com/embed.js');
         document.getElementsByTagName("body")[0].appendChild(file);
         almSinglePosts.is_disqus = true;
      }
   };

   if (document.querySelector('.alm-single-post .alm-disqus')) {
      almSinglePosts.disqusInit(document.querySelector('.alm-single-post .alm-disqus')); // Init Disqus 
   }



   /*
    * almSinglePosts.disqusLoad
    * Load Disqus comments when page comes into view
    *
    * @param id          string
    * @param permalink   string
    * @param title       string
    * @param page        string
    * @since 1.2
    */
   almSinglePosts.disqusLoad = function(id, permalink, title, page) {

      var disqus_thread = document.getElementById('disqus_thread');
      if (disqus_thread) {
         var parent = disqus_thread.parentNode, // .alm-disqus
            height = parent.offsetHeight;

         parent.style.minHeight = height + 'px'; // Set height of .alm-disqus to prevent page jumping when disqus loads.

         // Hide #disqus_thread
         disqus_thread.style.display = 'none';

         // New target div
         var target = document.querySelectorAll('.alm-single-post .alm-disqus').item(parseInt(page));
         if (target) {
            // Append #disqus_thread to new container
            target.appendChild(disqus_thread).style.display = 'block';
         }

         // RESET Disqus instance
         // https://help.disqus.com/customer/portal/articles/472107-using-disqus-on-ajax-sites
         DISQUS.reset({
            reload: true,
            config: function() {
               this.page.identifier = id + ' ' + permalink;
               this.page.url = permalink;
               this.page.title = title;
            }
         });
      }
   };

})();