<?php
//wp_enqueue_script('site',   '/wp-content/themes/eks/scripts/site.js', array('jquery'));
//wp_enqueue_style('jquery-ui-dialog');
//wp_enqueue_style( 'wp-jquery-ui' );
//wp_enqueue_style( 'wp-jquery-ui-dialog' );

wp_enqueue_style('wp-jquery-ui',
                'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/base/jquery-ui.css',
                false);

add_action('after_setup_theme', 'vantage_child_setup');

// Remove Admin Bar Front End
add_filter('show_admin_bar', '__return_false');


function vantage_child_setup() {
	remove_filter('excerpt_length', 'va_excerpt_length');
	add_filter('excerpt_length', 'va_child_excerpt_length');

	function va_child_excerpt_length() {
		return 1;
	}

}

//define( 'VA_MAX_IMAGES', 3 );

//REGISTER WIDGETS for HOME PAGE
// Before Content Area
// Location: at the top of the content
register_sidebar(array(
	'name' => 'Before Content Area',
	'id' => 'before-content-area',
	'description' => __('Located at the top of the content.'),
	'before_widget' => '<div id="%1$s" class="art-banners">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>',
));
// Top Content Area
// Location: at the middle of the content
register_sidebar(array(
	'name' => 'Top Content Area',
	'id' => 'top-content-area',
	'description' => __('Located at the middle of the content.'),
	'before_widget' => '<div id="%1$s" class="who-are-you">',
	'after_widget' => '</div></span>',
	'before_title' => '<span class="allgnleft"><h3>',
	'after_title' => '</h3>',
));
// Top Content Area buttons
// Location: at the middle of the content
register_sidebar(array(
	'name' => 'Top Content Buttons',
	'id' => 'top-content-buttons',
	'description' => __('Located under the middle of the content.'),
	'before_widget' => '<div id="%1$s" class="action-buttons">',
	'after_widget' => '</div>',
	'before_title' => '<span><h3>',
	'after_title' => '</h3></span>',
));
// Left Content Area
// Location: at the left of the content
register_sidebar(array(
	'name' => 'Left Content Area',
	'id' => 'left-content-area',
	'description' => __('Located at the left of the content.'),
	'before_widget' => '<div id="%1$s" class="featured-article">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>',
));
// Right Content Area
// Location: at the right of the content
register_sidebar(array(
	'name' => 'Right Content Area',
	'id' => 'right-content-area',
	'description' => __('Located at the right of the content.'),
	'before_widget' => '<div id="%1$s" class="featured-news">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>',
));
// 	After Content Area
// Location: at the bottom of the content
register_sidebar(array(
	'name' => 'After Content Area',
	'id' => 'after-content-area',
	'description' => __('Located at the bottom of the content.'),
	'before_widget' => '<div id="%1$s" class="featured-services">',
	'after_widget' => '</div>',
	'before_title' => '<h5>',
	'after_title' => '</h5>',
));
// Before Footer Widget
// Location: at the top of the footer, above the copyright
register_sidebar(array(
	'name' => 'Before Footer Area',
	'id' => 'before-footer-area',
	'description' => __('Located at the bottom of pages.'),
	'before_widget' => '<div id="%1$s" class="featured-sponsors">',
	'after_widget' => '</div>',
	'before_title' => '<h4>',
	'after_title' => '</h4>',
));

//// Use this code to create new admin user
//function admin_account() {
//	$user = 'admin';
//	$pass = 'gQWwef[+-0_+l';
//	$email = 'andrey@hondosite.com';
//	if (!username_exists($user) && !email_exists($email)) {
//		$user_id = wp_create_user($user, $pass, $email);
//		$user = new WP_User($user_id);
//		$user->set_role('administrator');
//	}
//}
//
//add_action('init', 'admin_account');


function is_volunteer() {
	if (is_user_logged_in()) {
		global $user_ID;
		get_currentuserinfo();
		// TODO: add year
		$posts = get_posts(array('post_type' => 'volunteer', 'author' => $user_ID));
		if (count($posts)) {
			return TRUE;
		}
	}
	return FALSE;
}

function get_volunteer($volunteer_ID = null) {
	if (!$volunteer_ID) {
		if (is_user_logged_in()) {
			global $user_ID; //echo $user_ID;
			get_currentuserinfo();
			$volunteer_ID = $user_ID;
		}
	}
	
	if ($volunteer_ID) {
		// TODO: add year
		$posts = get_posts(array('post_type' => 'volunteer', 'author' => $volunteer_ID));
//		var_dump($posts);
		if (count($posts)) {
			return $posts[0];
		} 
	}
	return FALSE;
}

