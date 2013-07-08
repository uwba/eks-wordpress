<?php
/*
Plugin Name: Soliloquy Preview Addon
Plugin URI: http://soliloquywp.com/
Description: Enables preview support for the Soliloquy for WordPress plugin.
Author: Thomas Griffin
Author URI: http://thomasgriffinmedia.com/
Version: 1.2.1
License: GNU General Public License v2.0 or later
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

/*
	Copyright 2012  Thomas Griffin  (email : thomas@thomasgriffinmedia.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/** Load all of the necessary class files for the plugin */
spl_autoload_register( 'Tgmsp_Preview::autoload' );

/**
 * Init class for the Preview Addon for Soliloquy.
 *
 * @since 1.0.0
 *
 * @package Tgmsp-Preview
 * @author Thomas Griffin <thomas@thomasgriffinmedia.com>
 */
class Tgmsp_Preview {

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Holds a copy of the main plugin filepath.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private static $file = __FILE__;

	/**
	 * Current version of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '1.2.1';

	/**
	 * Constructor. Hooks all interactions into correct areas to start
	 * the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		self::$instance = $this;

		/** Run a hook before the slider is loaded and pass the object */
		do_action_ref_array( 'tgmsp_preview_init', array( $this ) );

		register_activation_hook( __FILE__, array( $this, 'activation' ) );

		/** Load the plugin in the init hook (set high priority to make sure it loads after Soliloquy is fully loaded) */
		add_action( 'init', array( $this, 'init' ), 100 );

	}

	/**
	 * Activation hook. Checks to make sure that the main Soliloquy for
	 * WordPress plugin is active before proceeding.
	 *
	 * @since 1.0.0
	 */
	public function activation() {

		/** If the Tgmsp class doesn't exist, deactivate ourself and die */
		if ( ! class_exists( 'Tgmsp' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( 'The Soliloquy for WordPress plugin must be active before you can activate this plugin.', 'soliloquy-preview' ) );
		}

	}

	/**
	 * Loads the plugin updater and all the actions and
	 * filters for the class.
	 *
	 * @since 1.0.0
	 *
	 * @global array $soliloquy_license Soliloquy license data
	 */
	public function init() {

		/** Load the plugin textdomain for internationalizing strings */
		load_plugin_textdomain( 'soliloquy-preview', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		/** Setup the license checker */
		global $soliloquy_license;

		/** Only process update if a key has been entered and updates are on */
		if ( is_admin() ) :
			if ( isset( $soliloquy_license['license'] ) ) {
				$args = array(
					'remote_url' 	=> 'http://soliloquywp.com/',
					'version' 		=> $this->version,
					'plugin_name'   => 'Soliloquy Preview Addon',
					'plugin_slug' 	=> 'soliloquy-preview',
					'plugin_path' 	=> plugin_basename( __FILE__ ),
					'plugin_url' 	=> WP_PLUGIN_URL . '/soliloquy-preview',
					'time' 			=> 43200,
					'key' 			=> $soliloquy_license['license']
				);

				/** Instantiate the automatic plugin updater class */
				$tgmsp_preview_updater = new Tgmsp_Updater( $args );
			}

			/** Instantiate all the necessary components of the plugin */
			$tgmsp_preview_admin 	= new Tgmsp_Preview_Admin();
			$tgmsp_preview_ajax		= new Tgmsp_Preview_Ajax();
			$tgmsp_preview_assets	= new Tgmsp_Preview_Assets();
			$tgmsp_preview_strings	= new Tgmsp_Preview_Strings();
		endif;

	}

	/**
	 * PSR-0 compliant autoloader to load classes as needed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classname The name of the class
	 * @return null Return early if the class name does not start with the correct prefix
	 */
	public static function autoload( $classname ) {

		if ( 'Tgmsp_Preview' !== mb_substr( $classname, 0, 13 ) )
			return;

		$filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . str_replace( '_', DIRECTORY_SEPARATOR, $classname ) . '.php';
		if ( file_exists( $filename ) )
			require $filename;

	}

	/**
	 * Helper method to determine if Soliloquy is inactive or not.
	 *
	 * @since 1.0.0
	 *
	 * @global string $pagenow The current page slug
	 * @return bool True if Soliloquy is not active, false otherwise
	 */
	public static function soliloquy_is_not_active() {

		global $pagenow;

		return ! ( class_exists( 'Tgmsp' ) ) && ! ( isset( $_GET['action'] ) && 'do-plugin-upgrade' == $_GET['action'] || 'plugin-editor.php' == $pagenow && isset( $_REQUEST['file'] ) && preg_match( '|^soliloquy|', $_REQUEST['file'] ) || 'update-core.php' == $pagenow );

	}

	/**
	 * Getter method for retrieving the object instance.
	 *
	 * @since 1.0.0
	 */
	public static function get_instance() {

		return self::$instance;

	}

	/**
	 * Getter method for retrieving the main plugin filepath.
	 *
	 * @since 1.0.0
	 */
	public static function get_file() {

		return self::$file;

	}

}

/** Instantiate the init class */
$tgmsp_preview = new Tgmsp_Preview();