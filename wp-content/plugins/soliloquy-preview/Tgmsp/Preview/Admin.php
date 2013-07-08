<?php
/**
 * Admin class for the Soliloquy Preview Addon.
 *
 * @since 1.0.0
 *
 * @package	Soliloquy Preview
 * @author	Thomas Griffin
 */
class Tgmsp_Preview_Admin {

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	
		self::$instance = $this;
	
		add_action( 'admin_init', array( $this, 'deactivation' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enable_preview' ) );
	
	}
	
	/**
	 * Deactivate the plugin if Soliloquy is not active and update the recently
	 * activate plugins with our plugin.
	 *
	 * @since 1.0.0
	 */
	public function deactivation() {
		
		/** Don't deactivate when doing a Soliloquy update or when editing Soliloquy from the Plugin Editor */
		if ( Tgmsp_Preview::soliloquy_is_not_active() ) {
			$recent = (array) get_option( 'recently_activated' );
			$recent[plugin_basename( Tgmsp_Preview::get_file() )] = time();
			update_option( 'recently_activated', $recent );
			deactivate_plugins( plugin_basename( Tgmsp_Preview::get_file() ) );
		}
		
	}
	
	/**
	 * Adds the Soliloquy preview metabox to the Soliloquy edit screen.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
	
		add_meta_box( 'soliloquy_preview_settings', Tgmsp_Preview_Strings::get_instance()->strings['preview_settings'], array( $this, 'preview_settings' ), 'soliloquy', 'normal', 'low' );
	
	}
	
	/**
	 * Callback function for the Soliloquy preview metabox.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object
	 */
	public function preview_settings( $post ) {
	
		/** Always keep security first */
		wp_nonce_field( 'soliloquy_preview_settings', 'soliloquy_preview_settings' );
		
		do_action( 'tgmsp_preview_before_settings_table', $post );
		
		?>
		<p><?php echo Tgmsp_Preview_Strings::get_instance()->strings['settings_desc']; ?></p>
		<p><strong><?php echo Tgmsp_Preview_Strings::get_instance()->strings['preview_note']; ?></strong></p>
		<a href="#" id="soliloquy-preview" class="button-secondary" title="<?php echo esc_attr( Tgmsp_Preview_Strings::get_instance()->strings['preview_slider'] ); ?>"><?php echo esc_html( Tgmsp_Preview_Strings::get_instance()->strings['preview_slider'] ); ?></a>
		<?php 
		
		do_action( 'tgmsp_preview_before_setting_theme', $post ); 
		
		echo '<div id="soliloquy-preview-wrap">';
			soliloquy_slider( $post->ID );
		echo '</div>';
		
		do_action( 'tgmsp_preview_after_settings', $post );
	
	}
	
	/**
	 * If we are in the preview area, output enabler scripts for Soliloquy and any necessary addons.
	 *
	 * @since 1.0.0
	 */
	public function enable_preview() {
	
		/** Only load on add/edit screens for Soliloquy */
		if ( $this->is_preview_area() ) {
			/** We must load assets here in order for the preview functionality to work on newly created instances */
			wp_enqueue_script( 'soliloquy-script' );
			wp_enqueue_style( 'soliloquy-style' );
			
			if ( version_compare( Tgmsp::get_instance()->version, '1.3.0', '>=' ) )
				wp_enqueue_script( 'soliloquy-fitvids' );
			
			/** Provide a hook for addons to load their assets as well */
			do_action( 'tgmsp_preview_assets' );
			
			/** Load function to print script enablers */
			add_action( 'admin_print_footer_scripts', array( $this, 'print_enablers' ) );
		}
	
	}
	
	/**
	 * Enables preview support by instantiating the necessary scripts to get the slider
	 * and other addons running.
	 *
	 * @since 1.0.0
	 *
	 * @global array $soliloquy_data Array of Soliloquy slider data
	 */
	public function print_enablers() {
	
		global $soliloquy_data;
		
		/** This addon can't be activated without Soliloquy, so we know this class/method will be available */
		Tgmsp_Shortcode::slider_script();
		
		/** Only load if we are at 1.3.0 or greater of Soliloquy and have a video in the slider */
		if ( version_compare( Tgmsp::get_instance()->version, '1.3.0', '>=' ) && ( isset( $soliloquy_data[0]['youtube'] ) && $soliloquy_data[0]['youtube'] || isset( $soliloquy_data[0]['vimeo'] ) && $soliloquy_data[0]['vimeo'] ) )
			Tgmsp_Shortcode::video_script();
		
		/** Provide a hook for addons to print their enabler/init scripts */
		do_action( 'tgmsp_preview_enablers', $soliloquy_data );
	
	}
	
	/**
	 * Helper function to determine if we are in a preview area.
	 *
	 * @since 1.0.0
	 *
	 * @global object $current_screen The current screen object
	 * @return bool True if in preview area, false otherwise
	 */
	public static function is_preview_area() {
	
		global $current_screen;
			
		if ( 'soliloquy' == $current_screen->post_type && 'post' == $current_screen->base )
			return true;
			
		return false;
	
	}
		
	/**
	 * Getter method for retrieving the object instance.
	 *
	 * @since 1.0.0
	 */
	public static function get_instance() {
	
		return self::$instance;
	
	}
	
}