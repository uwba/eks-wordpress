<?php

// if both logged in and not logged in users can send this AJAX request,
// add both of these actions, otherwise add only the appropriate one
add_action( 'wp_ajax_nopriv_email-all', 'email_all' );
add_action( 'wp_ajax_email-all', 'email_all' );

 
function email_all() {
	$errors = array();
	if (!isset($_POST['subject']) ||empty($_POST['subject'])) {
		$errors[] = 'Please enter subject';
	}
	if (!isset($_POST['message']) ||empty($_POST['message'])) {
		$errors[] = 'Please enter message';
	}
	if (!isset($_POST['volunteers']) || !count($_POST['volunteers'])) {
		$errors[] = 'Please select at least one volunteer';
	}

	if (!count($errors)) {
		// Validate volunteers - if user hack form
		$volunteers = get_volunteers();
		$volunteers_ids = array();
		foreach ($volunteers as $volunteer) {
			$volunteers_ids[] = $volunteer->post_author;
		}

		global $current_user;
		get_currentuserinfo();
		$name = $current_user->user_login;
		$email = 'noreply@' . $_SERVER["HTTP_HOST"];
		$subject = $_POST['subject'];
		$message = $_POST['message'];
		$headers = "From: \"{$name}\"<{$email}>\r\n";

		foreach ($volunteers as $volunteer) {
			if (in_array($volunteer->post_author, $_POST['volunteers'])) {
				set_current_user($volunteer->post_author);
				get_currentuserinfo();
				$to = "\"{$current_user->display_name}\"<{$current_user->user_email}>";
				$message = str_replace('!user', $current_user->display_name, $message);
				if (!wp_mail($to, $subject, $message, $headers)) {
					$errors[] = htmlentities("Message to {$to} is failed");
				}
			}
		}


	}

	$response = json_encode(array( 
			'success' => !count($errors),
			'errors' => implode('<br/>', $errors),
			));
	// response output
	header( "Content-Type: application/json" );

	echo $response;

	// IMPORTANT: don't forget to "exit"
	exit;
}


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
	$data = date('Y-m-d--h-i-s') . " save post {$post_id} " . serialize($post) . "\n\n";
	file_put_contents('registration.log', $data, FILE_APPEND);
    //wp_mail( 'example@example.com', $post->post_title, 'This post was posted' );
	if ($post->post_type == 'listing' && isset($_REQUEST['email'])) {
		$email = sanitize_email($_REQUEST['email']);
		if (is_email($email)) {
			update_post_meta($post_id, 'email', $email);
		}
        
    }
}