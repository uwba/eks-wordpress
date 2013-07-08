<?php
/**
 * Preview class for the Soliloquy Carousel Addon.
 *
 * @since 1.0.0
 *
 * @package	Soliloquy Carousel
 * @author	Thomas Griffin
 */
class Tgmsp_Carousel_Preview {

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
		
		add_action( 'tgmsp_preview_start', array( $this, 'preview_init' ) );
		
		if ( version_compare( Tgmsp_Preview::get_instance()->version, '1.0.3', '>=' ) )
			add_filter( 'tgmsp_preview_post_var', array( $this, 'force_transition' ) );
	
	}
	
	/**
	 * Init callback to make sure that filters and hooks are only executed in the Preview
	 * context.
	 *
	 * @since 1.0.0
	 *
	 * @param array $post_var The $_POST data from the Ajax request
	 */
	public function preview_init( $post_var ) {
	
		/** Only execute if there is a carousel instance to process */
		foreach ( $post_var as $key => $val ) {
			if ( 'soliloquy-carousel-width' == $key && ! empty( $val ) && 0 !== $val ) {
				if ( version_compare( Tgmsp_Preview::get_instance()->version, '1.0.3', '>=' ) )
					add_filter( 'tgmsp_slider_classes', array( $this, 'classes' ), 10, 3 );
					
				add_action( 'tgmsp_slider_script', array( $this, 'carousel' ) );
				
				if ( version_compare( Tgmsp_Preview::get_instance()->version, '1.0.3', '>=' ) )
					add_filter( 'tgmsp_slider_item_style', array( $this, 'margin' ), 10, 5 );
					
				break;
			}
		}
	
	}
	
	/**
	 * Adds an appropriate class if a carousel slider is active.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Current slider classes
	 * @param int $id The current slider ID
	 * @param array $post_var Array of $_POST data submitted by the user
	 * @return array $classes Amended array of slider classes
	 */
	public function classes( $classes, $id, $post_var ) {
			
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
	 * @param array $post_var Array of $_POST data submitted by user
	 */
	public function carousel( $post_var ) {
	
		/** Prepare variables */
		$width 	= isset( $post_var['soliloquy-carousel-width'] ) ? absint( $post_var['soliloquy-carousel-width'] ) : 0;
		$margin	= isset( $post_var['soliloquy-carousel-margin'] ) ? absint( $post_var['soliloquy-carousel-margin'] ) : 0;
		$min	= isset( $post_var['soliloquy-carousel-minimum'] ) ? absint( $post_var['soliloquy-carousel-minimum'] ) : 0;
		$max	= isset( $post_var['soliloquy-carousel-maximum'] ) ? absint( $post_var['soliloquy-carousel-maximum'] ) : 0;
		$move	= isset( $post_var['soliloquy-carousel-move'] ) ? absint( $post_var['soliloquy-carousel-move'] ) : 0;
			
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
	 * @param array $post_var Array of $_POST data submitted by the user
	 * @return string $style Amended inline style for the element
	 */
	public function margin( $style, $id, $image, $i, $post_var ) {
		
		/** If we have any margin to set, let's set it */
		if ( isset( $post_var['soliloquy-carousel-margin'] ) && $post_var['soliloquy-carousel-margin'] )
			$style .= 'margin-left: ' . $post_var['soliloquy-carousel-margin'] . 'px;';
			
		/** Return the inline style */
		return $style;
	
	}
	
	/**
	 * Forces a transition if the carousel item is active.
	 *
	 * @since 1.0.0
	 *
	 * @param array $post_var Array of $_POST data submitted by the user
	 * @return string $style Amended array of post data
	 */
	public function force_transition( $post_var ) {
		
		/** Only execute if there is a carousel instance to process */
		foreach ( $post_var as $key => $val ) {
			if ( 'soliloquy-carousel-width' == $key && ! empty( $val ) ) {
				$post_var['soliloquy-transition'] = 'slide-horizontal';
				break;
			}
		}
		
		/** Return the amended $post_var variable */
		return $post_var;
	
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