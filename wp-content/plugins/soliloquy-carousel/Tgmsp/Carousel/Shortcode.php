<?php
/**
 * Shortcode class for the Soliloquy Carousel Addon.
 *
 * @since 1.0.0
 *
 * @package	Soliloquy Carousel
 * @author	Thomas Griffin
 */
class Tgmsp_Carousel_Shortcode {

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
	
		/** Customize the shortcode output for the carousel */
		add_filter( 'tgmsp_slider_classes', array( $this, 'classes' ), 10, 2 );
		add_action( 'tgmsp_slider_script', array( $this, 'carousel' ) );
		add_filter( 'tgmsp_slider_item_style', array( $this, 'margin' ), 10, 4 );
	
	}
	
	/**
	 * Adds an appropriate class if a carousel slider is active.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Current slider classes
	 * @param int $id The current slider ID
	 * @return array $classes Amended array of slider classes
	 */
	public function classes( $classes, $id ) {
	
		/** Get the Carousel data for the current slider */
		$meta = get_post_meta( $id, '_soliloquy_carousel', true );
		
		/** If the individual item width is not set or is 0, do nothing - no carousel should be made */
		if ( self::is_not_carousel( $meta ) )
			return $classes;
			
		/** We know a carousel is being used, so let's add the class */
		$classes[] = 'soliloquy-carousel';
		
		/** Return the classes */
		return array_unique( $classes );
		
	}
	
	/**
	 * Outputs the appropriate slider object information to get the carousel running.
	 *
	 * @since 1.0.0
	 *
	 * @param array $slider An array of the current slider data
	 */
	public function carousel( $slider ) {
	
		/** Get the Carousel data for the current slider */
		$meta = get_post_meta( $slider['id'], '_soliloquy_carousel', true );
		
		/** If the individual item width is not set or is 0, do nothing - no carousel should be made */
		if ( self::is_not_carousel( $meta ) )
			return;
			
		/** Prepare variables */
		$width 	= isset( $meta['width'] ) ? absint( $meta['width'] ) : 0;
		$margin	= isset( $meta['margin'] ) ? absint( $meta['margin'] ) : 0;
		$min	= isset( $meta['minimum'] ) ? absint( $meta['minimum'] ) : 0;
		$max	= isset( $meta['maximum'] ) ? absint( $meta['maximum'] ) : 0;
		$move	= isset( $meta['move'] ) ? absint( $meta['move'] ) : 0;
			
		/** We know that the user wants a carousel, so let's make it happen */
		$output = 'itemWidth:' . $width . ',';
		$output .= 'itemMargin:' . $margin . ',';
		$output .= 'minItems:' . $min . ',';
		$output .= 'maxItems:' . $max . ',';
		$output .= 'move:' . $move . ',';
		
		/** Echo the output inside of the init script */
		echo $output;
	
	}
	
	/**
	 * Filters individual item inline styles so that carousel items can have appropriate margins.
	 *
	 * @since 1.0.0
	 *
	 * @param string $style The current inline style of the element
	 * @param int $id The current slider ID
	 * @param array $image Array of image data for the current slide
	 * @param int $i The current slide number in the slider
	 * @return string $style Amended inline style for the element
	 */
	public function margin( $style, $id, $image, $i ) {
	
		/** Get the Carousel data for the current slider */
		$meta = get_post_meta( $id, '_soliloquy_carousel', true );
		
		/** If the individual item width is not set or is 0 or if it is the first slide, don't alter the style */
		if ( self::is_not_carousel( $meta ) || 1 == $i )
			return $style;
		
		/** If we have any margin to set, let's set it */
		if ( isset( $meta['margin'] ) && $meta['margin'] )
			$style .= 'margin-left: ' . $meta['margin'] . 'px;';
			
		/** Return the inline style */
		return $style;
	
	}
	
	/**
	 * Helper function to determine if the slider is a carousel or not.
	 *
	 * @since 1.0.0
	 *
	 * @param array $meta The current meta for the slider
	 * @return bool True if carousel is active, false if not
	 */
	public static function is_not_carousel( $meta ) {
	
		return (bool) ( ! isset( $meta['width'] ) || isset( $meta['width'] ) && empty( $meta['width'] ) || isset( $meta['width'] ) && ! $meta['width'] );
	
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