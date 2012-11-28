Add the directory to your plugin and name it banner

Insert the following code into your master plugin initializer file

// -------------------------------------------------------------------------------------- //
// Plugin Name for Option Note: This would be better in the banner directory
// -------------------------------------------------------------------------------------- //
#	if( !function_exists('pluginchief_get_plugin_name') ){
#		function pluginchief_get_plugin_name() {
#			if ( ! function_exists( 'get_plugins' ) )
#				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
#				$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
#				$plugin_file = basename( ( __FILE__ ) );
#				return strtolower(str_replace(" ", "_", $plugin_folder[$plugin_file]['Name']));
#
#		}
#	}
