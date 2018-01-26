<?php
/*
Plugin Name: Views Counter - Pages/Posts
Plugin URI: https://wordpress.org/plugins/view-count
Version: 1.0
Author: Akhtarujjaman Shuvo
Author URI: https://www.facebook.com/suvobd.ml
Description: Simple Plugin for showing the post or page view on Admin Column.no need to add code to theme file.just activate the plugin and enjoy.
*/

//reset the count from database on deactivation
register_deactivation_hook( __FILE__, 'asrvc_plugin_deactivate' );
function asrvc_plugin_deactivate(){
    $count_key = 'post_views_count';
	
	$allposts = get_posts( 'numberposts=-1&post_type=any&post_status=any' );
	 
	foreach( $allposts as $postinfo ) {
		delete_post_meta( $postinfo->ID, 'related_posts' );
		
		delete_post_meta($postinfo->ID,$count_key);
		add_post_meta($postinfo->ID, $count_key, '0');
	}

}

// function to display number of posts.
if(!function_exists('asrvc_getpostviews')){
	function asrvc_getpostviews($postID){
		$count_key = 'post_views_count';
		$count = get_post_meta($postID, $count_key, true);
		if($count==''){
			delete_post_meta($postID, $count_key);
			add_post_meta($postID, $count_key, '0');
			return "0 View";
		}
		return $count.' Views';
	}
}

// function to count views.
if(!function_exists('asrvc_setpostviews')){
	function asrvc_setpostviews($postID) {
		$count_key = 'post_views_count';
		$count = get_post_meta($postID, $count_key, true);
		if($count==''){
			$count = 0;
			delete_post_meta($postID, $count_key);
			add_post_meta($postID, $count_key, '0');
		}else{
			if ( is_singular( 'page' ) ) {
				$count++;
				update_post_meta($postID, $count_key, $count);
			}else{
				$count = $count+1/2;
				update_post_meta($postID, $count_key, $count);
			}
		}
	}
}


// Add views counter to Admin Column 
add_filter('manage_posts_columns', 'asrvc_posts_column_views');
add_action('manage_posts_custom_column', 'asrvc_posts_custom_column_views',5,2);

add_filter('manage_pages_columns', 'asrvc_posts_column_views');
add_action('manage_pages_custom_column', 'asrvc_posts_custom_column_views',5,2);

function asrvc_posts_column_views($defaults_count){
    $defaults_count['post_views'] = __('Views');
    return $defaults_count;
}

function asrvc_posts_custom_column_views($column_name, $id){
	if($column_name === 'post_views'){
		echo asrvc_getpostviews(get_the_ID());
    }
}

// Set post view function inside post/page loop
if(!function_exists('asrvc_the_post_action')){
	function asrvc_the_post_action() {
		asrvc_setpostviews(get_the_ID());
	}
	add_action( 'loop_start', 'asrvc_the_post_action' );
}

