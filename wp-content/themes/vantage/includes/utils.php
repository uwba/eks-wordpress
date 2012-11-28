<?php

// These utility functions can go away at any time. Don't rely on them.

function va_update_post_status( $post_id, $new_status ) {
	wp_update_post( array(
		'ID' => $post_id,
		'post_status' => $new_status
	) );
}

function va_register_sidebar( $id, $name, $description = '' ) {
	register_sidebar( array(
		'id' => $id,
		'name' => $name,
		'description' => $description,
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<div class="section-head"><h3>',
		'after_title' => '</h3></div>',
	) );
}

function va_excerpt_more() {
	return '&hellip;';
}

function va_excerpt_length() {
	return 15;
}

function va_string_ago( $time ) {
	$now = time();
	$diff = time() - strtotime( $time );

	if ( $diff< 60 ) {
		$string = $diff .' second'.( $diff>1?'s':'' ).' ago';
	} elseif ( $diff< 3600 ) {
		$the_diff = round( $diff/60, 0 );
		$string = $the_diff .' minute'.( $the_diff>1?'s':'' ).' ago';
	} elseif ( $diff< 86400 ) {
		$the_diff = round( $diff/60/60, 0 );
		$string = $the_diff .' hour'.( $the_diff>1?'s':'' ).' ago';
	} elseif ( $diff< 604800 ) {
		$the_diff = round( $diff/60/60/24, 0 );
		$string = $the_diff .' day'.( $the_diff>1?'s':'' ).' ago';
	} elseif ( $diff< 2592000 ) {
		$the_diff = round( $diff/60/60/24/7, 0 );
		$string = $the_diff .' week'.( $the_diff>1?'s':'' ).' ago';
	} elseif ( $diff< 31104000 ) {
		$the_diff = round( $diff/60/60/24/30, 0 );
		$string = $the_diff .' month'.( $the_diff>1?'s':'' ).' ago';
	} else {
		$the_diff = round( $diff/60/60/24/30/12, 0 );
		$string = $the_diff .' year'.( $the_diff>1?'s':'' ).' ago';
	}
	return $string;
}

function va_truncate( $string, $limit, $link, $break="", $pad="..." ) {

	// return with no change if string is shorter than $limit
	if ( strlen( $string ) <= $limit ) return $string;

	$string = substr( $string, 0, $limit );

	if ( false !== ( $breakpoint = strrpos( $string, $break ) ) ) {
		$string = substr( $string, 0, $breakpoint );
	}

	return $string . $pad . $link;
}

function va_show_search_query_var( $qv ) {
	echo va_get_search_query_var( $qv );
}

function va_get_search_query_var( $qv ) {
	return stripslashes( esc_attr( trim( get_query_var( $qv ) ) ) );
}

// Fetches geo coordinates from Google Maps, caches and returns them
function va_geocode_address( $listing_id ) {
	$coord = appthemes_get_coordinates( $listing_id, false );
	if ( $coord )
		return $coord;

	$address = get_post_meta( $listing_id, 'address', true );
	if ( empty( $address ) )
		return false;

	list( $region ) = get_theme_support( 'app-geo' );

	$args = array(
		'address' => urlencode($address),
		'sensor' => 'false',
		'region' => $region
	);

	$response = wp_remote_get( add_query_arg( $args, 'http://maps.googleapis.com/maps/api/geocode/json' ) );

	if ( 200 != wp_remote_retrieve_response_code( $response ) )
		return false;

	$results = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( !$results || 'OK' != $results['status'] )
		return false;

	$lat = $results['results'][0]['geometry']['location']['lat'];
	$lng = $results['results'][0]['geometry']['location']['lng'];

	appthemes_set_coordinates( $listing_id, $lat, $lng );

	return appthemes_get_coordinates( $listing_id );
}

function get_blog_page_title() {
	return get_the_title(VA_Blog_Archive::get_id());
}

// temporary workaround for wordpress bug #9296 http://core.trac.wordpress.org/ticket/9296
// Although there is a hook in the options-permalink.php to insert custom settings,
// it does not actually save any custom setting which is added to that page.
function va_enable_permalink_settings() {
    global $new_whitelist_options;

    // save hook for permalinks page
    if ( isset( $_POST['permalink_structure'] ) || isset( $_POST['category_base'] ) ) {
        check_admin_referer('update-permalink');

        $option_page = 'permalink';

        $capability = 'manage_options';
        $capability = apply_filters( "option_page_capability_{$option_page}", $capability );

        if ( !current_user_can( $capability ) )
            wp_die(__('Cheatin&#8217; uh?'));

        // get extra permalink options
        $options = $new_whitelist_options[ $option_page ];

        if ( $options ) {
            foreach ( $options as $option ) {
                $option = trim($option);
                $value = null;
                if ( isset($_POST[$option]) )
                    $value = $_POST[$option];
                if ( !is_array($value) )
                    $value = trim($value);
                $value = stripslashes_deep($value);

				// get the old values to merge
				$db_option=get_option($option);

				if ( is_array($db_option) )
					update_option($option, array_merge( $db_option, $value ));
				else
					update_option($option, $value);

				// flush rewrite rules using a transient
				//set_transient('va_flush_rewrite_rules', 1, 300);
				

            }
            	//Yes, we need to do this now, come back to this and make the transient work in the right timing. 
            	flush_rewrite_rules();
        }

        /**
         *  Handle settings errors
         */
        set_transient('settings_errors', get_settings_errors(), 30);
    }
}

// temporarly use a transient to flush the rewrite rules
function va_check_rewrite_rules_transient() {

	if ( get_transient('va_flush_rewrite_rules') ) {
		delete_transient('va_flush_rewrite_rules');
		flush_rewrite_rules();
	}

}
