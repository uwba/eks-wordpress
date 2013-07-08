<?php
/**
 * Admin class for the Soliloquy Carousel Addon.
 *
 * @since 1.0.0
 *
 * @package	Soliloquy Carousel
 * @author	Thomas Griffin
 */ 
class Tgmsp_Carousel_Admin {

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
		add_action( 'save_post', array( $this, 'save_carousel_settings' ), 10, 2 );
		add_filter( 'tgmsp_slider_settings', array( $this, 'set_carousel_opts' ), 10, 2 );
	
	}
	
	/**
	 * Deactivate the plugin if Soliloquy is not active and update the recently
	 * activate plugins with our plugin.
	 *
	 * @since 1.0.0
	 */
	public function deactivation() {
		
		/** Don't deactivate when doing a Soliloquy update or when editing Soliloquy from the Plugin Editor */
		if ( Tgmsp_Carousel::soliloquy_is_not_active() ) {
			$recent = (array) get_option( 'recently_activated' );
			$recent[plugin_basename( Tgmsp_Carousel::get_file() )] = time();
			update_option( 'recently_activated', $recent );
			deactivate_plugins( plugin_basename( Tgmsp_Carousel::get_file() ) );
		}
		
	}
	
	/**
	 * Adds the Soliloquy Carousel metabox to the Soliloquy edit screen.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
	
		add_meta_box( 'soliloquy_carousel_settings', Tgmsp_Carousel_Strings::get_instance()->strings['carousel_settings'], array( $this, 'carousel_settings' ), 'soliloquy', 'normal', 'core' );
	
	}
	
	/**
	 * Callback function for the Soliloquy Carousel metabox.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The current post object
	 */
	public function carousel_settings( $post ) {
	
		/** Always keep security first */
		wp_nonce_field( 'soliloquy_carousel_settings', 'soliloquy_carousel_settings' );
		
		do_action( 'tgmsp_carousel_before_settings_table', $post );
		
		?>
		<p><?php echo Tgmsp_Carousel_Strings::get_instance()->strings['settings_desc']; ?></p>
		<a id="soliloquy-carousel-reset" href="#" class="button-secondary" title="<?php echo Tgmsp_Carousel_Strings::get_instance()->strings['reset']; ?>"><?php echo Tgmsp_Carousel_Strings::get_instance()->strings['reset']; ?></a>
		<table class="form-table">
			<tbody>
				<?php do_action( 'tgmsp_carousel_before_setting_width', $post ); ?>
				<tr id="soliloquy-carousel-width-box" valign="middle">
					<th scope="row"><label for="soliloquy-carousel-width"><?php echo Tgmsp_Carousel_Strings::get_instance()->strings['item_width']; ?></label></th>
					<td>
						<input id="soliloquy-carousel-width" type="text" name="_soliloquy_carousel[width]" value="<?php echo esc_attr( Tgmsp_Admin::get_custom_field( '_soliloquy_carousel', 'width' ) ); ?>" />
						<span class="description"><?php echo Tgmsp_Carousel_Strings::get_instance()->strings['item_width_desc']; ?></span>
					</td>
				</tr>
				<?php do_action( 'tgmsp_carousel_before_setting_margin', $post ); ?>
				<tr id="soliloquy-carousel-margin-box" valign="middle">
					<th scope="row"><label for="soliloquy-carousel-margin"><?php echo Tgmsp_Carousel_Strings::get_instance()->strings['item_margin']; ?></label></th>
					<td>
						<input id="soliloquy-carousel-margin" type="text" name="_soliloquy_carousel[margin]" value="<?php echo esc_attr( Tgmsp_Admin::get_custom_field( '_soliloquy_carousel', 'margin' ) ); ?>" />
						<span class="description"><?php echo Tgmsp_Carousel_Strings::get_instance()->strings['item_margin_desc']; ?></span>
					</td>
				</tr>
				<?php do_action( 'tgmsp_carousel_before_setting_minimum', $post ); ?>
				<tr id="soliloquy-carousel-minimum-box" valign="middle">
					<th scope="row"><label for="soliloquy-carousel-minimum"><?php echo Tgmsp_Carousel_Strings::get_instance()->strings['item_minimum']; ?></label></th>
					<td>
						<input id="soliloquy-carousel-minimum" type="text" name="_soliloquy_carousel[minimum]" value="<?php echo esc_attr( Tgmsp_Admin::get_custom_field( '_soliloquy_carousel', 'minimum' ) ); ?>" />
						<span class="description"><?php echo Tgmsp_Carousel_Strings::get_instance()->strings['item_minimum_desc']; ?></span>
					</td>
				</tr>
				<?php do_action( 'tgmsp_carousel_before_setting_maximum', $post ); ?>
				<tr id="soliloquy-carousel-maximum-box" valign="middle">
					<th scope="row"><label for="soliloquy-carousel-maximum"><?php echo Tgmsp_Carousel_Strings::get_instance()->strings['item_maximum']; ?></label></th>
					<td>
						<input id="soliloquy-carousel-maximum" type="text" name="_soliloquy_carousel[maximum]" value="<?php echo esc_attr( Tgmsp_Admin::get_custom_field( '_soliloquy_carousel', 'maximum' ) ); ?>" />
						<span class="description"><?php echo Tgmsp_Carousel_Strings::get_instance()->strings['item_maximum_desc']; ?></span>
					</td>
				</tr>
				<?php do_action( 'tgmsp_carousel_before_setting_move', $post ); ?>
				<tr id="soliloquy-carousel-move-box" valign="middle">
					<th scope="row"><label for="soliloquy-carousel-move"><?php echo Tgmsp_Carousel_Strings::get_instance()->strings['item_move']; ?></label></th>
					<td>
						<input id="soliloquy-carousel-move" type="text" name="_soliloquy_carousel[move]" value="<?php echo esc_attr( Tgmsp_Admin::get_custom_field( '_soliloquy_carousel', 'move' ) ); ?>" />
						<span class="description"><?php echo Tgmsp_Carousel_Strings::get_instance()->strings['item_move_desc']; ?></span>
					</td>
				</tr>
				<?php do_action( 'tgmsp_carousel_end_of_settings', $post ); ?>
			</tbody>
		</table>
		<?php
		
		do_action( 'tgmsp_carousel_after_settings', $post );
	
	}
	
	/**
	 * Save carousel settings post meta fields added to Soliloquy metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The post ID
	 * @param object $post Current post object data
	 */
	public function save_carousel_settings( $post_id, $post ) {

		/** Bail out if we fail a security check */
		if ( ! isset( $_POST[sanitize_key( 'soliloquy_carousel_settings' )] ) || ! wp_verify_nonce( $_POST[sanitize_key( 'soliloquy_carousel_settings' )], 'soliloquy_carousel_settings' ) )
			return $post_id;

		/** Bail out if running an autosave, ajax or a cron */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			return;
		if ( defined( 'DOING_CRON' ) && DOING_CRON )
			return;

		/** Bail out if the user doesn't have the correct permissions to update the slider */
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		/** All security checks passed, so let's store our data */
		$settings = isset( $_POST['_soliloquy_carousel'] ) ? $_POST['_soliloquy_carousel'] : '';
		
		/** Sanitize all data before updating */
		$settings['width'] 		= isset( $_POST['_soliloquy_carousel']['width'] ) && ! empty( $_POST['_soliloquy_carousel']['width'] ) ? absint( $_POST['_soliloquy_carousel']['width'] ) : '';
		$settings['margin'] 	= isset( $_POST['_soliloquy_carousel']['margin'] ) && ! empty( $_POST['_soliloquy_carousel']['margin'] ) ? absint( $_POST['_soliloquy_carousel']['margin'] ) : '';
		$settings['minimum']	= isset( $_POST['_soliloquy_carousel']['minimum'] ) && ! empty( $_POST['_soliloquy_carousel']['minimum'] ) ? absint( $_POST['_soliloquy_carousel']['minimum'] ) : '';
		$settings['maximum'] 	= isset( $_POST['_soliloquy_carousel']['maximum'] ) && ! empty( $_POST['_soliloquy_carousel']['maximum'] ) ? absint( $_POST['_soliloquy_carousel']['maximum'] ) : '';

		do_action( 'tgmsp_carousel_save_settings', $settings, $post_id, $post );

		/** Update post meta with sanitized values */
		update_post_meta( $post_id, '_soliloquy_carousel', $settings );

	}
	
	/**
	 * Set certain settings to specific values if the carousel option is active.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings The sanitized $_POST var settings submitted by the user
	 * @param int $post_id The current post ID
	 * @return array $settings Amended array of sanitized $_POST settings
	 */
	public function set_carousel_opts( $settings, $post_id ) {
	
		/** If these options are set, we know that the carousel is active */
		if ( isset( $_POST['_soliloquy_carousel']['width'] ) && ! empty( $_POST['_soliloquy_carousel']['width'] ) )
			/** The slide animation must be horizontal for the carousel */
			$settings['transition'] = 'slide-horizontal';
		
		return $settings;
	
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