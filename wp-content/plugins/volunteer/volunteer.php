<?php
/*
  Plugin Name: Volunteer
  Description: Volunteer registration and management plugin for EKS, heavily reworked by Rolf Kaiser.
  Author: Andrey / Hondosite
  Version: 2.0
 */
if (!session_id())
    session_start();

// Update the new submenus item for Admins, as per http://codex.wordpress.org/Function_Reference/add_submenu_page
add_action('admin_menu', 'eks_update_menu_items');

function eks_update_menu_items() {

    // Remove the old "Add New" submenus under Listings, Volunteers and Training for clarity
    remove_submenu_page('edit.php?post_type=listing', 'post-new.php?post_type=listing');
    remove_submenu_page('edit.php?post_type=volunteer', 'post-new.php?post_type=volunteer');
    remove_submenu_page('edit.php?post_type=training', 'post-new.php?post_type=training');

    add_submenu_page('edit.php?post_type=listing', 'Add New Tax Site', 'Add New Tax Site', 'export', '../coordinators/create-listing');
    add_submenu_page('edit.php?post_type=listing', 'Export All Tax Sites', 'Export All Tax Sites', 'export', '../export-sites');

    add_submenu_page('edit.php?post_type=volunteer', 'Export All Volunteers', 'Export All Volunteers', 'export', '../export-volunteers');
    add_submenu_page('edit.php?post_type=volunteer', 'Email All Volunteers', 'Email All Volunteers', 'administrator', '../email-all');
    add_submenu_page('edit.php?post_type=volunteer', 'Update Volunteer Registration Email', 'Update Volunteer Registration Email', 'administrator', __FILE__, 'eks_settings_page');

    add_submenu_page('edit.php?post_type=training', 'Add New Training', 'Add New Training', 'administrator', '../edit');

    //call register settings function
    add_action('admin_init', 'eks_register_mysettings');
}

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'options.php';

// Redefine user notification function, as per http://wordpress.stackexchange.com/questions/15304/how-to-change-the-default-registration-email-plugin-and-or-non-plugin
if (!function_exists('wp_new_user_notification')) {

    function wp_new_user_notification($user_id, $plaintext_pass = '') {
        $user = new WP_User($user_id);

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);

        $message = sprintf(__('New user registration on %s:'), get_option('blogname')) . "<br>";
        $message .= sprintf(__('Username: %s'), $user_login) . "<br>";
        $message .= sprintf(__('E-mail: %s'), $user_email) . "<br>";

        eks_mail(get_option('admin_email'), get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);

        if (empty($plaintext_pass))
            return;

        $message = get_option('volunteer_email_body');

        // Replace the tokens
        $message = str_replace('[USERNAME]', $user_login, $message);
        $message = str_replace('[PASSWORD]', $plaintext_pass, $message);

        eks_mail($user_email, "\"{$name} via EarnItKeepItSaveIt!\"<{$noreply_email}>", get_option('volunteer_email_subject'), $message);
    }

}

/* Minimal ajax setup */
// http://www.garyc40.com/2010/03/5-tips-for-using-ajax-in-wordpress/
// if both logged in and not logged in users can send this AJAX request,
// add both of these actions, otherwise add only the appropriate one
add_action('wp_ajax_nopriv_myajax-submit', 'myajax_submit');
add_action('wp_ajax_myajax-submit', 'myajax_submit');

/**
 * Handle volunteer registrations via AJAX forms.
 * 
 * @param int $step                 POST param for the step in the wizard.
 * @global type $wpdb
 * @global type $table_prefix
 * @global type $wpdb
 */
