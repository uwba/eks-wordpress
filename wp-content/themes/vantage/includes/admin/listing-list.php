<?php

// Listing columns
add_filter( 'manage_' . VA_LISTING_PTYPE . '_posts_columns', 'va_listing_manage_columns', 11 );
add_action( 'manage_' . VA_LISTING_PTYPE . '_posts_custom_column', 'va_listing_add_column_data', 10, 2 );
add_filter( 'manage_edit-' . VA_LISTING_PTYPE . '_sortable_columns', 'va_listing_columns_sort' );


function va_listing_manage_columns( $columns ) {
	$comments = $columns['comments'];
	$date = $columns['date'];

	unset($columns['date']);
	unset($columns['comments']);
	if ( !empty( $_GET['post_status'] ) && VA_LISTING_PTYPE == $_GET['post_type'] && 'pending-claimed' == $_GET['post_status'] ) {
		unset($columns['author']);
		$columns['claimee'] = __( 'Claimee', APP_TD );
	}
	
	$columns['expire'] = __( 'Expire Date', APP_TD );
	$columns['comments'] = $comments;
	$columns['date'] = $date;
	$columns['thumbnail'] = '';
	return $columns;
}

function va_listing_columns_sort($columns) {
	$columns['expire'] = 'expire';
	$columns['tax_listing_category'] = 'listing_category';
	return $columns;
}

function va_listing_add_column_data( $column_index, $post_id ) {
	switch ( $column_index ) {
	case 'expire' :
		$expiration_date = va_get_listing_exipration_date( $post_id );
		echo mysql2date( get_option('date_format'), $expiration_date );
		break;
	case 'thumbnail' :
		the_listing_thumbnail( $post_id );
		break;
	case 'claimee' :
		echo va_get_the_author_listings_link( get_post_meta( $post_id, 'claimee', true ) );
		break;
		
	}
}

