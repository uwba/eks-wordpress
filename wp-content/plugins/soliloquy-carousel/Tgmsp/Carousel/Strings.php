<?php
/**
 * Strings class for the Soliloquy Carousel Addon.
 *
 * @since 1.0.0
 *
 * @package	Soliloquy Carousel
 * @author	Thomas Griffin
 */ 
class Tgmsp_Carousel_Strings {

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private static $instance;
	
	/**
	 * Holds a copy of all the strings used by the Soliloquy Carousel Addon.
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
	
		$this->strings = apply_filters( 'tgmsp_carousel_strings', array(
			'carousel_settings'	=> __( 'Soliloquy Carousel Settings', 'soliloquy-carousel' ),
			'item_margin'		=> __( 'Individual Item Margin', 'soliloquy-carousel' ),
			'item_margin_desc'	=> __( 'Sets the margin between each item in your carousel.', 'soliloquy-carousel' ),
			'item_maximum'		=> __( 'Maximum Visible Items', 'soliloquy-carousel' ),
			'item_maximum_desc'	=> __( 'The maximum number of items visible in the carousel.', 'soliloquy-carousel' ),
			'item_minimum'		=> __( 'Minimum Visible Items', 'soliloquy-carousel' ),
			'item_minimum_desc'	=> __( 'The minimum number of items visible in the carousel.', 'soliloquy-carousel' ),
			'item_move'			=> __( 'Number of Items to Move', 'soliloquy-carousel' ),
			'item_move_desc'	=> __( 'Determines the number of items moved with each animation.', 'soliloquy-carousel' ),
			'item_width'		=> __( 'Individual Item Width', 'soliloquy-carousel' ),
			'item_width_desc' 	=> __( 'Sets the width (in pixels) of each item in your carousel.', 'soliloquy-carousel' ),
			'reset'				=> esc_attr__( 'Click Here to Reset Carousel Settings', 'soliloquy-carousel' ),
			'settings_desc' 	=> __( 'By configuring the options below, you will turn your regular slider into a carousel. If you do not want your slider to be a carousel or wish to revert back to a normal slider, click on the button below to reset your carousel settings.', 'soliloquy-carousel' )
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