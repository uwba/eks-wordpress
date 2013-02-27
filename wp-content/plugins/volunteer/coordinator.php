<?php

// if both logged in and not logged in users can send this AJAX request,
// add both of these actions, otherwise add only the appropriate one
add_action( 'wp_ajax_nopriv_email-all', 'email_all' );
add_action( 'wp_ajax_email-all', 'email_all' );

//add_action( 'wp_ajax_edit_training', 'edit_training');
//
//function edit_training() {
//	$errors = array();
//	
//	
//	$response = json_encode(array( 
//			'success' => !count($errors),
//			'errors' => implode('<br/>', $errors),
//			));
//	// response output
//	header( "Content-Type: application/json" );
//
//	echo $response;
//
//	// IMPORTANT: don't forget to "exit"
//	exit;
//}


add_action( 'save_post', 'custom_listing_save', 10, 2);
function custom_listing_save( $post_id, $post ){
	if ($post->post_type == 'listing' && isset($_REQUEST['email'])) {
		$email = sanitize_email($_REQUEST['email']);
		if (is_email($email)) {
			update_post_meta($post_id, 'email', $email);
		}
        
    }
}