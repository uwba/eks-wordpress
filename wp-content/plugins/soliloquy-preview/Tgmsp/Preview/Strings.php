<?php
/**
 * Strings class for the Soliloquy Preview Addon.
 *
 * @since 1.0.0
 *
 * @package	Soliloquy Preview
 * @author	Thomas Griffin
 */
class Tgmsp_Preview_Strings {

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private static $instance;
	
	/**
	 * Holds a copy of all the strings used by the Soliloquy Preview Addon.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $strings = array();

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
	
		$this->strings = apply_filters( 'tgmsp_preview_strings', array(
			'no_images'			=> __( 'You have no images to preview!', 'soliloquy-preview' ),
			'preview_note'		=> __( 'If your images are "spilling out" of the metabox, your images do not scale in direct proportion to the slider size you have entered. If your images are different sizes, consider using a custom WordPress size instead.', 'soliloquy-preview' ),
			'preview_settings'	=> __( 'Soliloquy Preview', 'soliloquy-preview' ),
			'preview_slider'	=> __( 'Click Here to Generate Preview', 'soliloquy-preview' ),
			'settings_desc'		=> __( 'Click on the button below to generate a preview of your slider based on your current settings. The preview does not save your settings, so once you are satisfied with your preview, save the slider in order to apply your changes.', 'soliloquy-preview' )
		) );
	
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