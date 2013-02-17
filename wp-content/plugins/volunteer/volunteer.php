<?
/*
  Plugin Name: Volunteer
  Description: Volunteer registration and management plugin for EKS, heavily reworked by Rolf Kaiser.
  Author: Andrey / Hondosite
  Version: 2.0
 */
if (!session_id())
    session_start();
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
            add_post_meta($post_id, "experience", wp_strip_all_tags($_SESSION['volunteer']['preparer']));

            foreach ($_SESSION['volunteer']['position'] as $position) {
                    add_post_meta($post_id, $position, $_POST['tax_sites']);
            }

            $valid = count($errors) == 0;
            if ($valid) {
                $_SESSION['volunteer']['steps'][4] = 4;
                
                // Email the coordinator of the selected tax site.
                $tax_site = get_post($_POST['tax_sites']);

                $coordinator = get_user_by('id', $tax_site->post_author);

                $to = $coordinator->data->user_email;
                $from_email = 'noreply@' . $_SERVER["HTTP_HOST"];
                $subject = "New Volunteer Registration";
                $message = sprintf("Hello.  The following person has registered to volunteer at your tax site:\r\n\r\n%s \r\n%s \r\n%s \r\n", $_SESSION['volunteer']['name'], $_SESSION['volunteer']['phone'], $_SESSION['volunteer']['email']);
                $headers = "From: {$from_email}\r\n";
                if (!wp_mail($to, $subject, $message, $headers)) {
                    // There was an error sending the email - swallow it and move on.
                }
            }
            $response = json_encode(array(
                'success' => $valid,
                'errors' => $errors,
                'step' => $valid ? 0 : 4,
                'data' => $_SESSION['volunteer'],
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

        function tax_search() {
            $errors = array();

//	$response = json_encode(array( 
//			'success' => !count($errors),
//			'errors' => implode('<br/>', $errors),
//			));
            // response output
            header("Content-Type: text/html");

            global $wpdb;
            global $table_prefix;

            $query = "SELECT p.*, bart.meta_value as bartstations, t.name as county,
        GROUP_CONCAT(DISTINCT l.meta_value) as languages, a.meta_value as address,
        CONCAT_WS('; ', ID, post_title, post_excerpt, post_content, bart.meta_value, t.name, GROUP_CONCAT(DISTINCT l.meta_value), a.meta_value) as search
        FROM {$table_prefix}posts as p
        LEFT JOIN {$table_prefix}postmeta as bart ON ID = bart.post_id AND bart.meta_key = 'app_closestbartstations'
        LEFT JOIN {$table_prefix}postmeta as ada ON ID = ada.post_id AND ada.meta_key = 'app_adaaccessible'
        LEFT JOIN {$table_prefix}postmeta as l ON ID = l.post_id AND l.meta_key = 'app_additionallanguagescheckallthatapply'
        LEFT JOIN {$table_prefix}postmeta as a ON ID = a.post_id AND a.meta_key = 'address'

        LEFT JOIN {$table_prefix}term_relationships ON ID = object_id
        LEFT JOIN {$table_prefix}term_taxonomy ON (
            {$table_prefix}term_relationships.term_taxonomy_id = {$table_prefix}term_taxonomy.term_taxonomy_id 
            AND {$table_prefix}term_taxonomy.taxonomy = 'listing_category'
        )
        LEFT JOIN {$table_prefix}terms AS t USING(term_id) -- for full text search
			
		LEFT JOIN {$table_prefix}postmeta m1 ON m1.meta_key = 'app_numberoftaxpreparersneeded' AND p.ID=m1.post_id 
		LEFT JOIN {$table_prefix}postmeta m2 ON m2.meta_key = 'app_numberofinterpretersneeded' AND p.ID=m2.post_id 
		LEFT JOIN {$table_prefix}postmeta m3 ON m3.meta_key = 'app_numberofgreetersneeded' AND p.ID=m3.post_id 

        WHERE post_status = 'publish' AND post_type = 'listing'
		AND CONVERT((SELECT count(*) c FROM {$table_prefix}postmeta WHERE meta_value=p.ID AND meta_key='preparer'), UNSIGNED INTEGER) <= (CONVERT(m1.meta_value, UNSIGNED INTEGER))
		AND CONVERT((SELECT count(*) c FROM {$table_prefix}postmeta WHERE meta_value=p.ID AND meta_key='interpreter'), UNSIGNED INTEGER) <= (CONVERT(m2.meta_value, UNSIGNED INTEGER))
		AND CONVERT((SELECT count(*) c FROM {$table_prefix}postmeta WHERE meta_value=p.ID AND meta_key='greeter'), UNSIGNED INTEGER) <= (CONVERT(m3.meta_value, UNSIGNED INTEGER))"

//        ." AND meta_key = 'app_closestbartstations'  "
            ;

//    var_dump($_GET);


//    if ($_GET['zip_code']) $query .= " AND a.meta_value like '%" . $wpdb->escape($_GET['zip_code']) . "%'";

            $query .= " GROUP BY ID";

            if ($_GET['search_terms'] && trim($_GET['search_terms']) != 'Search ...') {
                if ($_GET['searchphrase'] != 'exact') {
                    $terms = split(' ', $_GET['search_terms']);
                } else {
                    $terms = array($_GET['search_terms']);
                }
                foreach ($terms as $term) {
                    $term = mysql_real_escape_string($term);
                    $whereclauses[] = "search like '%$term%'";
                }
                if ($_GET['searchphrase'] == 'any') {
                    $cond = 'OR';
                } else { //if 'all'
                    $cond = 'AND';
                }
                $query .= " having (" . implode(" $cond ", $whereclauses) . ")";
            };

            //echo $query;
            $data = $wpdb->get_results($query, 'OBJECT');
            global $post;
//    var_dump($data);
            if (count($data)) {
                foreach ($data as $post) {
                    //	var_dump($item);
                    if (setup_postdata($post)) {
                        //the_post();
                ?>
                <article class="tax-search-dialog" id="post-<?php the_ID(); ?>" <?php //post_class(); ?>><?php
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
                            operation:<?php echo esc_html(get_post_meta(get_the_ID(), 'app_hoursofoperation', true)); ?></p>

                        <p class="listing-coordinator">
                            Coordinator
                            info: <?php echo esc_html(get_post_meta(get_the_ID(), 'app_sitecoordinatorname', true)); ?>
                            <?php echo esc_html(get_post_meta(get_the_ID(), 'app_sitecoordinatorphonenumber', true)); ?>
                            <?php echo esc_html(get_post_meta(get_the_ID(), 'app_sitecoordinatoremailaddress', true)); ?>
                        </p>

                        <p class="listing-lang">Addition
                            languages: <?php echo esc_html(implode(', ', get_post_meta(get_the_ID(), 'app_additionallanguagescheckallthatapply', false))); // the_terms(get_the_ID(), 'listing_tag', 'Languages: ', ' ', '');   ?></p>

                        <p class="listing-transportation">
                            Parking: <?php echo esc_html(implode(', ', get_post_meta(get_the_ID(), 'app_parking', false))); ?>
                            |
                            Transit
                            Agency: <?php echo esc_html(get_post_meta(get_the_ID(), 'app_busshuttlesincludetransitagencyname', true)); ?>
                            |
                            Bart
                            stations: <?php echo esc_html(get_post_meta(get_the_ID(), 'app_closestbartstations', true)); ?>
                        </p>

                        <p class="listing-openclose">
                            Opening/closing
                            dates: <?php echo esc_html(get_post_meta(get_the_ID(), 'app_openingdate01012013', true)); ?> |
                            <?php echo esc_html(get_post_meta(get_the_ID(), 'app_closingdate04152012', true)); ?>
                        </p>
                    </div>
                    <?php ?></article><?php
                } else {
                    //echo 'error';
                }
            }
        } else {
            ?>
        <article class="listing">
            <h2><?php printf(__('Sorry, No Tax Sites Found', APP_TD)); ?></h2>
        </article><?php
    }

    // IMPORTANT: don't forget to "exit"
    exit;
}

