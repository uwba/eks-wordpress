<?php
/**
 * Aseets class for the Soliloquy Preview Addon.
 *
 * @since 1.0.0
 *
 * @package	Soliloquy-Preview
 * @author	Thomas Griffin
 */
class Tgmsp_Preview_Assets {

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
		
		/** Return early if Soliloquy is not active */
		if ( Tgmsp_Preview::soliloquy_is_not_active() )
			return;
			
		/** Load dev scripts and styles if in Soliloquy dev mode */
		$dev = defined( 'SOLILOQUY_DEV' ) && SOLILOQUY_DEV ? '-dev' : '';
	
		/** Register scripts and styles */
		wp_register_script( 'soliloquy-preview-admin', plugins_url( 'js/admin' . $dev . '.js', dirname( dirname( __FILE__ ) ) ), array( 'jquery' ), '1.0.0', true );
		wp_register_style( 'soliloquy-preview-admin', plugins_url( 'css/admin' . $dev . '.css', dirname( dirname( __FILE__ ) ) ) );
		
		/** Load assets */
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
	
	}
	
	/**
	 * Enqueue custom scripts and styles for the Soliloquy post type.
	 *
	 * @since 1.0.0
	 *
	 * @global object $current_screen The current screen object
	 * @global int $id The current post ID
	 * @global object $post The current post object
	 */
	public function load_assets() {

		global $current_screen, $id, $post;

		/** Only load for the Soliloquy post type add and edit screens */
		if ( 'soliloquy' == $current_screen->post_type && 'post' == $current_screen->base ) {
			/** Enqueue the stylesheet */
			wp_enqueue_style( 'soliloquy-preview-admin' );
			
			/** Send the post ID along with our script */
			$post_id = ( null === $id ) ? $post->ID : $id;

			/** Store script arguments in an array */
			$args = apply_filters( 'tgmsp_preview_object_args', array(
				'id'		=> $post_id,
				'generate'	=> __( 'Generating Your Preview…', 'soliloquy-preview' ),
				'error'		=> __( 'There was an error generating your preview. Please try again.', 'soliloquy-preview' ),
				'nonce'		=> wp_create_nonce( 'soliloquy_preview' ),
				'process'	=> __( 'Processing Your Preview…', 'soliloquy-preview' ),
				'success'	=> __( 'Slider preview generated!', 'soliloquy-preview' )
			) );

			wp_enqueue_script( 'soliloquy-preview-admin' );
			wp_localize_script( 'soliloquy-preview-admin', 'soliloquy_preview', $args );
		}

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