function myajax_submit() {

    // get the submitted parameters
    $step = $_POST['step'];
    $valid = FALSE;
    $errors = array();
    global $wpdb;

    switch ($step) {
        case 1: // Submit name, username, password, and email

            $_SESSION['volunteer']['name'] = $wpdb->escape($_POST['name']);
            $_SESSION['volunteer']['phone'] = $wpdb->escape($_POST['phone']);
            $_SESSION['volunteer']['email'] = $wpdb->escape($_POST['email']);
            $_SESSION['volunteer']['email_confirm'] = $wpdb->escape($_POST['email_confirm']);
            $_SESSION['volunteer']['password'] = $wpdb->escape($_POST['password']);
            $_SESSION['volunteer']['password_confirm'] = $wpdb->escape($_POST['password_confirm']);
            $_SESSION['volunteer']['username'] = $wpdb->escape($_POST['username']);

            $_POST['role'] = 'volunteer';
            $user_ID = volunteer_register_new_user($_SESSION['volunteer']['username'], $_SESSION['volunteer']['email']);
            if (is_wp_error($user_ID)) {
                $errors = $user_ID->get_error_messages();
            } else {
                $_SESSION['volunteer']['user_ID'] = $user_ID;
            }

            $_SESSION['volunteer']['steps'][1] = 1;
            $valid = !count($errors);
            $response = json_encode(array(
                'success' => $valid,
                'errors' => $errors,
                'step' => $valid ? 2 : 1,
                'data' => $_SESSION['volunteer']
            ));
            break;

        case 2: // Select volunteer position(s) desired
            $_SESSION['volunteer']['preparer'] = $wpdb->escape(!empty($_POST['preparer']) ? $_POST['preparer'] : '');
            $_SESSION['volunteer']['position'] = !empty($_POST['position']) ? $_POST['position'] : '';

            if (empty($_SESSION['volunteer']['position'][0])) {
                $errors[] = '<strong>ERROR</strong>: Please select a position.';
            }
            $valid = !count($errors);
            $_SESSION['volunteer']['steps'][2] = 2;
            $response = json_encode(array(
                'success' => $valid,
                'errors' => $errors,
                'step' => $valid ? 3 : 2,
                'data' => $_SESSION['volunteer']
            ));
            break;

        case 3: // Select desired Tax Site
            // Create post object for the Volunteer/Tax Site association
            $my_post = array(
                'post_title' => wp_strip_all_tags($_SESSION['volunteer']['name']),
                'post_type' => 'volunteer',
                // 'post_content' => $_POST['post_content'],
                'post_status' => 'publish',
                'comment_status' => 'closed',
                'post_author' => $_SESSION['volunteer']['user_ID'],
            );

            // Insert the post into the database
            $post_id = wp_insert_post($my_post);
            add_post_meta($post_id, "name", wp_strip_all_tags($_SESSION['volunteer']['name']));
            add_post_meta($post_id, "phone", wp_strip_all_tags($_SESSION['volunteer']['phone']));
            add_post_meta($post_id, "email", wp_strip_all_tags($_SESSION['volunteer']['email']));
            add_post_meta($post_id, "experience", wp_strip_all_tags($_SESSION['volunteer']['preparer'])); // preparer|new

            foreach ($_SESSION['volunteer']['position'] as $position) {
                add_post_meta($post_id, $position, $_POST['tax_sites']);
            }

            $valid = count($errors) == 0;
            if ($valid) {
                $_SESSION['volunteer']['tax_site'] = $_POST['tax_sites'];
                $_SESSION['volunteer']['steps'][4] = 4;
                
                /* Look up the list of appropriate trainings:
                 * 1) If the volunteer is a new preparer, find the "new preparer" training for the given tax site.
                 * 2) If the volunteer is a returning preparer, find the "returning preparer" training for the given tax site.
                 * 3) Otherwise show the Link and Learn selection.
                 */
                
                $html = '';
                $is_preparer = in_array('preparer', array_values($_SESSION['volunteer']['position']));
                if ($is_preparer)
                {
                    $html = '';
                    $tax_site = get_post($_POST['tax_sites']);
                    $training_type_to_check = $_SESSION['volunteer']['preparer']; // new or returning
                    $training_type = get_post_meta($tax_site->ID, 'app_' . $training_type_to_check . 'taxpreparertrainingrequired', true);

                    if ($training_type == 'Onsite Training')
                    {
                    // Cloned from page-coordinator-trainings
			$myposts = get_posts(array('numberposts' => -1, 'post_type' => 'training', 'author' => $tax_site->post_author));

			foreach ($myposts as $post) {
				setup_postdata($post);
                                $html .= '<div><input type="radio" name="training" value="'.$post->ID.'"> ' . $post->post_title . '</div>';
			}
                    }
                    elseif ($training_type == 'County Public Training')
                    {
                        $tax_site_county = get_the_listing_category( $tax_site->ID );
                        
                        $query = new WP_Query(array(
                            'post_type' => 'training',
                            'posts_per_page' => -1,
                            'orderby' => 'title',
                            'order' => 'ASC'
                        ));
                        
                        // This is really inefficient but works
			foreach ($query->posts as $post) {
                                $training_county = get_post_meta($post->ID, 'cat', true);
                                if ($tax_site_county->term_id == $training_county)
                                {                                   
                                    $html .= '<div><input type="radio" name="training" value="'.$post->ID.'"> ' . $post->post_title . '</div>';
                                }
			}
                    }
                    else
                    {
                        // Link and Learn - should just be one of these
                        $query = new WP_Query(array(
                            'post_type' => 'training',
                            's' => 'Link and Learn',
                            'posts_per_page' => -1,
                            'orderby' => 'title',
                            'order' => 'ASC'
                        ));

                         foreach ($query->posts as $post) {
				setup_postdata($post);
                                $html .= '<div><input type="radio" name="training" value="'.$post->ID.'"> ' . $post->post_title . '</div>';
			}
                    }
                }
                else
                {
                    $html = '<div>No selection needed; your site coordinator will be in contact with you regarding your training.</div>';
                }
            }

            $response = json_encode(array(
                'success' => $valid,
                'errors' => $errors,
                'step' => $valid ? 4 : 3,
                'data' => $_SESSION['volunteer'],
                'html' => empty($html) ? '' : $html
            ));
            break;

        case 4: // Selected desired Training, so show the details so the volunteer can confirm
            $_SESSION['volunteer']['training'] = $_POST['training'];
            if (empty($_SESSION['volunteer']['training']))
                $html = '<div>Your site coordinator will be in contact with you regarding your training.</div>';
            else      
            {
                $training = get_post($_POST['training']);
                $training_meta = get_post_meta($training->ID);
                $html = "<p style='font-weight:bold'>{$training->post_title}</p>"
                . "<p>{$training->post_content}</p>
                    <p>
                    Address: {$training_meta['address'][0]}<br/>
                    Date(s): {$training_meta['date'][0]}<br/>
                    Times(s): {$training_meta['times'][0]}<br/>
                    Special Instructions: {$training_meta['special_instructions'][0]}<br/>
                    </p>";
            }
            $response = json_encode(array(
                'success' => true,
                'errors' => array(),
                'step' => 5,
                'data' => $_SESSION['volunteer'],
                'html' => $html
            ));
            break;
        
        case 5:
            // Training confirmed, so save it and email the coordinator of the selected tax site.
            if (!empty($_SESSION['volunteer']['training']))
            {
                $volunteer = get_volunteer($_SESSION['volunteer']['user_ID']);
                add_post_meta($volunteer->ID, 'training', $_SESSION['volunteer']['training']);
            }
            //var_dump($volunteer->ID);
            //var_dump($_SESSION['volunteer']['training']);die();
            
            $tax_site = get_post($_SESSION['volunteer']['tax_site']);
            $tax_site_meta = get_post_meta($tax_site->ID);
            $training = get_post($_SESSION['volunteer']['training']);
            $training_meta = get_post_meta($training->ID);

            $coordinator = get_user_by('id', $tax_site->post_author);
            $cc = $coordinator->data->user_email;

            $to = $_SESSION['volunteer']['email'];
            $noreply_email = 'noreply@' . $_SERVER["HTTP_HOST"];
            $subject = "New Volunteer Registration";
            $login_url = 'http://' . $_SERVER['SERVER_NAME'] . '/volunteer';
            $message = "<p>Dear {$_SESSION['volunteer']['name']},</p>
                <p>Thank you for signing up to volunteer with Earn It! Keep It! Save It!  Always feel free to log back in
                at <a href=\"$login_url\">$login_url</a> to access your information.  Here it all is, just in case:</p>

<p>
<strong>My Details</strong><br>
Email: <a href=\"mailto:{$_SESSION['volunteer']['email']}\">{$_SESSION['volunteer']['email']}</a><br/>
Phone: {$_SESSION['volunteer']['phone']}<br/>
</p>

<p>
<strong>My Tax Site</strong><br>
{$tax_site->post_title}<br/>
Coordinator: {$coordinator->data->user_nicename} (<a href=\"mailto:{$coordinator->data->user_email}\">{$coordinator->data->user_email})</a><br/>
Address: {$tax_site_meta['address'][0]}<br/>
Phone: {$tax_site_meta['phone'][0]}<br/>
</p>

<p>
<strong>My Training</strong><br>
{$training->post_title}<br/>
{$training->post_content}<br/>
Address: {$training_meta['address'][0]}<br/>
Date: {$training_meta['date'][0]}<br/>
Times: {$training_meta['times'][0]}<br/>
Special Instructions: {$training_meta['special_instructions'][0]}<br/>
</p>";

            $from = "\"EarnItKeepItSaveIt!\"<{$noreply_email}>";
            $success = eks_mail($to, $from, $subject, $message, $cc);

            $response = json_encode(array(
                'success' => true,
                'errors' => null,
                'step' => 6,
                'data' => $_SESSION['volunteer']
            ));
            break;
        default:
            break;
    }
    header("Content-Type: application/json");
    echo $response;

    // IMPORTANT: don't forget to "exit"
    exit;
}

add_action('init', 'volunteer_init');

function volunteer_init() {
//    create_post_type();
    volunteer_roles(); //
//	flush_rewrite_rules();//
}

//function create_post_type() {
//	register_post_type( 'training',
//		array(
//			'labels' => array(
//				'name' => __( 'Training' ),
//				'singular_name' => __( 'Training' ),
//				'add_new_item' => __('Add New Training'),
//				'edit_item' => __('Edit Training'),
//				'new_item' => __('New Training'),
//				'view_item' => __('View Training'),
//			),
//			'public' => true,
//			'has_archive' => true,
//			'rewrite' => array('slug' => 'training'),
//			'supports' => array('title', 'editor', 'thumbnail'),//, 'custom-fields'
//		)
//	);
//}


register_activation_hook(__FILE__, 'volunteer_activation');

function volunteer_activation() {
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry,
    // when you add a post of this CPT.
    volunteer_init();

    volunteer_roles();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}

function volunteer_roles() {
    remove_role('coordinator');
    // Create roles
    $result = add_role('volunteer', 'Volunteer', array(
        'read' => true, // True allows that capability
        'volunteer_registration' => true,
    ));
    if (null !== $result) {
        //echo 'Yay!  New role created!';
    } else {
        //echo 'Oh... the volunteer role already exists.';
    }

    $result = add_role('coordinator', 'Coordinator', array(
        'read' => true, // True allows that capability
        'edit_posts' => true,
        'edit_published_posts' => true,
        'publish_posts' => true,
        'publish_training' => true,
        'edit_training' => true,
        'edit_publish_training' => true,
        'delete_training' => true,
        'delete_published_training' => true,
        'publish_trainings' => true,
        'edit_trainings' => true,
        'edit_publish_trainings' => true,
//		'delete_posts' => true,
//		'delete_published_posts' => true,
        'edit_listings' => true,
        'edit_published_listings' => true,
        'publish_listings' => true,
        'edit_ai1ec_event' => true,
        'edit_ai1ec_events' => true,
        'delete_ai1ec_event' => true,
        'publish_ai1ec_events' => true,
        'edit_others_ai1ec_events' => false,
        'edit_private_ai1ec_events' => false,
        'edit_published_ai1ec_events' => false,
        'upload_files' => true,
    ));
    if (null !== $result) {
//	    echo 'Yay!  New role created!';
    } else {
//	    echo 'Oh... the coordinator role already exists.';
    }

//	$role = get_role( 'coordinator' );
//	$role->add_cap('edit_posts' );
}

/** Tweak Registration Form * */
/*  Add Role Select */
//add_action('register_form', 'register_role_custom');
function register_role_custom() {
    ?><div class="form-field mti_font_element">
        <label class="mti_font_element">
            Role:
            <select id="role" name="role" tabindex="5">
                <option disabled>Please select role</option>
                <option <?php
                if (isset($_GET['role']) && $_GET['role'] == 'volunteer') {
                    echo 'selected';
                }
                ?> value="volunteer">Volunteer</option>
                <option <?php
                if (isset($_GET['role']) && $_GET['role'] == 'coordinator') {
                    echo 'selected';
                }
                ?> value="coordinator">Coordinator</option>
            </select>
        </label>
    </div><?php
}

/* Process Custom Roles */
add_action('user_register', 'custom_user_role');

function custom_user_role($user_id, $password = "", $meta = array()) {


    $role = empty($_REQUEST['role']) ? 'volunteer' : $_REQUEST['role'];
    $userdata = array();
    $userdata['ID'] = $user_id;
    $userdata['role'] = $role;

    if ($role == "coordinator" || strpos(getenv("HTTP_REFERER"), 'role=coordinator') !== FALSE) {
        $userdata['role'] = "coordinator";
    } else {
        $userdata['role'] = "volunteer";
    }

//	$data = date('Y-m-d--h-i-s') . ' ' . getenv("HTTP_REFERER") . ' ' . getenv("REQUEST_URI") . " = {$userdata['role']}\n\n";
//	file_put_contents('registration.log', $data, FILE_APPEND);


    if (in_array($userdata['role'], array('volunteer', 'coordinator'))) {
        wp_update_user($userdata);
    }
}

/** Save LatLng for Gmap * */
add_action('save_post', 'add_gmap');

function add_gmap($post_id) {

    if (!empty($_REQUEST['address'])) {
        $address = urlencode($_REQUEST['address']);

        $url = "http://maps.googleapis.com/maps/api/geocode/json?address={$address}&sensor=false";
        $curl = curl_init($url);
        //  $cookieJar = 'cookies.txt';
        //  curl_setopt($this->curl, CURLOPT_COOKIEJAR, $cookieJar);
        //  curl_setopt($this->curl, CURLOPT_COOKIEFILE, $cookieJar);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 25);
        //  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
        $result = curl_exec($curl);
        $result = json_decode($result);
        //  var_dump($result->results[0]->geometry->location);exit;
        if (isset($result->results[0]->geometry->location->lat)) {
            $lat = $result->results[0]->geometry->location->lat;
            update_post_meta($post_id, 'lat', $lat);
            //	  echo $lat;
        }
        if (isset($result->results[0]->geometry->location->lng)) {
            $lng = $result->results[0]->geometry->location->lng;
            update_post_meta($post_id, 'lng', $lng);
            //	  echo $lng;
        }
    }
}

// require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
register_activation_hook(__FILE__, 'volunteer_install');

//add_action('wp_footer', 'your_function'); // test
//add_action('publish_post', 'unreadcounter_publish_post' );//trash_post , delete_post , publish_page , // delete/add categories from post
//add_action('the_post', 'unreadcounter_view_single_post');
//
function volunteer_install() {
    global $wpdb;
//	    $table = $wpdb->prefix."volunteer";
//	    $structure = "CREATE TABLE $table (
//	        volunteer_id INT(9) NOT NULL AUTO_INCREMENT,
//	        volunteer_name VARCHAR(80) NOT NULL,
//	        volunteer_email VARCHAR(80) NOT NULL,
//	        volunteer_year INT(9) DEFAULT 0,
//			volunteer_register INT(9) DEFAULT 0,
//			volunteer_level VARCHAR(20) NOT NULL,
//		UNIQUE KEY id (volunteer_id)
//	    );";
//	    $wpdb->query($structure);
}

//
//
//function volunteer_deinstall() {
//    delete_option('unreadcounter_period');
//    delete_option('unreadcounter_latest_articles');
//}


function volunteer_register_new_user($user_login, $user_email) {
    $errors = new WP_Error();

    $sanitized_user_login = sanitize_user($user_login);
    $user_email = apply_filters('user_registration_email', $user_email);

    // Check the username
    if ($sanitized_user_login == '') {
        $errors->add('empty_username', __('<strong>ERROR</strong>: Please enter a username.'));
    } elseif (!validate_username($user_login)) {
        $errors->add('invalid_username', __('<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.'));
        $sanitized_user_login = '';
    } elseif (username_exists($sanitized_user_login)) {
        $errors->add('username_exists', __('<strong>ERROR</strong>: This username is already registered, please choose another one.'));
    }

    // Check the e-mail address
    if ($user_email == '') {
        $errors->add('empty_email', __('<strong>ERROR</strong>: Please type your e-mail address.'));
    } elseif (!is_email($user_email)) {
        $errors->add('invalid_email', __('<strong>ERROR</strong>: The email address isn&#8217;t correct.'));
        $user_email = '';
    } elseif (email_exists($user_email)) {
        $errors->add('email_exists', __('<strong>ERROR</strong>: This email is already registered, please choose another one.'));
    } elseif ($_SESSION['volunteer']['email'] != $_SESSION['volunteer']['email_confirm']) {
        $errors->add('email_confirm', __('<strong>ERROR</strong>: Confirm Email is not same.'));
    }

    // Check the password
    if ($_SESSION['volunteer']['password'] == '') {
        $errors->add('empty_password', __('<strong>ERROR</strong>: Please type your password.'));
    } elseif ($_SESSION['volunteer']['password'] != $_SESSION['volunteer']['password_confirm']) {
        $errors->add('password_confirm', __('<strong>ERROR</strong>: Confirm password is not same.'));
    }

    // Check the Username
    if ($_SESSION['volunteer']['name'] == '') {
        $errors->add('empty_name', __('<strong>ERROR</strong>: Please type your name.'));
    }


//	// Check captcha
//	if (function_exists( 'cptch_check_custom_form' ) && cptch_check_custom_form() !== true) {
//		$errors->add('wrong_captcha', __( '<strong>ERROR</strong>: Please complete the CAPTCHA.'));
//	}

    do_action('register_post', $sanitized_user_login, $user_email, $errors);




    $errors = apply_filters('registration_errors', $errors, $sanitized_user_login, $user_email);

    if ($errors->get_error_code())
        return $errors;

    $user_pass = wp_generate_password(12, false);
    $user_pass = $_SESSION['volunteer']['password'];
    $user_id = wp_create_user($sanitized_user_login, $user_pass, $user_email);

    if (!$user_id) {
        $errors->add('registerfail', sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_option('admin_email')));
        return $errors;
    }

    update_user_option($user_id, 'default_password_nag', true, true); //Set up the Password change nag.

    wp_new_user_notification($user_id, $user_pass);

    $userdata = array(
        'ID' => $user_id,
        'user_nicename' => $_SESSION['volunteer']['name']
    );
    wp_update_user($userdata);

    $user = get_userdata($user_id);
    $login_data['user_login'] = $user->name;
    $login_data['user_password'] = $user_pass;
    $login_data['remember'] = TRUE;
    $user_verify = wp_signon($login_data, true);

    return $user_id;
}

