<?php
/*
Plugin Name: Easy Sign Up
Plugin URI: http://www.beforesite.com/plugins/easy-sign-up-features
Description: E-mail Sign Up and Redirection to a url. Possible use are collecting email addresses for a newsletter, or leads for your sales force before redirecting to a brochure. Use the following short code in your pages and posts <code>[easy_sign_up title="Put in your own title here"]</code> Or add the form via a <a href="widgets.php">Widget</a>. Change the options under Easy Sign Up: <a href="admin.php?page=esu_options_pg">Easy Sign Up</a>
Author: Andrew @ Geeenville Web Design
Version: 3.1.3
Author URI: http://www.beforesite.com
License: GPLv2 or later
*/
if (!function_exists ('add_action')){
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
/**
 * Register Globals
 * */
$plugin_loc = plugin_dir_url( __FILE__ );
$plugname = "Easy Sign Up";
$plug_shortname = "easy_sign_up";
$the_web_url = home_url();
$the_blog_name = get_bloginfo('name');
$the_default_email = get_bloginfo('admin_email');
$esu_no_placeholder = false; /* setting this to true gets ride of the placeholder in the inputs see EsuForms::esu_if_ie_input_js() */
if ( preg_match( '/^https/', $plugin_loc ) && !preg_match( '/^https/', home_url() ) )
	$plugin_loc = preg_replace( '/^https/', 'http', $plugin_loc );
/**
 * Define Globals
 * */
define( 'ESU_FRONT_URL', $plugin_loc );
define( 'ESUDS', DIRECTORY_SEPARATOR );
define( 'ESU_URL',          plugin_dir_url(__FILE__) );
define( 'ESU_PATH',         plugin_dir_path(__FILE__) );
define( 'ESU_BASENAME',     plugin_basename( __FILE__ ) );
define( 'ESU_WEB_URL',      $the_web_url );
define( 'ESU_NAME',         $plugname );
define( 'ESU_S_NAME',       $plug_shortname );
define( 'ESU_DEFAULT_EMAIL',$the_default_email );
define( 'ESU_VERSION', '3.1.3' );
define( 'ESU_PREFIX' , "esu_");
/**
 * WP_BLOG_NAME & WP_URL
 * used by easy-sign-up/lib/esu-admin-class.php
 **/
if ( ! defined('WP_BLOG_NAME') )
  define( 'WP_BLOG_NAME', $the_blog_name );
if ( ! defined('WP_URL') )
  define( 'WP_URL', $the_web_url );

/**
 * Load included files
 **/
include 'lib'.ESUDS.'esu-utility-functions.php';
/* Class files */
include 'lib'.ESUDS.'esu-admin-class.php';
include 'lib'.ESUDS.'esu-widget-class.php';
include 'lib'.ESUDS.'esu-front-end-class.php';
include 'lib'.ESUDS.'esu-form-process-class.php';
/* Spam or Ham */
include 'lib'.ESUDS.'esu-akismet.php';

/**
 * Create a new instances for our classes
 **/
$esu_admin    = new EsuAdmin();
$esu_forms    = new EsuForms();
if (isset($_REQUEST['esu_qv'])) {
  $esu_process  = new EsuProcess();
}

/* activation and deactivation */
/**
 * Run on activation 
 * */
register_activation_hook( __FILE__, 'esu_activate' );

function esu_activate()
{
  // preload the default options in to the database
  $defaults = esu_options_array(); 
  foreach ($defaults as $default_array) {
    extract($default_array);
    add_option( $id, $std, '', 'no');
  } // end first loop
  // Set transient - TODO Remove this in next update
  $esu_set_transient = __("<b>Important Message:</b> This update will changes the way Easy Sign Up's Extras work! This message will self destruct in 60 seconds.", 'esu_lang');
  set_transient( 'esu_update_message', $esu_set_transient, 2 * MINUTE_IN_SECONDS);
}
#add_action('admin_notices', 'esu_get_transient_message');
function esu_get_transient_message()
{
  if ( ! current_user_can('activate_plugins') ) return;
  $esu_update_message = get_transient( 'esu_update_message' );
  if (false  === $esu_update_message) return;

  $left_link_text = __("Please have a look at the upgrade info page", 'esu_lang');
  $left_link = '<a href="http://www.beforesite.com/easy-sign-up-update-to-v3">'.$left_link_text.'</a>';
  echo ("<div id='esu-message' class='updated'><p>$esu_update_message <span style='float:right'>$left_link</span></p></div>");
}

/**
 * Removes the esu options from the database 
 * @since 3.0
 **/
register_deactivation_hook(__FILE__, 'esu_deactivate');
function esu_deactivate()
{
  delete_transient( 'esu_update_message');
  if (get_option('easy_sign_up_delete_settings') != true) return; // don't delete
  // remove the plugin's default options from the database
  $defaults = esu_options_array(); //was EsuAdmin::esu_options_array();
  foreach ($defaults as $default_array) {
    extract($default_array);
    delete_option( $id );
  } // end first loop
}
// load translations - if any
function esu_init() {
  load_plugin_textdomain( 'esu_lang', false, dirname( plugin_basename( __FILE__ ) )  . '/languages/' );  
}
add_action('plugins_loaded', 'esu_init', 99999);
