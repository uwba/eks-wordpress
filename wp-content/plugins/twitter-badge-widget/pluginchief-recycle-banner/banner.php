<?php
function pluginchief_load_custom_wp_admin_style() {

		# Define Variables
		global $current_user;
		$pluginUrl   = WP_PLUGIN_URL . "/plugindir/phpfile.php";

		# Register Styles
		wp_register_style( 'pluginchief_banner_wp_admin_css', plugin_dir_url(__FILE__) . 'banner.css', false, '1.0.0' );
		wp_enqueue_style( 'pluginchief_banner_wp_admin_css' );

		# Register Scripts
		wp_register_script( 'pluginchief-banner-js', plugin_dir_url(__FILE__) . 'banner.js', NULL ) ;
		wp_enqueue_script( 'pluginchief-banner-js' );


		wp_localize_script( 'pluginchief-banner-js', 'jsFileVariables', array( 'useremail' => $current_user->user_email ) );
}
add_action( 'admin_enqueue_scripts', 'pluginchief_load_custom_wp_admin_style' );
add_action('admin_print_scripts', 'pluginchief_load_custom_wp_admin_style');

// -------------------------------------------------------------------- //
// Main Banner Function
// -------------------------------------------------------------------- //
	# Update the option to not display
	if (is_admin() && isset($_POST['Submit'])) {
  	update_option( pluginchief_get_plugin_name(), 2 );
  }

	$getOption = get_option( pluginchief_get_plugin_name() );
	if( !function_exists('pluginchief_banner_admin_notice') && $getOption == 1 ){
	# Create the function
	function pluginchief_banner_admin_notice(){
		# Define the Globals
		global $current_user;
		# Get user info
		get_currentuserinfo();
		#Display the Banner
		echo '<div class="updated pluginchief-plugin-admin-message pluginchief-plugin-admin-connect">
			    <p>Hi, <u>' . $current_user->display_name . '</u> we really hope you <strong>love</strong> using this plugin! Because it\'s been revived through the PluginChief Plugin Recycle program. We take plugins that aren\'t getting any love and make them new again. Don\'t worry all your favorite plugins will still be available on the Wordpress repository.<br>
			    <br>
			    <em>Thanks from everyone on the PluginChief Team!</em></p><br>
			    <a href="#" id="pluginchief-plugin-admin-connect-learn-more" class="button submit pluginchief-plugin-admin-connect-button">Learn More ⬇</a><br>

			    <div id="pluginchief-hidden-form" class="pluginchief-hidden-form">
			        <div id="pluginchief-lists-container">
			            <ul class="pluginchief-list-one">
			                <li>
			                    <h2>Get Updates From PluginChief</h2>

			                    <p>If you would like to receive updates and offers from PluginChief Please simply click the button below and you will be added to our newsletter. Theres no obligation and PluginChief is awesome so why wouldn\'t you want to hear from us.<br>
			                    <a id="pluginchief-post-subscribe" class="button submit pluginchief-plugin-admin-connect-button" href="#">Get Updates From PluginChief</a></p>
			                </li>
			            </ul>

			            <ul class="pluginchief-list-two">
			                <li><img src="'.plugin_dir_url(__FILE__).'updates.png" class="img-margin"></li>
			            </ul>
			        </div>

			        <div id="pluginchief-lists-container-two">
			            <ul class="pluginchief-list-one">
			                <li>
			                    <h2>Recycle your plugin</h2>

			                    <p>There is no arguing that the WordPress plugin repository is negatively affected by out-of-date and abandoned plugins. It is totally understandable from a developers point of view why so many plugins get abandoned, we get busy and don’t have the time to spend on support and updates. The obvious down side though is that there are typically tens of thousands to hundreds of thousands of users who depend on that plugin to work in one way or another, and every plugin that is neglected hurts the overall WordPress Community. That is why PluginChief has started the Plugin Recycling Program. If you know of any plugins that you wish were still active, or you are a author and don’t have the time and don’t want to leave users hanging, contact us through this form. We will look over every plugin submitted and do whatever we can to address it, but we can not guarantee we can get to every plugin and offer it on PluginChief. Also, if you are an author of a paid plugin but don’t want to keep it up anymore, let us know, we also do profit sharing.<br>
			                    <a href="http://pluginchief.com/recycle/" target="_blank" class="button submit pluginchief-plugin-admin-connect-button">Recycle your plugin</a></p>
			                </li>
			            </ul>

			            <ul class="pluginchief-list-two">
			                <li><img src="'.plugin_dir_url(__FILE__).'recycle.png" class="img-margin"></li>
			            </ul>
			        </div>
			        <form method="post" action="'.$_SERVER['PHP_SELF'].'">
			        <input type="submit" class="close" name="Submit" value="Don\'t Show me this anymore">
			        </form>

			    </div>
			</div>';
		} # End Function pluginchief_banner_admin_notice
		add_action('admin_notices', 'pluginchief_banner_admin_notice');

		} # End If