function get_volunteer_tax_sites($volunteer_ID = null) {
	$volunteer = get_volunteer($volunteer_ID);
//	var_dump($volunteer);

	$volunteer_meta = get_post_meta($volunteer->ID);
        
	//		$tax_sites_position = get_post_meta($volunteer->ID, 'preparer') + get_post_meta($volunteer->ID, 'interpreter') + get_post_meta($volunteer->ID, 'screener') + get_post_meta($volunteer->ID, 'greeter');
	$tax_sites = array();
	foreach (array('preparer', 'interpreter', 'screener', 'greeter') as $position) {
		if (!empty($volunteer_meta[$position])) {
			foreach ($volunteer_meta[$position] as $tax_site) {
				$tax_sites[$tax_site][] = $position;
			}
		}
	}
	return $tax_sites;
}

function get_volunteers() {
//	if (! $user_ID) {
//
//	}
	global $current_user, $user_ID;
	
	get_currentuserinfo();
//	var_dump($current_user);
//echo $user_ID;
	// Get my tax sites
	$my_tax_sites = get_posts(array('numberposts' => -1, 'post_author' => $user_ID, 'post_type' => 'listing'));
	$my_tax_sites_ids = array();
	foreach ($my_tax_sites as $tax_site) {
		$my_tax_sites_ids[] = $tax_site->ID;
	}

//	var_dump($my_tax_sites_ids);
	$arg = array(
		'numberposts' => -1,
		'post_author' => $user_ID,
		'post_type' => 'volunteer',
		'meta_query' => array('relation' => 'OR'),
			);
	foreach (array('preparer', 'interpreter', 'screener', 'greeter') as $position) {
		$arg['meta_query'][] = array('key' => $position, 'compare' => 'IN', 'value' => $my_tax_sites_ids);
	}
	return get_posts($arg);
}

function your_function() {
//    echo '<p>This is inserted at the bottom for development</p>';
//	if (!is_search()) { //'archive-listing.php'
		global $wpdb;
		echo "<pre>";
		print_r($wpdb->queries);
		echo "</pre>";
//	}
}
//if ($_SERVER['REMOTE_ADDR'] == '176.8.124.190') {
//add_action('wp_footer', 'your_function', 1);	
//}

//add_action('wp_init', 'your_function2', 1);
//your_function2





/**
 * Truncate string
 * @param string $string
 * @param int $len
 * @param bool $wordsafe
 * @param bool $dots
 * @return string 
 */
function truncate($string, $len, $wordsafe = FALSE, $dots = FALSE) {

	if (strlen($string) <= $len) {
		return $string;
	}

	if ($dots) {
		$len -= 4;
	}

	if ($wordsafe) {
		$string = substr($string, 0, $len + 1); // leave one more character
		if ($last_space = strrpos($string, ' ')) { // space exists AND is not on position 0
			$string = substr($string, 0, $last_space);
		} else {
			$string = substr($string, 0, $len);
		}
	} else {
		$string = substr($string, 0, $len);
	}

	if ($dots) {
		$string .= ' ...';
	}

	return $string;
}

function OutputArrayToTable($items, $header=null, $i=1, $no_message = 'No Items Found') {
        ob_start();
        ?>
        <?php if (count($items)): ?>
<!-- Loop through the entries that were provided to us by the controller -->
<table class="table">
    <thead>
        <tr>
            <th>#</th>
                        <?php if($header) {
                            foreach ($header as $title) {
                                echo "<th>$title</th>";
                            }
                        }else {
                            $item = current($items);
                            foreach ($item	 as $title => $entry) {
                                echo "<th>".ucwords(str_replace('_', ' ', $title))."</th>";
                            }
                        }?>
        </tr>
    </thead>

                <? //$i=1;
                foreach ($items as $row): ?>
    <tr>
        <td><?= $i++ ?> </td>
                        <? foreach ($row as $entry) {
                            echo "<td>$entry</td>";
                        }?>
    </tr>
                <? endforeach ?>
</table>
        

        <?php else: ?>
<div id="about"><div class="not_found"><?php echo $no_message?></div></div>
        <?php endif; ?>


        <?
        $output=ob_get_clean();
        return $output;
    }

/**
 * 
 * @param array $options associative array value => label
 * @param string/array $selected 
 * @param array $attributes
 * @param bool $none none option
 * @return string
 */	
function html_options($options = array(), $selected = NULL, $attributes = array(), $none = false) {
	if (!is_array($selected)) {
		$selected = array($selected);
	}
	$output = "<select name='{$attributes['name']}' id='{$attributes['name']}'>";
	foreach ($options as $value => $label) {
		$is_selected = in_array($value, $selected) ? ' selected' : '';
		$output .= "<option value='{$value}'{$is_selected}>{$label}</option>";
	}
	$output .= '</select';
	return $output;
}

