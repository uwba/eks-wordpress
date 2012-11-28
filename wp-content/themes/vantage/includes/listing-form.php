<?php

add_action( 'wp_loaded', 'va_handle_listing_form' );

add_action( 'va_listing_validate_fields', 'va_validate_listing_title' );
add_action( 'va_listing_validate_fields', 'va_validate_listing_category' );

add_filter( 'va_handle_update_listing', 'va_validate_update_listing' );

add_action( 'appthemes_notices', 'va_listing_error_notice' );


function va_handle_listing_form() {
	global $va_options;

	if ( !isset( $_POST['action'] ) || ( 'new-listing' != $_POST['action'] && 'edit-listing' != $_POST['action'] ) )
		return;

	if ( !current_user_can( 'edit_listings' ) )
		return;

	check_admin_referer( 'va_create_listing' );

	$listing = va_handle_update_listing();
	if ( ! $listing ) {

		// there are errors, redirect the user to the edit or create listing form
		if ( 'edit-listing' == $_POST['action'] ) {
			wp_redirect( $_REQUEST['_wp_http_referer'] );
			exit;
		}
		// return to current page
		return;
	}
	
	if( _va_needs_purchase( $listing ) ){

		wp_redirect( va_get_listing_purchase_url( $listing->ID ) );

	}else{

		if( _va_needs_publish( $listing ) ){
			va_maybe_publish_listing( $listing->ID );
		}

		wp_redirect( get_permalink( $listing->ID ) );
	}
	exit;
}

function va_handle_update_listing() {

	$listing_cat = va_get_listing_cat_id();

	$args = wp_array_slice_assoc( $_POST, array( 'ID', 'post_title', 'post_content', 'tax_input' ) );

	$errors = apply_filters( 'va_listing_validate_fields', va_get_listing_error_obj() );
	if( $errors->get_error_codes() ){
		return false;
	}

	$args['post_type'] = VA_LISTING_PTYPE;

	if ( !(bool) get_post( $_POST['ID'] ) ) {
		$listing_id = wp_insert_post( $args );
	} else {
		$listing_id = wp_update_post( $args );
	}

	$tags = va_get_listing_tags($args['tax_input'][VA_LISTING_TAG]);

	wp_set_object_terms( $listing_id, $tags, VA_LISTING_TAG );
	
	wp_set_object_terms( $listing_id, (int) $listing_cat, VA_LISTING_CATEGORY );

	foreach ( va_get_listing_contact_fields() as $field ) {
		update_post_meta( $listing_id, $field, strip_tags( _va_get_initial_field_value( $field ) ) );
	}

	va_update_form_builder( $listing_cat, $listing_id );

	appthemes_set_coordinates( $listing_id, $_POST['lat'], $_POST['lng'] );

	va_handle_files( $listing_id, $listing_cat );

	return apply_filters('va_handle_update_listing', get_post( $listing_id) );
}

// validates the listing data and returns the post if there are no errors. In case of errors, returns false
function va_validate_update_listing( $listing ) {

	$errors = va_get_listing_error_obj();
	if ( $errors->get_error_codes( )) {
		set_transient('va-errors', $errors );
		$listing = false;
	}

	return $listing;
}

function _va_needs_purchase( $listing ){
	global $va_options;
	return _va_needs_publish( $listing ) && $va_options->listing_charge;
}

function _va_needs_publish( $listing ){
	return in_array( $listing->post_status, array( 'draft', 'expired' ));
}

function _va_is_claimable( $listing_id = '' ) {
	$listing_id = !empty( $listing_id ) ? $listing_id : get_the_ID();
	
	$claimable = get_post_meta( $listing_id, 'listing_claimable', true );
	
	if ( empty( $claimable ) ) return false;
	
	return true;
}

function va_validate_listing_title( $errors ){

	$args = wp_array_slice_assoc( $_POST, array( 'ID', 'post_title', 'post_content', 'tax_input' ) );
	if ( empty( $args['post_title'] ) ) 
		$errors->add( 'no-title', 'No title was submitted.' );
	
	return $errors;

}

function va_validate_listing_category( $errors ){

	$listing_cat = va_get_listing_cat_id();
	if ( !$listing_cat ) 
		$errors->add( 'wrong-cat', 'No category was submitted.' );
	
	return $errors;

}

function va_update_form_builder( $listing_cat, $listing_id ) {
	$fields = va_get_fields_for_cat( $listing_cat );

	$to_update = scbForms::validate_post_data( $fields );

	scbForms::update_meta( $fields, $to_update, $listing_id );
}

function va_get_listing_cat_id() {
	static $cat_id;

	if ( is_null( $cat_id ) ) {
		if ( isset( $_REQUEST[VA_LISTING_CATEGORY] ) && $_REQUEST[VA_LISTING_CATEGORY] != -1 ) {
			$listing_cat = get_term( $_REQUEST[VA_LISTING_CATEGORY], VA_LISTING_CATEGORY );
			$cat_id = is_wp_error( $listing_cat ) ? false : $listing_cat->term_id;
		} else {
			$cat_id = false;
		}
	}

	return $cat_id;
}

function va_get_listing_tags($tags_string) {
	$tags_array = array();

	foreach(explode(',',$tags_string) as $tag){
		$tags_array[] = trim($tag);
	}

	return $tags_array;
}

function the_listing_tags_to_edit( $listing_id ) {
	$tags = get_the_terms( $listing_id, VA_LISTING_TAG );

	if ( empty( $tags ) )
		return;

	echo esc_attr( implode( ', ', wp_list_pluck( $tags, 'name' ) ) );
}

function va_get_default_listing_to_edit() {
	require ABSPATH . '/wp-admin/includes/post.php';

	$listing = get_default_post_to_edit( VA_LISTING_PTYPE );

	$listing->category = va_get_listing_cat_id();

	foreach ( array( 'post_title', 'post_content' ) as $field ) {
		$listing->$field = _va_get_initial_field_value( $field );
	}

	foreach ( va_get_listing_contact_fields() as $field ) {
		$listing->$field = _va_get_initial_field_value( $field );
	}

	return $listing;
}

function _va_get_initial_field_value( $field ) {
	return isset( $_POST[$field] ) ? stripslashes( $_POST[$field] ) : '';
}

function va_get_existing_listing_to_edit() {
	$listing = get_queried_object();

	$listing->category = get_the_listing_category( $listing->ID )->term_id;

	foreach ( va_get_listing_contact_fields() as $field ) {
		$listing->$field = get_post_meta( $listing->ID, $field, true );
	}

	return $listing;
}

function va_get_listing_contact_fields() {
	return array( 'phone', 'address', 'website', 'twitter', 'facebook' );
}

function va_get_listing_error_obj(){

	static $errors;

	if ( !$errors ){
		$errors = new WP_Error();
	}

	return $errors;

}

function va_listing_error_notice() {

	$errors = va_get_listing_error_obj();
	if ( ! $errors )
		return;

	// look for transient errors and merge them if they exist
	$transient_errors = get_transient('va-errors');
	if ( $transient_errors && $transient_errors->get_error_codes() ) {
		$errors->errors = array_merge( $errors->errors, $transient_errors->errors );
		delete_transient('va-errors');
	}

	$map = array(
		'no-title' => __( 'The listing must have a title.', APP_TD ),
		'wrong-cat' => __( 'The selected category does not exist.', APP_TD ),
	);

	foreach( $errors->get_error_messages() as $message )
		appthemes_display_notice( 'error', $message );
}
