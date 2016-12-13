<?php
/*
 * Plugin Name: WashU Outbound Tracking
 * Plugin URI: ???????
 * Description: Track in page external links that aren't tracked via normal page tracking. 
 * Version: 0.1
 * Author: John Richards, Rachel Zhou
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die( "Don't do that. Come here via WP-Admin like you know you should." );
}
include_once plugin_dir_path( __FILE__ ) . 'settings.php'; // settings page
add_action( 'wp_head','washu_ot_hook_analytics' );

function washu_ot_hook_analytics() {
	$options = get_option( 'wustl_outbound_settings' );

	//$tracking_code = 'UA-XXXXXXXXX-X';
	$tracking_code = $options['wustl_GoogleAnalytics_UA_code'];

	$output = "<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  ga('create', '" . $tracking_code . "', 'auto');
		  ga('send', 'pageview');

		</script>";

	echo $output;
}


function gaOutBound(){
	?><script type="text/javascript"><!--
	if (typeof jQuery != 'undefined') {
		jQuery(document).ready(function($) {
			jQuery('a').on('click', function(event) {
				var el = jQuery(this);
				var track = true;
				var href = (typeof(el.attr('href')) !== 'undefined' ) ? el.attr('href') :"";
				//var isThisDomain = href.hostname.match(window.location.hostname);
				var isThisDomain = href.match(document.domain.split('.').reverse()[1] + '.' + document.domain.split('.').reverse()[0]);
				var target = (typeof(el.attr('target')) !== 'undefined' ) ? el.attr('target') :"";

				if (!href.match(/^javascript:/i)) {
					var elEv = []; elEv.value=0;
					if (href.match(/^https?\:/i) && !isThisDomain) {
						elEv.category = "external";
						elEv.action = "click";
						elEv.label = href.replace(/^https?\:\/\//i, '');
						elEv.loc = href;
					}
					else track = false;
					if (track) {
						if (window.__gaTracker){
							__gaTracker( 'send', 'event', elEv.category.toLowerCase(),elEv.action.toLowerCase(),elEv.label.toLowerCase(),elEv.value );
						}
						if (window.ga){
							ga( 'send', 'event', elEv.category.toLowerCase(),elEv.action.toLowerCase(),elEv.label.toLowerCase(),elEv.value );
						}
						if (window.gaplusu){
							gaplusu( 'send', 'event', elEv.category.toLowerCase(),elEv.action.toLowerCase(),elEv.label.toLowerCase(),elEv.value );
							gaplusu( 'single.send', 'event', elEv.category.toLowerCase(),elEv.action.toLowerCase(),elEv.label.toLowerCase(),elEv.valuel );
						}
						if (target == "_blank"){
								window.open(elEv.loc);
						}else{
							setTimeout(function() {window.location.href = elEv.loc;}, 400);
						}
						if (window.console){
							console.log( 'Outbound: ' + elEv.label );
						}
						return false;	
						
					}
				}
			});
		});
	}
	--></script><?php
}
add_action('wp_head','gaOutBound',99);
/*
//is internal?
var isInternal = function(href) {
	var int_flag = '';
  var l = document.createElement("a");
  l.href = href;

	if ( l.hostname == window.location.hostname || l.hostname === '' ) {
  	int_flag = '-int';
  }

	return int_flag;
};


//link binding
$("a").on('click', function() {
	trackEvents( 'outbound-primarynav', $(this).attr("href"), $(this).text() );
});


//branching tracking code events
var trackEvents = function( category, action, label ) {
	if ( category.substring(0, 8) == 'outbound' ) {
		category += isInternal(action);
	}

	// Yoast uses global __gaTracker() function instead of standard ga()
	// https://wordpress.org/support/topic/note-change-of-global-function-ga-_gatracker
	if ( typeof __gaTracker == 'function' ) {
		__gaTracker( 'send', 'event', category, action, label );
	} else if ( typeof ga == 'function' ) {
		ga( 'send', 'event', category, action, label );
	} else if ( typeof gaplusu == 'function' ) { // Check if this is CampusPress, if so, fire an event for both the global and single UA accounts
		gaplusu( 'send', 'event', category, action, label );
		gaplusu( 'single.send', 'event', category, action, label );
	} else if (window.console){
		console.log( 'no analytics detected' );
	}

}

*/
?>