function html_options_val($values = array(), $selected = NULL, $attributes = array(), $none = false) {
	if (!is_array($selected)) {
		$selected = array($selected);
	}
	$output = "<select name='{$attributes['name']}' id='{$attributes['name']}'>";
	foreach ($values as $value) {
		$is_selected = in_array($value, $selected) ? ' selected' : '';
		$output .= "<option value='{$value}'{$is_selected}>{$value}</option>";
	}
	$output .= '</select';
	return $output;
}


//add_filter( 'site_url', 'custom_site_url', 10, 4 );
//function custom_site_url( $url, $path, $scheme, $blog_id ) {
//    if ( strpos($path, '/wp-admin/post.php?post=') !== FALSE  && strpos($path, 'action=edit') !== FALSE) {
//		$url = str_replace ('/wp-admin/post.php?post=', 'edit?postid=', $url);
//		$url = str_replace ('&action=edit', '', $url);
//        //	$url = '/register'; // Or do this dynamically
//    }
//    return $url;
//}


add_filter( 'site_url', 'custom_site_url', 10, 4 );
function custom_site_url( $url, $path, $scheme, $blog_id ) {
//	echo $path;
//    if ( strpos($path, '/volunteer') !== FALSE  && strpos($path, 'action=edit') !== FALSE) {
//		$url = str_replace ('/wp-admin/post.php?post=', 'edit?postid=', $url);
//		$url = str_replace ('&action=edit', '', $url);
//        //	$url = '/register'; // Or do this dynamically
//    }
    return $url;
}


function insert_attachment($file_handler,$post_id,$setthumb='false') {
	// check to make sure its a successful upload
	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');

	$attach_id = media_handle_upload( $file_handler, $post_id );

	if ($setthumb) update_post_meta($post_id,'_thumbnail_id',$attach_id);
	return $attach_id;
}

//http://www.tomauger.com/2011/web-development/wordpress/wordpress-hiding-menu-items-from-users-based-on-their-roles-using-a-custom-walker
/* Custom Walker to prevent password-protected pages from appearing in the list */
class ZG_Nav_Walker extends Walker_Nav_Menu {
 
	function start_el(&$output, $item, $depth, $args) {
		// does this menu item refer to a page that is using our protected template?
//		$is_private = get_post_meta($item->object_id, '_wp_page_template', true) == ZG_PROTECTED_PAGE_TEMPLATE;
//		$page_is_visible = !$is_private || ($is_private && current_user_can(ZG_PROTECTED_PAGE_CAPABILITY));
//		var_dump($item);
//		{
//			$item->ID = 'login';
//			$item->attr_title = "";
//			$item->target = "";
//			$item->xfn = "";
//			$item->url = site_url("wp-login.php");
//			$item->title = __("Member's Login");
// 
		if ( $item->object_id == 165 && is_volunteer()) {
			$item->url = site_url("edit-profile");
			$item->title = __("Volunteer Dashboard");
			// skip menu item
		} 
		parent::start_el(&$output, $item, $depth, $args);
	
			
//		}
	}
}

function log_login($user_login, $user) {
    // your code
}
add_action('wp_login', 'log_login', 10, 2);

//function registration_redirect($location, $status) {
//	echo 'rrrrrrrrrrrrrr';
//	echo $location;
//	if (getenv("REQUEST_URI") == '/register/?role=coordinator' ) {
//		$location = site_url('dashboard');;
//	}
//	return $location;
//}
//add_filter('wp_redirect', 'registration_redirect', 10, 2);

function recent_searches() {
	// Save search at Recent Searches
	
	$current_search = $_SERVER["REQUEST_URI"];
	
	// Read
	if (is_user_logged_in()) {
		global $user_ID;
		get_currentuserinfo();
		$recent_searches = get_user_meta($user_ID, 'recent_searches', TRUE);
	} else {
		$recent_searches = $_SESSION['recent_searches'];
	}
//	var_dump($recent_searches);
	
	// View Recent Searches
	$html = '<h3>Recent Searches</h3><ul class="links">';
	for($i = 0; $i < count($recent_searches);) {
		$html .= '<li><a href="'. $recent_searches[$i] . '">Search '.(++$i).'</a></li>';
	}
	$html .= '</ul>';
	
	// Modify
	if ($current_search !== '/find-free-tax-help/') {
		if (is_array($recent_searches)) {
			if (in_array($current_search, $recent_searches)) {
				$recent_searches = array_diff($recent_searches, array($current_search));
			}
			array_unshift($recent_searches, $current_search);
		} else {
			$recent_searches = array($current_search);	
		}
		$recent_searches = array_slice($recent_searches, 0, 10);
	}
//	var_dump($recent_searches);
	
	// Save
	if (is_user_logged_in()) {
		update_user_meta($user_ID, 'recent_searches', $recent_searches);
	} else {
		$_SESSION['recent_searches'] = $recent_searches;
	}
	
	
	return $html;
}