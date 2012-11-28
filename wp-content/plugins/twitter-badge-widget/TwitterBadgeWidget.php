<?php
/**
Plugin Name: PluginChief Twitter Badge Widget
Plugin URI: http://wordpress.org/extend/plugins/twitter-badge-widget/
Description: Show a simple twitter badge with the latest tweets on a web page
Author: Andy Clark, Brandon Camenisch, Jason Bahl
Author URI: http://pluginchief.com/
Version: 1.75
Stable tag: 1.75
License: GPLv2
*/
// -------------------------------------------------------------------- //
//	Constants
// -------------------------------------------------------------------- //
	define('PLUGINCHIEF_TWITTER_WIDGET_URL', plugin_dir_url(__FILE__));
	define('PLUGINCHIEF_TWITTER_WIDGET_PATH', plugin_dir_path(__FILE__));

// -------------------------------------------------------------------------------------- //
// Plugin Name for Option Note: This would be better in the banner directory
// -------------------------------------------------------------------------------------- //
	if( !function_exists('pluginchief_get_plugin_name') ){
		function pluginchief_get_plugin_name() {
			if ( ! function_exists( 'get_plugins' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
				$plugin_file = basename( ( __FILE__ ) );
				return strtolower(str_replace(" ", "_", $plugin_folder[$plugin_file]['Name']));

		}
	}
	add_option( pluginchief_get_plugin_name() , 1, '', 'yes' );

	function pluginchief_deactivate_twitter_widget() {

		delete_option( pluginchief_get_plugin_name() );

	}
	register_deactivation_hook( __FILE__, 'pluginchief_deactivate_twitter_widget' );

// -------------------------------------------------------------------- //
//	Includes
// -------------------------------------------------------------------- //
	require_once 'functions.php';
	require_once 'pluginchief-recycle-banner/banner.php';

