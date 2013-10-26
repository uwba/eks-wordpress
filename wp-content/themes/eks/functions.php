<?php
add_action('wp_enqueue_scripts', 'eks_add_jqueryui_stylesheet');

/**
 * Enqueue plugin style-file
 */
function eks_add_jqueryui_stylesheet() {
    wp_enqueue_style('wp-jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/base/jquery-ui.css', false);
}

add_action('after_setup_theme', 'vantage_child_setup');

function vantage_child_setup() {
    remove_filter('excerpt_length', 'va_excerpt_length');
    add_filter('excerpt_length', 'va_child_excerpt_length');

    function va_child_excerpt_length() {
        return 1;
    }

}

// Hook this function to the init() action so that it gets executed prior to the headers going out.
add_action('init', 'eks_handle_download_document');

/**
 * Export an Excel spreadsheet of the volunteers or tax sites, if requested in the URL.  If the user is an Admin,
 * all results will be shown; otherwise only the ones I own are returned.
 * 
 * @return void
 */
function eks_handle_download_document() {

    class EksExcel {

        /**
         * downloadExcel() - Export a 2-dimensional array out to Excel 2007 spreadsheet (.xlsx) format and pipe it to the browser.
         * In order to prevent mangling, all cells in the spreadsheet will be of datatype "String".
         * 
         * @param	$filename			Filename to save to the browser as
         * @param	$data_array			Array of rows to export
         * @param	$header_array		Optional header array for the first row
         */
        function downloadExcel($filename, $data_array, $header_array = null) {
            // Use this simple regex to clean the filename
            $filename = preg_replace("/[^a-zA-Z0-9-\.]/", "_", $filename);
            $tmpfname = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "export_xlsx" . time();

            require_once ABSPATH . 'wp-content/plugins/volunteer/lib/PHPExcel/Classes/PHPExcel.php';
            require_once ABSPATH . 'wp-content/plugins/volunteer/lib/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $sheet = $objPHPExcel->getActiveSheet();

            $row = 1;
            if ($header_array != null)
                array_unshift($data_array, $header_array);

            for ($i = 0; $i < count($data_array); $i++) {
                for ($j = 0; $j < count($data_array[$i]); $j++) {
                    $this->_writeCell($sheet, $j, $row, $data_array[$i][$j]);
                }
                $row++;
            }

            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save($tmpfname);

            // read it then delete it
            $buffer = file_get_contents($tmpfname);
            unlink($tmpfname);

            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Description: File Transfer');
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header("Expires: 0");
            header("Pragma: public");

            print($buffer);
            exit();
        }

        private function _writeCell($sheet, $column, $row, $value) {
            // If the length is over 15 characters and it's all numbers, then set the string datatype explicitly; otherwise set it using the ordinary method
            if (strlen($value) > 15 && preg_match('/[^\d]/', $value) == 0)
                $sheet->getCellByColumnAndRow($column, $row)->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);
            else
                $sheet->setCellValueByColumnAndRow($column, $row, $value);
        }

    }

    if (strstr($_SERVER["REQUEST_URI"], 'export-volunteers') != null) {

        $volunteers = get_volunteers();

        $arr = array();
        foreach ($volunteers as $el) {

            $meta_values = get_post_meta($el->ID);
            $user = get_user_by('id', $el->post_author);
            if ($user !== false) {
                $row = array(
                    $el->post_date
                );

                $name = '';
                $phone = '';
                $email = '';
                $tax_site = '';
                $tax_site_id = null;
                $training_type = '';
                $positions = array();
                $experience = '';

                $contacted = '';
                $training = false;
                $confirmed = false;
                $ethics = false;
                $basic = false;
                $intermediate = false;
                $specialized = false;
                $volunteered = false;
                $another_site = false;

                //var_dump($meta_values);die();

                foreach (array_keys($meta_values) as $k) {
                    if (in_array($k, array('interpreter', 'greeter', 'preparer', 'screener'))) {
                        $positions[] = $k;
                        $tax_site_id = $meta_values[$k][0];
                    } elseif ($k == 'notes_contacted')
                        $contacted = $meta_values[$k][0];
                    elseif ($k == 'notes_signed_up_for_appropriate_training')
                        $training = $meta_values[$k][0];
                    elseif ($k == 'notes_confirmed_as_my_volunteer')
                        $confirmed = $meta_values[$k][0];
                    elseif ($k == 'notes_certified_in_ethics')
                        $ethics = $meta_values[$k][0];
                    elseif ($k == 'notes_certified_in_basic_level')
                        $basic = $meta_values[$k][0];
                    elseif ($k == 'notes_certified_in_intermediate_level')
                        $intermediate = $meta_values[$k][0];
                    elseif ($k == 'notes_certified_specialized')
                        $specialized = $meta_values[$k][0];
                    elseif ($k == 'notes_volunteered_at_my_site')
                        $volunteered = $meta_values[$k][0];
                    elseif ($k == 'notes_also_volunteers_at_another_vita_site')
                        $another_site = $meta_values[$k][0];
                    elseif ($k == 'training') {
                        $training_type_post = get_post($meta_values['training'][0]);
                        $training_type = $training_type_post->post_title;
                    } elseif ($k == 'name')
                        $name = $meta_values[$k][0];
                    elseif ($k == 'phone')
                        $phone = $meta_values[$k][0];
                    elseif ($k == 'email')
                        $email = $meta_values[$k][0];
                    elseif ($k == 'experience')
                        $experience = $meta_values[$k][0];
                }

                if (!empty($tax_site_id)) {
                    $tax_site_post = get_post($tax_site_id);
                    $tax_site = $tax_site_post->post_title;
                }

                $arr[] = array(
                    $row[0],
                    $name,
                    $phone,
                    $tax_site,
                    $training_type,
                    $contacted ? 'Yes' : 'No',
                    $training ? 'Yes' : 'No',
                    $confirmed ? 'Yes' : 'No',
                    $ethics ? 'Yes' : 'No',
                    $basic ? 'Yes' : 'No',
                    $intermediate ? 'Yes' : 'No',
                    $specialized ? 'Yes' : 'No',
                    $volunteered ? 'Yes' : 'No',
                    $another_site ? 'Yes' : 'No',
                    $email,
                    // Experience is only relevant is "preparer" has been selected
                    in_array('preparer', $positions) ? $experience : '',
                    implode(', ', $positions)
                );
            }
        }
        $header = array(
            'Date Registered',
            'Name',
            'Phone',
            'Tax Site',
            'Training',
            'Contacted Date',
            'Signed Up for Training',
            'Confirmed',
            'Certified Ethics',
            'Certified Basic',
            'Certified Intermediate',
            'Certified Specialized',
            'Volunteered',
            'Volunteers at Another VITA Site',
            'Email',
            'Experience',
            'Positions'
        );
        $e = new EksExcel();
        $e->downloadExcel('EKS Volunteer Export.xlsx', $arr, $header);
    } elseif (strstr($_SERVER["REQUEST_URI"], 'export-sites') != null) {

        $u = wp_get_current_user();
        $arr = array();

        // Inspired by va_get_dashboard_listings()
        $args = array(
            'post_type' => VA_LISTING_PTYPE,
            'post_status' => array('publish', 'pending', 'expired'),
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        );
        if (!eks_is_admin())
            $args['author'] = $u->ID;

        $query = new WP_Query($args);

        foreach ($query->posts as $el) {
            $meta_values = get_post_meta($el->ID);

            $county = get_the_listing_category($el->ID);

            $arr[] = array(
                $el->post_title,
                $meta_values['address'][0],
                $county->name,
                empty($meta_values['app_nearbylandmarks'][0]) ? '' : $meta_values['app_nearbylandmarks'][0],
                empty($meta_values['app_parking'][0]) ? '' : implode(', ', $meta_values['app_parking']),
                empty($meta_values['app_busshuttles'][0]) ? '' : $meta_values['app_busshuttles'][0],
                empty($meta_values['app_closestbartstation'][0]) ? '' : $meta_values['app_closestbartstation'][0],
                empty($meta_values['app_adaaccessible'][0]) ? '' : $meta_values['app_adaaccessible'][0],
                empty($meta_values['app_openingdate'][0]) ? '' : $meta_values['app_openingdate'][0],
                empty($meta_values['app_closingdate'][0]) ? '' : $meta_values['app_closingdate'][0],
                empty($meta_values['app_hoursofoperation'][0]) ? '' : get_formatted_hours_of_operation($meta_values['app_hoursofoperation'][0], "; "),
                empty($meta_values['app_availability'][0]) ? '' : implode(', ', $meta_values['app_availability']),
                empty($meta_values['app_specialcloseddates'][0]) ? '' : $meta_values['app_specialcloseddates'][0],
                empty($meta_values['app_specialinstructions'][0]) ? '' : $meta_values['app_specialinstructions'][0],
                empty($meta_values['app_additionallanguagesspoken'][0]) ? '' : implode(', ', $meta_values['app_additionallanguagesspoken']),
                empty($meta_values['app_otherlanguages'][0]) ? '' : $meta_values['app_otherlanguages'][0],
                empty($meta_values['app_certifyingacceptanceagent'][0]) ? '' : $meta_values['app_certifyingacceptanceagent'][0],
                empty($meta_values['app_specialtaxformsorschedulesprepared'][0]) ? '' : implode(', ', $meta_values['app_specialtaxformsorschedulesprepared']),
                empty($meta_values['app_taxreturnsprocessedforspecificyears'][0]) ? '' : implode(', ', $meta_values['app_taxreturnsprocessedforspecificyears']),
                empty($meta_values['app_sitecoordinatorname'][0]) ? '' : $meta_values['app_sitecoordinatorname'][0],
                empty($meta_values['app_sitecoordinatorphonenumber'][0]) ? '' : $meta_values['app_sitecoordinatorphonenumber'][0],
                empty($meta_values['app_sitecoordinatoremailaddress'][0]) ? '' : $meta_values['app_sitecoordinatoremailaddress'][0],
                empty($meta_values['app_numberoftaxpreparersneeded'][0]) ? '' : $meta_values['app_numberoftaxpreparersneeded'][0],
                empty($meta_values['app_numberofinterpretersneeded'][0]) ? '' : $meta_values['app_numberofinterpretersneeded'][0],
                empty($meta_values['app_numberofgreetersneeded'][0]) ? '' : $meta_values['app_numberofgreetersneeded'][0],
                empty($meta_values['phone'][0]) ? '' : $meta_values['phone'][0],
                empty($meta_values['app_sitetype'][0]) ? '' : $meta_values['app_sitetype'][0],
                empty($meta_values['app_newtaxpreparertrainingrequired'][0]) ? '' : $meta_values['app_newtaxpreparertrainingrequired'][0],
                empty($meta_values['app_returningtaxpreparertrainingrequired'][0]) ? '' : $meta_values['app_returningtaxpreparertrainingrequired'][0]
            );
        }

        $header = array(
            'Tax Site Name',
            'Address',
            'County',
            'Nearby Landmarks',
            'Parking',
            'Bus/Shuttles',
            'Closest Bart Station',
            'ADA Accessible',
            'Opening Date',
            'Closing Date',
            'Hours of Operation',
            'Availabilty ',
            'Special Closed Dates',
            'Special Instructions',
            'Special Languages',
            'Other Languages',
            'Certifying Acceptance Agent',
            'Special tax forms or schedules prepared at your tax site',
            'Tax returns processed for the following years',
            'Site Coordinator Name',
            'Site Coordinator Phone Number',
            'Site Coordinator Email',
            'Number of Tax Preparers needed',
            'Number of Interpreters needed',
            'Number of Greeters needed',
            'Public Phone Number',
            'Site Type',
            'New Tax Preparer Training',
            'Returning Tax Preparer Training'
        );

        $e = new EksExcel();
        $e->downloadExcel('EKS Tax Sites Export.xlsx', $arr, $header);
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

/**
 * Return an array of the tax sites that a given volunteer is assigned to.  In practice this will be an array of at most one element.
 * 
 * @param int $volunteer_user_id     The user ID of the volunteer
 * @return array                Array of Tax Sites, of the form:
 * Array
  (
  [$tax_site_post_id] => Array
  (
  [0] => preparer|greeter|interpreter
  )

  )
 */
function get_volunteer_tax_sites($volunteer_user_id = null) {
    $tax_sites = array();
    $volunteer = get_volunteer($volunteer_user_id);
    if (!empty($volunteer->ID)) {
        $volunteer_meta = get_post_meta($volunteer->ID);

        foreach (array('preparer', 'interpreter', 'screener', 'greeter') as $position) {
            if (!empty($volunteer_meta[$position])) {
                foreach ($volunteer_meta[$position] as $tax_site) {
                    $tax_sites[$tax_site][] = $position;
                }
            }
        }
    }
    return $tax_sites;
}

/**
 * Return all the volunteers associated with Tax Sites of the current user (if not an admin).  If the current user is an admin then all
 * volunteers in the system are returned.
 * 
 * @global object $current_user
 * @global int $user_ID
 * @return array                    Array of posts, each representing a volunteer
 */
function get_volunteers() {
    global $current_user, $user_ID;

    get_currentuserinfo();

    $conditions = array(
        'numberposts' => -1,
        'post_type' => 'listing',
        'post_status' => array('publish', 'pending'),
        'orderby' => 'title',
        'order' => 'ASC'
    );
    if (!eks_is_admin())
        $conditions['author'] = $user_ID;

    $my_tax_sites = get_posts($conditions);
    $my_tax_sites_ids = array();
    foreach ($my_tax_sites as $tax_site) {
        $my_tax_sites_ids[] = $tax_site->ID;
    }

    $arg = array(
        'numberposts' => -1,
        'post_type' => 'volunteer',
        'post_status' => array('publish', 'pending'),
        'meta_query' => array('relation' => 'OR'),
    );

    foreach (array('preparer', 'interpreter', 'screener', 'greeter') as $position) {
        $arg['meta_query'][] = array('key' => $position, 'compare' => 'IN', 'value' => $my_tax_sites_ids);
    }

    return get_posts($arg);
}

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

function OutputArrayToTable($items, $header = null, $i = 1, $no_message = 'No Items Found') {
    ob_start();
    ?>
    <?php if (count($items)): ?>
        <!-- Loop through the entries that were provided to us by the controller -->
        <table class="table">
            <thead>
                <tr>
                    <?php
                    if ($header) {
                        foreach ($header as $title) {
                            echo "<th>$title</th>";
                        }
                    } else {
                        $item = current($items);
                        foreach ($item as $title => $entry) {
                            echo "<th>" . ucwords(str_replace('_', ' ', $title)) . "</th>";
                        }
                    }
                    ?>
                </tr>
            </thead>

            <?
            //$i=1;
            foreach ($items as $row):
                ?>
                <tr>
                    <?
                    foreach ($row as $entry) {
                        echo "<td>$entry</td>";
                    }
                    ?>
                </tr>
            <? endforeach ?>
        </table>


    <?php else: ?>
        <div id="about"><div class="not_found"><?php echo $no_message ?></div></div>
    <?php endif; ?>


    <?
    $output = ob_get_clean();
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
    $output .= '</select>';
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
    $output .= '</select>';
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


add_filter('site_url', 'custom_site_url', 10, 4);

function custom_site_url($url, $path, $scheme, $blog_id) {
//	echo $path;
//    if ( strpos($path, '/volunteer') !== FALSE  && strpos($path, 'action=edit') !== FALSE) {
//		$url = str_replace ('/wp-admin/post.php?post=', 'edit?postid=', $url);
//		$url = str_replace ('&action=edit', '', $url);
//        //	$url = '/register'; // Or do this dynamically
//    }
    return $url;
}

function insert_attachment($file_handler, $post_id, $setthumb = 'false') {
    // check to make sure its a successful upload
    if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK)
        __return_false();

    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');

    $attach_id = media_handle_upload($file_handler, $post_id);

    if ($setthumb)
        update_post_meta($post_id, '_thumbnail_id', $attach_id);
    return $attach_id;
}

//http://www.tomauger.com/2011/web-development/wordpress/wordpress-hiding-menu-items-from-users-based-on-their-roles-using-a-custom-walker
/* Custom Walker to prevent password-protected pages from appearing in the list */
class ZG_Nav_Walker extends Walker_Nav_Menu {

    protected $page_is_visible = true;

    function start_el(&$output, $item, $depth, $args) {

        $this->page_is_visible = true;

        if ($item->post_name == 'volunteer-sign-up') { // This is the menu item for the volunteer-registration page link
            // Since the "Volunteer" main menu only has a single option under it, we cannot hide it or the CSS breaks on IE.  
            // So just rename it and leave it visible
            if (is_user_logged_in()) {
                $item->url = site_url("/dashboard");
                $item->title = __("Volunteer Dashboard");
            }
        }

        if ($item->post_name == 'co-ordinator-registration') { // This is the menu item for the Coordinators link
            if (!is_volunteer()) {
                $item->url = site_url("/dashboard");
                $item->title = __("Coordinator Dashboard");
            }
            else
                $this->page_is_visible = false;
        }

        // If it's not visible, skip the menu item
        if ($this->page_is_visible)
            parent::start_el($output, $item, $depth, $args);
    }

}

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
    for ($i = 0; $i < count($recent_searches);) {
        $html .= '<li><a href="' . $recent_searches[$i] . '">Search ' . (++$i) . '</a></li>';
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

/**
 * Return whether or not the current user is an admin.
 * 
 * @global int $user_ID
 * @return boolean
 */
function eks_is_admin() {
    $ret = false;
    global $user_ID;

    if ($user_ID) {
        if (current_user_can('level_10'))
            $ret = true;
    }
    return $ret;
}

/**
 * Form builder helper
 *
 * @param string $label         Field label
 * @param array $myposts        Optional array of tax sites display in poplist
 * @return none
 */
function fileupload($label, $myposts = array()) {
    ?>
    <form name="uploadfile" id="uploadfile_form" method="POST" enctype="multipart/form-data" action="<?php //echo $this->filepath.'#uploadfile';    ?>" accept-charset="utf-8" >

        <?php if (count($myposts) > 0) { ?>

            <label>Tax Site: <select name="tax_site">
                    <?php foreach ($myposts as $post) {
                        ?><option value="<?php echo $post->ID ?>"><?php echo $post->post_title ?></option>
                    <?php } ?>
                </select>
            </label>
            <p>Uploaded documents will be displayed to volunteers assigned to this Tax Site.</p>
            <br/>
        <?php } ?>

        <label><?php echo $label; ?><input type="file" name="uploadfiles[]" id="uploadfiles" size="35" class="uploadfiles" /></label>
        <input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="Upload"  />
    </form>
    <?php
}

/**
 * Handle file uploads
 *
 * @todo check nonces
 * @todo check file size
 *
 * @return none
 */
function fileupload_process() {
    $uploadfiles = isset($_FILES['uploadfiles']) ? $_FILES['uploadfiles'] : null;

    $response = '';

    if (is_array($uploadfiles)) {

        try {

            foreach ($uploadfiles['name'] as $key => $value) {

                // look only for uploaded files
                if ($uploadfiles['error'][$key] == 0) {

                    $filetmp = $uploadfiles['tmp_name'][$key];

                    //clean filename and extract extension
                    $filename = $uploadfiles['name'][$key];

                    // get file info
                    // @fixme: wp checks the file extension....
                    $filetype = wp_check_filetype(basename($filename), null);
                    $filetitle = preg_replace('/\.[^.]+$/', '', basename($filename));
                    $filename = $filetitle . '.' . $filetype['ext'];

                    $upload_dir = wp_upload_dir();

                    if (!empty($upload_dir['error']))
                        throw new exception($upload_dir['error']);

                    /**
                     * Check if the filename already exist in the directory and rename the
                     * file if necessary
                     */
                    $i = 0;
                    while (file_exists($upload_dir['path'] . '/' . $filename)) {
                        $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
                        $i++;
                    }
                    $filedest = $upload_dir['path'] . '/' . $filename;

                    /**
                     * Check write permissions
                     */
                    if (!is_writeable($upload_dir['path'])) {
                        //$this->msg_e('Unable to write to directory %s. Is this directory writable by the server?');
                        throw new exception($upload_dir['path'] . ' is not writable.');
                    }

                    /**
                     * Save temporary file to uploads dir
                     */
                    if (!@move_uploaded_file($filetmp, $filedest)) {
                        throw new exception($filedest . ' could not be saved.');
                    }

                    $attachment = array(
                        'post_mime_type' => $filetype['type'],
                        'post_title' => $filetitle,
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    if (!empty($_REQUEST['tax_site']))
                        $attachment['post_parent'] = $_REQUEST['tax_site'];

                    $attach_id = wp_insert_attachment($attachment, $filedest);
                    require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                    $attach_data = wp_generate_attachment_metadata($attach_id, $filedest);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                    $response .= '<div class="notice success"><span>' . $filename . ' was uploaded successfully.</span></div>';
                }
            }
        } catch (exception $ex) {
            $response .= '<div class="notice error"><span>' . $ex->getMessage() . '</span></div>';
        }
    }
    return $response;
}

/**
 * Return the "hours of operation" JSON object as a readable string.
 * 
 * @param string $json       JSON string representing the object
 * @param string $separator
 * @return string $str           
 */
function get_formatted_hours_of_operation($json, $separator = "<br/>") {
    $arr = array();
    $obj = json_decode($json, true);
    if (!empty($obj)) {
        foreach (array_keys($obj) as $day) {
            $row = '';
            $slot1 = '';
            $slot2 = '';

            if (!empty($obj[$day]['Start1']) && !empty($obj[$day]['End1']))
                $slot1 = $obj[$day]['Start1'] . ' - ' . $obj[$day]['End1'];
            if (!empty($obj[$day]['Start2']) && !empty($obj[$day]['End2']))
                $slot2 = $obj[$day]['Start2'] . ' - ' . $obj[$day]['End2'];

            if (!empty($slot1) && !empty($slot2))
                $row = "$day: $slot1 and $slot2";
            elseif (!empty($slot1) || !empty($slot2))
                $row = "$day: $slot1$slot2";
            if (!empty($row))
                $arr[] = $row;
        }
    }
    return implode($separator, $arr);
}

/**
 * Send an HTML email to the specified recipient.  Upon failure, the error is written to the log.
 * 
 * @param string $to
 * @param string $from
 * @param string $subject
 * @param string $body          HTML body
 * @param string $cc            Optional email to CC
 * @return bool $success
 */
function eks_mail($to, $from, $subject, $body, $cc = null) {
    $headers = 'From: ' . $from . "\r\n";
    if (!empty($cc))
        $headers .= 'Cc: ' . $cc . "\r\n";
    add_filter('wp_mail_content_type', 'eks_set_html_content_type');
    $success = wp_mail($to, $subject, $body, $headers);
    // reset content-type to to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
    remove_filter('wp_mail_content_type', 'eks_set_html_content_type');
    if (!$success)
        error_log("Could not email to:$to\n from:$from\n subject:$subject\n, cc:$cc");
    return $success;
}

function eks_set_html_content_type() {
    return 'text/html';
}

/**
 * Add filter to notify specified email addresses, when a Listing is saved in their county.
 */
add_filter('va_handle_update_listing', 'eks_notify_site_updated', 100);

function eks_notify_site_updated($listing) {

    // Logic from listing-form.php:va_handle_update_listing()
    $action = !(bool) get_post($_POST['ID']) ? 'added' : 'updated';

    $post_title = get_the_title($listing->ID);
    $post_url = get_permalink($listing->ID);
    $subject = "[" . get_option('blogname') . "] Tax site {$action}";

    $body = "<p>The following tax site has been $action: <a href='" . $post_url . "'>" . $post_title . "</a></p>";

    // This should really be a "no-reply" alias.
    $sender = "\"EarnItKeepItSaveIt!\"<" . get_option('admin_email') . ">";
    
    $county = get_the_listing_category($listing->ID);
    $to_emails = get_option('email_notifications_' . $county->term_id);
    
    if (!empty($to_emails))
    {
        if (!eks_mail($to_emails, $sender, $subject, $body)) {
            // Mailing failed, too bad but continue on
        }
    }
    return $listing;
}