include 'coordinator.php';


add_action('before_content', 'error_message');

function error_message() {
    if (is_user_logged_in()) {
        global $current_user;
        get_currentuserinfo();
        if ($current_user->roles[0] == 'volunteer') {
            if (!is_volunteer()) {
                echo '<div class="notice success">
<span>You\'re almost finished with registration! <a href="' . site_url('volunteer-registration') . '">Select a tax site</a> to volunteer at...</span></div>';
            }
        }
    }
}

add_action('wp_ajax_nopriv_tax_search', 'tax_search');
add_action('wp_ajax_tax_search', 'tax_search');

/**
 * Find Tax Sites to volunteer at - called in Step 3 of the volunteer registration wizard.
 * 
 * @global type $wpdb
 * @global type $table_prefix
 * @global type $post
 * @return $html                    HTML fragment showing a list of excerpted Tax Sites matching the search.
 */
function tax_search() {
    $errors = array();

    // response output
    header("Content-Type: text/html");

    global $wpdb;
    global $table_prefix;

    $conditions_string = '';
    if ($_GET['search_terms'] && trim($_GET['search_terms']) != 'Search ...') {
        $terms = split(' ', $_GET['search_terms']);
        $conditions = array();
        foreach ($terms as $term) {
            $term = mysql_real_escape_string($term);
            $conditions[] = " (p.post_title LIKE '%$term%' 
                OR p.post_excerpt LIKE '%$term%' 
                OR p.post_content LIKE '%$term%' 
                OR bart.meta_value LIKE '%$term%' 
                OR t.name LIKE '%$term%' 
                OR a.meta_value LIKE '%$term%' ) ";
        }
        $conditions_string = 'AND (' . implode(' OR ', $conditions) . ')';
    };

    $query = "SELECT p.*, 
            bart.meta_value AS bartstations, 
            t.name AS county,
            a.meta_value AS address 
        FROM {$table_prefix}posts as p
        LEFT JOIN {$table_prefix}postmeta as bart ON ID = bart.post_id AND bart.meta_key = 'app_closestbartstations'
        LEFT JOIN {$table_prefix}postmeta as a ON ID = a.post_id AND a.meta_key = 'address'

        LEFT JOIN {$table_prefix}term_relationships ON ID = object_id
        LEFT JOIN {$table_prefix}term_taxonomy ON (
            {$table_prefix}term_relationships.term_taxonomy_id = {$table_prefix}term_taxonomy.term_taxonomy_id 
            AND {$table_prefix}term_taxonomy.taxonomy = 'listing_category'
        )
        LEFT JOIN {$table_prefix}terms AS t USING(term_id) 
        WHERE post_status = 'publish' AND post_type = 'listing'
        $conditions_string
        GROUP BY ID";

    $data = $wpdb->get_results($query, 'OBJECT');
    $volunteer_position = $_SESSION['volunteer']['position'][0];
    
    global $post;
    if (count($data)) {
        foreach ($data as $post) {
            if (setup_postdata($post)) {
                
                // Screeners are always eligible; other positions may not be.
                $tax_site_available = true;
                if ($volunteer_position == 'preparer')
                    $tax_site_available = get_post_meta(get_the_ID(), 'app_numberoftaxpreparersneeded', true) > 0;
                if ($volunteer_position == 'greeter')
                    $tax_site_available = get_post_meta(get_the_ID(), 'app_numberofgreetersneeded', true) > 0;
                if ($volunteer_position == 'interpreter')
                    $tax_site_available = get_post_meta(get_the_ID(), 'app_numberofinterpretersneeded', true) > 0;
                
                if ($tax_site_available)
                {
                ?>
                <article class="tax-search-dialog" id="post-<?php the_ID(); ?>" <?php //post_class();   ?>><?php
                    //get_template_part('content-listing');
                    // TODO: USE select + join instead get_post_meta

                    $lat = get_post_meta(get_the_ID(), 'lat', true);
                    $lng = get_post_meta(get_the_ID(), 'lng', true);
                    $T = get_the_title();
                    $T = $T[0];
                    if ($lat && $lng) {
                        $center = $lat . ',' . $lng;
                    } else {
                        $center = esc_html(get_post_meta(get_the_ID(), 'address', true));
                    }
                    ?>
                    <div class="result-item">
                        <div id="map-<?php the_ID(); ?>" class="map"><img
                                src="http://maps.googleapis.com/maps/api/staticmap?center=<?php print $center; ?>&zoom=13&size=160x160&maptype=roadmap&markers=color:red%7Clabel:<?php echo $T; ?>%7C<?php echo $center; ?>&sensor=false"
                                title="Click to explain"/></div>
                        <!--  -->
                        <script type="text/javascript">
                            jQuery(document).ready(function($) {
                                $('#map-<?php the_ID() ?>').click(function() {
                                    var lat = '<?php print $lat; ?>';
                                    var lng = '<?php print $lng; ?>';
                                    if (!(lat && lng)) {
                                        codeAddress('<?= $center ?>', 'map-<?php the_ID() ?>');
                                    } else {
                                        initializeMap(lat, lng, 'map-<?php the_ID() ?>');
                                    }
                                });
                            });
                        </script>

                        <button class="tax_sites" name="tax_sites" value="<?php the_ID(); ?>">SELECT THIS SITE</button>
                        <h2><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

                        <p class="listing-cat"><?php the_listing_category(); ?></p>

                        <p class="listing-phone">
                            Phone: <?php echo esc_html(get_post_meta(get_the_ID(), 'phone', true)); ?></p>

                        <p class="listing-address">Address: <?php the_listing_address(); ?></p>

                        <p class="listing-hours">Hours of
                            operation:<br/><?php echo get_formatted_hours_of_operation(get_post_meta(get_the_ID(), 'app_hoursofoperation', true)); ?></p>

                        <p class="listing-coordinator">
                            Coordinator
                            info: <?php echo esc_html(get_post_meta(get_the_ID(), 'app_sitecoordinatorname', true)); ?>
                            <?php echo esc_html(get_post_meta(get_the_ID(), 'app_sitecoordinatorphonenumber', true)); ?>
                            <?php echo esc_html(get_post_meta(get_the_ID(), 'app_sitecoordinatoremailaddress', true)); ?>
                        </p>

                        <p class="listing-lang">Addition
                            languages: <?php echo esc_html(implode(', ', get_post_meta(get_the_ID(), 'app_additionallanguagesspoken', false))); ?></p>

                        <p class="listing-transportation">
                            Parking: <?php echo esc_html(implode(', ', get_post_meta(get_the_ID(), 'app_parking', false))); ?>
                            |
                            Transit
                            Agency: <?php echo esc_html(get_post_meta(get_the_ID(), 'app_busshuttles', true)); ?>
                            |
                            Bart
                            stations: <?php echo esc_html(get_post_meta(get_the_ID(), 'app_closestbartstation', true)); ?>
                        </p>

                        <p class="listing-openclose">
                            Opening/closing
                            dates: <?php echo esc_html(get_post_meta(get_the_ID(), 'app_openingdate', true)); ?> |
                            <?php echo esc_html(get_post_meta(get_the_ID(), 'app_closingdate', true)); ?>
                        </p>
                        <p>
                            ADA Accessible: <?php echo get_post_meta(get_the_ID(), 'app_adaaccessible', true) ?>
                        </p>
                        <p>
                            Number of Tax Preparers, Interpreters, and Greeters needed: <?php echo get_post_meta(get_the_ID(), 'app_numberoftaxpreparersneeded', true) ?> |  <?php echo get_post_meta(get_the_ID(), 'app_numberofinterpretersneeded', true) ?> | <?php echo get_post_meta(get_the_ID(), 'app_numberofgreetersneeded', true) ?>
                        </p>
                    </div>
                    <?php ?></article><?php
                }
            } else {
                //echo 'error';
            }
        }
    } else {
        ?>
        <article class="listing">
            <h2><?php printf(__('Sorry, no Tax Sites were found.', APP_TD)); ?></h2>
        </article><?php
    }

    // IMPORTANT: don't forget to "exit"
    exit;
}