<?php
/**
 * /lib/esu-form-process-class.php
 * easy-sign-up
 * Created by Rew Rixom on November 7, 2012
 * @since 3.0
 **/
if (!class_exists("EsuProcess")) {
	class EsuProcess
	{
		function __construct(){
			add_action('init', array($this,'esu_spam_check'));
			// use the hook defined in function esu_spam_check() 
			add_action('esu_hook_process_email', array('EsuProcess','esu_process_email'),10,3);
			add_action('esu_hook_send_responder_email', array('EsuProcess','esu_send_responder_email'),10,4);
		}

		function esu_spam_check(){
			$esu_check_nonce = self::esu_check_nonce();
			$esu_post_vars = $esu_check_nonce;

			$use_akismet = get_option('easy_sign_up_use_askismet');

			if(!esuAkismet::esu_has_akismet() OR $use_akismet == false) {
				$is_spam = false;
				$has_akismet = false;
			}else{
				$is_spam = esuAkismet::is_akismet_spam( $esu_post_vars );
				$has_akismet = true;
			}
			// add hooks for use by extras or plugins
			do_action( 'esu_hook_before_process_email', $esu_post_vars);
			do_action( 'esu_hook_process_email', $esu_post_vars,$is_spam,$has_akismet);
		}

		/**
		* Process the form
	 	* **/
		function esu_process_email($esu_post_vars,$is_spam,$esu_has_akismet){
			if(!is_array($esu_post_vars)) wp_die(__('sorry nothing was sent - please use the back button and try again','esu_lang'), __('sorry nothing was sent, try again','esu_lang'));
			if ($is_spam!==false) return false; // this is Spam 
			// set variables
			// Set err message.
			$esu_error_message = __('Error: please fill out all the required fields','esu_lang');
			extract($esu_post_vars);
			
			if(isset($extra_fields) && !empty($extra_fields)) {
				$extra=self::esu_process_extra_fields($extra_fields);
			}else{
				$extra='';
			}
			// err checking
			if( !isset($name) || $name == "")
				wp_die($esu_error_message, __('Please fill out your name','esu_lang'));
			if( !isset($email) || $email == "")
				wp_die($esu_error_message, __('Please fill out your email address','esu_lang'));
			$phone = (isset($phone)) ? __('Phone:','esu_lang') ."\n{$phone}\n" : null ;
			$label = (isset($label)) ? $label : "Easy Sign Up Form";

			// Get options
			$easy_sign_up_co_email = get_option('easy_sign_up_co_email'); // Admin's Email address
			$easy_sign_up_co_from_email = get_option('easy_sign_up_co_from_email'); // Automated Reply Email
			$easy_sign_up_thank_you_email = stripslashes_deep(get_option('easy_sign_up_thank_you_email')); // The thank you email content
			$easy_sign_up_thank_you_email = str_replace( "#fullname#",$name, $easy_sign_up_thank_you_email );
			$easy_sign_up_url = get_option('easy_sign_up_url'); // where we need to send them
			// This should not be necessary as we load the default options on activation
			// however if the option is deleted by the user we need a fall back
			if( !$easy_sign_up_url || trim($easy_sign_up_url) == "" ):
				$easy_sign_up_url = WP_URL;
			endif;
			
			$from = 'From: '.get_bloginfo('name').' <'.$easy_sign_up_co_from_email.'>' . "\r\n";

			if(self::esu_check_email_address($email))
			{
				$admin_message = "$name ( $email ) ".__('signed up and been redirected to','esu_lang')." $easy_sign_up_url $phone $extra";
				$admin_subject = get_bloginfo('name').": ".$label;
				$subject = __( "Email confirmation from",'esu_lang' )." ".get_bloginfo('name');
				// send admin email
				wp_mail ($easy_sign_up_co_email, $admin_subject, $admin_message, $from);
				$esu_responder_email = do_action( 'esu_hook_send_responder_email', $email, $subject, $easy_sign_up_thank_you_email, $from );
				// redirect
				wp_redirect($easy_sign_up_url); exit;
			}else{
				wp_die($esu_error_message);
			}
		} // end esu_process_email

		public function esu_post_vars(){
			$esu_post_vars = $_REQUEST;
			if (!isset($esu_post_vars['esu_formID'])) return false;
			$esu_f_id = $esu_post_vars['esu_formID'].'_';
			$esu_form_defaults = array('lama','_wp_http_referer','formID','label','permalink','fname','lname','name','email','phone');
			$ret_arr = array(); // return array
			$extra_fields = array();
			foreach ($esu_post_vars as $key => $value) {
				if($key!='esu_send_bnt'):
					if($key!='esu_qv'):
						$k = trim( str_replace( array($esu_f_id,'esu_'), array(null,null), $key ) );
						if(in_array($k, $esu_form_defaults)) { 
							$ret_arr[$k] = $value;
						}else{
							$extra_fields[$k] = $value;
						}
					endif;
				endif;
			}
			
			if( !isset($ret_arr['fname']) ){
				$a = explode(' ', $ret_arr['name'] );
				$count = count($a);
				if($count == 2){
					$ret_arr['fname'] = $a['0'];
					$ret_arr['lname'] = $a['1'];
				}elseif ($count == 1 || $count > 2 ) {
					$ret_arr['fname'] = false;
					$ret_arr['lname'] = false;
				}
			}

			$ret_arr['name'] =  (isset($ret_arr['name'])) ? $ret_arr['name'] : $ret_arr['fname'].' '.$ret_arr['lname'];
			$ret_arr['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
      $ret_arr['referer'] = $_SERVER['HTTP_REFERER'];
      $ret_arr['user_ip'] = $_SERVER['REMOTE_ADDR'];
      $ret_arr['comment_author_IP'] = preg_replace( '/[^0-9., ]/', '', $ret_arr['user_ip'] );
      if(!empty($extra_fields)) $ret_arr['extra_fields'] = $extra_fields;
			return $ret_arr;
		}

		// Validate for folks with no JS
		function esu_check_email_address($email){
		  if(function_exists('is_email')){
		    return is_email($email); // a WordPress function. is_email() is located in wp-includes/formatting.php
		  }else{
		    return true;
		  }
		}

		// first line of defense in the Spam war
		function esu_check_nonce(){
			$esu_post_vars = self::esu_post_vars();
			$esu_failed_message_vars  = __('Failed Security Check: nothing was sent','esu_lang');
			$esu_failed_message_nonce = __('Failed Nonce Security Check','esu_lang');
			if (!$esu_post_vars) wp_die($esu_failed_message_vars);
			extract($esu_post_vars);
			if ( !wp_verify_nonce($lama, "{$formID}_esu_nonce") ) wp_die($esu_failed_message_nonce);
			return $esu_post_vars;
		}

		/* Moved the email to the user out of the process function so it can be affected by a hook */
		function esu_send_responder_email($email, $subject, $easy_sign_up_thank_you_email, $from){
			// send auto responder
			add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
			$esu_body = apply_filters('the_content', $easy_sign_up_thank_you_email);
			$esu_return = wp_mail ($email, $subject, $esu_body, $from); 
			add_filter('wp_mail_content_type',create_function('', 'return "text/plain"; '));
			return $esu_return;
		}

		/* Pocessing  */
		function esu_process_extra_fields($extra_fields)
		{
			$extra = null;
			foreach ($extra_fields as $key => $value) {
				if (is_array($key)) {
					$extra = self::esu_process_extra_fields($key);
				}else{
					$extra .= "\n{$key}: {$value}\n";
				}
			}
			return $extra;
		}

	} /* End Class */
}
