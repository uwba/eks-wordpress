<?php
/**
 * Ajax class for the Soliloquy Preview Addon.
 *
 * @since 1.0.0
 *
 * @package	Soliloquy-Preview
 * @author	Thomas Griffin
 */
class Tgmsp_Preview_Ajax {

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Flag for YouTube video present in slider.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $youtube = false;

	/**
	 * Flag for Vimeo video present in slider.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $vimeo = false;

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

		add_action( 'wp_ajax_soliloquy_update_preview', array( $this, 'preview' ) );

	}

	/**
	 * Generates a preview of the current slider instance based on the current settings.
	 *
	 * @since 1.0.0
	 */
	public function preview() {

		/** Do a security check first */
		check_ajax_referer( 'soliloquy_preview', 'nonce' );

		/** Store all of the $_POST contents and start generating the preview */
		$post_var 	= apply_filters( 'tgmsp_preview_post_var', $_POST );
		$has_images = false;
		$id			= absint( $post_var['id'] );
		$extra		= ''; // Used by other addons to hook into the preview script

		/** Loop through $_POST keys and make sure that we have images */
		foreach ( array_keys( $_POST ) as $key ) {
			if ( preg_match( '|^soliloquymeta-|', $key ) ) {
				$has_images = true;
				break;
			}
		}

		/** If there are no images, send an error message and die */
		$has_images = apply_filters( 'tgmsp_preview_has_images', $has_images, $post_var );
		if ( ! $has_images ) {
			echo json_encode( array( 'error' => Tgmsp_Preview_Strings::get_instance()->strings['no_images'] ) );
			die;
		}

		/** Setup globals and annouce that the preview generator is in session */
		global $soliloquy_data, $soliloquy_count;

		/** Provide a hook so that addons can come in and conditionally add in filters just for the preview section */
		do_action( 'tgmsp_preview_start', $post_var );

		/** Prepare our variables */
		$images = array();
		$meta	= get_post_meta( $id, '_soliloquy_settings', true );
		$args 	= apply_filters( 'tgmsp_get_slider_images_args', array(
			'orderby' 			=> 'menu_order',
			'order' 			=> 'ASC',
			'post_type' 		=> 'attachment',
			'post_parent' 		=> $id,
			'post_status' 		=> null,
			'posts_per_page' 	=> -1
		), $id, $post_var );

		/** Get all of the image attachments to the Soliloquy */
		$attachments = apply_filters( 'tgmsp_get_slider_images', get_posts( $args ), $args, $id, $post_var );

		/** If there are no images, send an error message and die */
		if ( ! $attachments ) {
			echo json_encode( array( 'error' => Tgmsp_Preview_Strings::get_instance()->strings['no_images'] ) );
			die;
		}

		/** Loop through the attachments and store the data */
		if ( $attachments ) {
			foreach ( $attachments as $attachment ) {
				switch ( $attachment->post_mime_type ) :
					default :
						/** Get attachment metadata for each attachment */
						if ( 'default' == $post_var['soliloquy-default-size'] )
							$image = wp_get_attachment_image_src( $attachment->ID, 'full' );
						else
							$image = wp_get_attachment_image_src( $attachment->ID, $post_var['soliloquy-custom-size'] );

						$image = apply_filters( 'tgmsp_get_image_data', $image, $id, $attachment, ( 'default' == $post_var['soliloquy-default-size'] ) ? $post_var['soliloquy-default-size'] : $post_var['soliloquy-custom-size'], $post_var );

						/** Store data in an array to send back to the shortcode */
						if ( $image ) {
							$images[] = apply_filters( 'tgmsp_image_data', array(
								'id' 		=> $attachment->ID,
								'src' 		=> $image[0],
								'width' 	=> $image[1],
								'height' 	=> $image[2],
								'title'		=> isset( $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-title'] ) ? $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-title'] : '',
								'alt' 		=> isset( $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-alt'] ) ? $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-alt'] : '',
								'link' 		=> isset( $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-link'] ) ? $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-link'] : '',
								'linktitle' => isset( $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-link-title'] ) ? $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-link-title'] : '',
								'linktab' 	=> isset( $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-link-check']) && 'true' == $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-link-check'] ? 1 : 0,
								'caption' 	=> isset( $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-caption'] ) ? $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-caption'] : '',
								'mime'		=> 'image'
							), $attachment, $id, $post_var );
						}
					break;
					case 'soliloquy/video' :
						$images[] = apply_filters( 'tgmsp_image_data', array(
							'id' 		=> $attachment->ID,
							'src' 		=> '',
							'width' 	=> '',
							'height' 	=> '',
							'title'		=> isset( $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-title'] ) ? $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-title'] : '',
							'caption' 	=> isset( $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-caption'] ) ? $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-caption'] : '',
							'content' 	=> isset( $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-content'] ) ? $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-content'] : '',
							'mime'		=> 'video'
						), $attachment, $id, $post_var );
					break;
					case 'soliloquy/html' :
						$images[] = apply_filters( 'tgmsp_image_data', array(
							'id' 		=> $attachment->ID,
							'src' 		=> '',
							'width' 	=> '',
							'height' 	=> '',
							'title'		=> isset( $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-title'] ) ? $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-title'] : '',
							'content' 	=> isset( $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-content'] ) ? $post_var['soliloquymeta-' . $attachment->ID]['soliloquy-content'] : '',
							'mime'		=> 'html'
						), $attachment, $id, $post_var );
					break;
				endswitch;
			}
		}

		/** Maintain filtering of images */
		$images = apply_filters( 'tgmsp_slider_images', $images, $meta, $attachments, $post_var );

		/** If there are no images, send an error message and die */
		if ( ! $images ) {
			echo json_encode( array( 'error' => Tgmsp_Preview_Strings::get_instance()->strings['no_images'] ) );
			die;
		}

		/** Now let's begin creating the slider instance based on the shortcode output */
		$soliloquy_data[absint( $soliloquy_count )]['id'] 		= $id;
		$soliloquy_data[absint( $soliloquy_count )]['meta'] 	= get_post_meta( $id, '_soliloquy_settings', true );
		$slider 												= '';
		$i 														= 1;
		$preloader												= false;

		/** Only proceed if we have images to output */
		if ( $images ) {
			/** Allow devs to circumvent the entire slider if necessary - beware, this filter is powerful - use with caution */
			$pre = apply_filters( 'tgmsp_pre_load_slider', false, $id, $images, $soliloquy_data, absint( $soliloquy_count ), $slider, $post_var );
			if ( $pre ) {
				echo json_encode( array( 'pre' => $pre ) );
				die;
			}

			// If the users wants randomized images, go ahead and do that now.
			if ( isset( $post_var['soliloquy-random'] ) && 'true' == $post_var['soliloquy-random'] )
				$images = $this->shuffle( $images );

			do_action( 'tgmsp_before_slider_output', $id, $images, $soliloquy_data, absint( $soliloquy_count ), $slider, $post_var );

			/** If a custom size is chosen, all image sizes will be cropped the same, so grab width/height from first image */
			/** If a custom size is chosen, all image sizes will be cropped the same, so grab width/height from first image */
			if ( 'default' == $post_var['soliloquy-default-size'] )
				$width = $post_var['soliloquy-width'];
			else
				$width = $images[0]['width'];

			// If the width is zero, make sure we have a positive width value first.
			if ( 0 == $width || empty( $width ) ) {
				foreach ( $images as $image ) {
					if ( ! empty( $image['width'] ) && $image['width'] > 0 ) {
						$width = $image['width'];
						break;
					}
				}
			}

			$width	= $ratio_width = apply_filters( 'tgmsp_slider_width', $width, $id );
			$width	= preg_match( '|%$|', trim( $width ) ) ? trim( $width ) . ';' : absint( $width ) . 'px;';

			if ( 'default' == $post_var['soliloquy-default-size'] )
				$height = $post_var['soliloquy-height'];
			else
				$height = $images[0]['height'];

			// If the height is zero, make sure we have a positive height value first.
			if ( 0 == $height || empty( $height ) ) {
				foreach ( $images as $image ) {
					if ( ! empty( $image['height'] ) && $image['height'] > 0 ) {
						$height = $image['height'];
						break;
					}
				}
			}

			$height	= $ratio_height = apply_filters( 'tgmsp_slider_height', $height, $id );
			$height	= preg_match( '|%$|', trim( $height ) ) ? trim( $height ) . ';' : absint( $height ) . 'px;';

			// If the user wants a preloader image, store the aspect ratio for dynamic height calculation.
			if ( isset( $post_var['soliloquy-preloader'] ) && 'true' == $post_var['soliloquy-preloader'] ) {
				$preloader = true;
				$ratio_width  = preg_match( '|%$|', trim( $ratio_width ) ) ? str_replace( '%', '', $ratio_width ) : absint( $ratio_width );
				$ratio_height = preg_match( '|%$|', trim( $ratio_height ) ) ? str_replace( '%', '', $ratio_height ) : absint( $ratio_height );
				$soliloquy_data[absint( $soliloquy_count )]['ratio'] = ( $ratio_width / $ratio_height );
				add_action( 'tgmsp_callback_start_' . $id, array( $this, 'preloader' ) );
				add_filter( 'tgmsp_slider_classes', array( $this, 'preloader_class' ) );
			}

			/** Output the slider info */
			$slider = apply_filters( 'tgmsp_before_slider', $slider, $id, $images, $soliloquy_data, absint( $soliloquy_count ), $post_var );
			$slider .= '<div id="soliloquy-container-' . esc_attr( $id ) . '" ' . $this->get_custom_slider_classes( $post_var ) . ' style="' . apply_filters( 'tgmsp_slider_width_output', 'max-width: ' . $width, $width, $id, $post_var ) . ' ' . apply_filters( 'tgmsp_slider_height_output', 'max-height: ' . $height, $height, $id, $post_var ) . ' ' . apply_filters( 'tgmsp_slider_container_style', '', $id, $post_var ) . '">';
				$slider .= '<div id="soliloquy-' . esc_attr( $id ) . '" class="soliloquy">';
					$slider .= '<ul id="soliloquy-list-' . esc_attr( $id ) . '" class="soliloquy-slides">';
						foreach ( $images as $image ) {
							if ( empty( $image['mime'] ) || 'image' == $image['mime'] ) :
								$alt 			= empty( $image['alt'] ) ? apply_filters( 'tgmsp_no_alt', '', $id, $image, $post_var ) : $image['alt'];
								$title 			= empty( $image['title'] ) ? apply_filters( 'tgmsp_no_title', '', $id, $image, $post_var ) : $image['title'];
								$link_title 	= empty( $image['linktitle'] ) ? apply_filters( 'tgmsp_no_link_title', '', $id, $image, $post_var ) : $image['linktitle'];
								$link_target 	= empty( $image['linktab'] ) ? apply_filters( 'tgmsp_no_link_target', '', $id, $image, $post_var ) : 'target="_blank"';

								$slide = '<li id="soliloquy-' . esc_attr( $id ) . '-item-' . $i . '" class="soliloquy-item soliloquy-image-slide" style="' . apply_filters( 'tgmsp_slider_item_style', 'display: none;', $id, $image, $i, $post_var ) . '" ' . apply_filters( 'tgmsp_slider_item_attr', '', $id, $image, $i, $post_var ) . '>';
									/** Output our normal data */
									if ( ! empty( $image['link'] ) )
										$slide .= apply_filters( 'tgmsp_link_output', '<a href="' . esc_url( $image['link'] ) . '" title="' . esc_attr( $link_title ) . '" ' . $link_target . '>', $id, $image, $link_title, $link_target, $post_var );

									/** Use data attributes to fake loading of the image until its time to get to it */
									if ( 0 !== $post_var['soliloquy-number'] && ( $i - 1 ) == $post_var['soliloquy-number'] )
										$slide .= apply_filters( 'tgmsp_image_output', '<img class="soliloquy-item-image" src="' . esc_url( $image['src'] ) . '" alt="' . esc_attr( $alt ) . '" title="' . esc_attr( $title ) . '" />', $id, $image, $alt, $title, $post_var );
									else if ( 1 == $i && 0 == $post_var['soliloquy-number'] )
										$slide .= apply_filters( 'tgmsp_image_output', '<img class="soliloquy-item-image" src="' . esc_url( $image['src'] ) . '" alt="' . esc_attr( $alt ) . '" title="' . esc_attr( $title ) . '" />', $id, $image, $alt, $title, $post_var );
									else
										$slide .= apply_filters( 'tgmsp_image_output', '<img class="soliloquy-item-image" src="' . esc_url( plugins_url( 'css/images/holder.gif', Tgmsp::get_file() ) ) . '" data-soliloquy-src="' . esc_url( $image['src'] ) . '" alt="' . esc_attr( $alt ) . '" title="' . esc_attr( $title ) . '" />', $id, $image, $alt, $title, $post_var );

									if ( ! empty( $image['link'] ) )
										$slide .= '</a>';
									if ( ! empty( $image['caption'] ) )
										$slide .= apply_filters( 'tgmsp_caption_output', '<div class="soliloquy-caption"><div class="soliloquy-caption-inside">' . $image['caption'] . '</div></div>', $id, $image, $post_var );
								$slide .= '</li>';
								$slider .= apply_filters( 'tgmsp_individual_slide', $slide, $id, $image, $i, $post_var );
							elseif ( 'video' == $image['mime'] ) :
								// We have a video slide, so let's output it.
								$slide = '<li id="soliloquy-' . esc_attr( $id ) . '-item-' . $i . '" class="soliloquy-item soliloquy-video-slide" style="' . apply_filters( 'tgmsp_slider_item_style', 'display: none;', $id, $image, $i, $post_var ) . '" ' . apply_filters( 'tgmsp_slider_item_attr', '', $id, $image, $i, $post_var ) . '>';
								$source = '';

								if ( preg_match( '#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#', $image['content'], $y_matches ) )
									$source = 'youtube';

								if ( preg_match( '#(?:https?:\/\/(?:[\w]+\.)*vimeo\.com(?:[\/\w]*\/videos?)?\/([0-9]+)[^\s]*)#i', $image['content'], $v_matches ) )
									$source = 'vimeo';

								/** If there was an error validating the URL, output a notice */
								if ( empty( $source ) ) {
									$slide .= '<p class="soliloquy-video-error">' . Tgmsp_Strings::get_instance()->strings['video_link_error'] . '</p>';
								} else {
									/** Generate the video embed code based on the type of video */
									switch ( $source ) {
										case 'youtube' :
											$this->youtube = true;
											$soliloquy_data[absint( $soliloquy_count )]['youtube'] = true; // Set the YouTube video flag to true
											$vid 	= $y_matches[0];
											$slide	.= $this->get_video_code( 'youtube', $vid, $id, $i, $width, $height, $post_var );
											break;

										case 'vimeo' :
											$this->vimeo = true;
											$soliloquy_data[absint( $soliloquy_count )]['vimeo'] = true; // Set the Vimeo video flag to true
											$vid 	= $v_matches[1];
											$slide 	.= $this->get_video_code( 'vimeo', $vid, $id, $i, $width, $height, $post_var );
											break;
									}

									/** Apply the caption as well, but hide it so a user could use it if they need */
									if ( ! empty( $image['caption'] ) )
										$slide .= apply_filters( 'tgmsp_caption_output', '<div class="soliloquy-caption soliloquy-video-caption"><div class="soliloquy-caption-inside">' . $image['caption'] . '</div></div>', $id, $image, $i, $post_var );

									$slide .= '</li>';
									$slider .= apply_filters( 'tgmsp_individual_slide', $slide, $id, $image, $i, $post_var );

									/** Now we need to initialize the video script for interactions between the slider and videos */
									add_action( 'tgmsp_callback_before_' . absint( $id ), array( $this, 'pause_video' ) );
								}
							elseif ( 'html' == $image['mime'] ) :
								$slide = '<li id="soliloquy-' . esc_attr( $id ) . '-item-' . $i . '" class="soliloquy-item soliloquy-html-slide" style="' . apply_filters( 'tgmsp_slider_item_style', 'display: none;', $id, $image, $i, $post_var ) . '" ' . apply_filters( 'tgmsp_slider_item_attr', '', $id, $image, $i, $post_var ) . '>';
									// Output the code that has been set in the HTML editor area for the HTML slide.
									$slide .= apply_filters( 'tgmsp_html_slide_output', do_shortcode( $image['content'] ), $id, $image, $i, $post_var );
								$slide .= '</li>';
								$slider .= apply_filters( 'tgmsp_individual_slide', $slide, $id, $image, $i, $post_var );
							endif;
							$i++;
						}
					$slider .= '</ul>';
					$slider = apply_filters( 'tgmsp_inside_slider', $slider, $id, $images, $soliloquy_data, absint( $soliloquy_count ), $post_var );
				$slider .= '</div>';
				$slider = apply_filters( 'tgmsp_inside_slider_container', $slider, $id, $images, $soliloquy_data, absint( $soliloquy_count ), $post_var );
			$slider .= '</div>';

			$slider = apply_filters( 'tgmsp_after_slider', $slider, $id, $images, $soliloquy_data, absint( $soliloquy_count ), $post_var );

			// If we are adding a preloading icon, do it now.
			if ( $preloader ) {
				$slider .= '<style type="text/css">.soliloquy-container.soliloquy-preloader{background: url("' . plugins_url( "css/images/preloader.gif", Tgmsp::get_file() ) . '") no-repeat scroll 50% 50%;}@media only screen and (-webkit-min-device-pixel-ratio: 1.5),only screen and (-o-min-device-pixel-ratio: 3/2),only screen and (min--moz-device-pixel-ratio: 1.5),only screen and (min-device-pixel-ratio: 1.5){.soliloquy-container.soliloquy-preloader{background-image: url("' . plugins_url( "css/images/preloader@2x.gif", Tgmsp::get_file() ) . '");background-size: 16px 16px;}}</style>';
			}
		}

		/** Increment the counter in case there are multiple slider instances on the same page */
		$soliloquy_count++;

		$slider = apply_filters( 'tgmsp_slider_shortcode', $slider, $id, $images, $post_var );

		/** Now let's go ahead and send the re-initialization code too */
		$animation	= isset( $post_var['soliloquy-transition'] ) && 'fade' == $post_var['soliloquy-transition'] ? 'fade' : 'slide';
		$transition = isset( $post_var['soliloquy-transition'] ) && 'slide-vertical' == $post_var['soliloquy-transition'] ? 'vertical' : 'horizontal';
		if ( version_compare( Tgmsp::get_instance()->version, '1.3.0', '>=' ) )
			$slide 	= ( 'slide' == $animation ) ? 'direction: \'' . $transition . '\',' : '';
		else
			$slide 	= ( 'slide' == $animation ) ? 'slideDirection: \'' . $transition . '\',' : '';
		$animate 	= isset( $post_var['soliloquy-animate'] ) 		&& 'true' == $post_var['soliloquy-animate']		? 'true' : 'false';
		$direction 	= isset( $post_var['soliloquy-navigation'] ) 	&& 'true' == $post_var['soliloquy-navigation'] 	? 'true' : 'false';
		$control 	= isset( $post_var['soliloquy-control'] ) 		&& 'true' == $post_var['soliloquy-control']		? 'true' : 'false';
		$keyboard 	= isset( $post_var['soliloquy-keyboard'] ) 		&& 'true' == $post_var['soliloquy-keyboard'] 	? 'true' : 'false';
		$multi 		= isset( $post_var['soliloquy-multi-keyboard'] )&& 'true' == $post_var['soliloquy-multi-keyboard'] 	? 'true' : 'false';
		$mouse 		= isset( $post_var['soliloquy-mousewheel'] ) 	&& 'true' == $post_var['soliloquy-mousewheel'] 	? 'true' : 'false';
		$pauseplay 	= isset( $post_var['soliloquy-pauseplay'] ) 	&& 'true' == $post_var['soliloquy-pauseplay']	? 'true' : 'false';
		$randomize 	= 'false'; // Force this to false since we will randomize server-side.
		$loop 		= isset( $post_var['soliloquy-loop'] ) 			&& 'true' == $post_var['soliloquy-loop']		? 'true' : 'false';
		$action 	= isset( $post_var['soliloquy-action'] ) 		&& 'true' == $post_var['soliloquy-action']		? 'true' : 'false';
		$hover 		= isset( $post_var['soliloquy-hover'] ) 		&& 'true' == $post_var['soliloquy-hover']		? 'true' : 'false';
		$video		= isset( $post_var['soliloquy-video'] )			&& 'true' == $post_var['soliloquy-video']		? 'true' : 'false';
		$fitvids	= $this->youtube || $this->vimeo ? 'fitVids().' : '';
		$css		= isset( $post_var['soliloquy-slider-css'] )	&& 'true' == $post_var['soliloquy-slider-css']	? 'true' : 'false';
		$reverse	= isset( $post_var['soliloquy-reverse'] )		&& 'true' == $post_var['soliloquy-reverse']		? 'true' : 'false';
		$smooth		= isset( $post_var['soliloquy-smooth'] )		&& 'true' == $post_var['soliloquy-smooth']		? 'true' : 'false';
		$touch		= isset( $post_var['soliloquy-touch'] )			&& 'true' == $post_var['soliloquy-touch']		? 'true' : 'false';
		$delay		= isset( $post_var['soliloquy-delay'] )			&& 'true' == $post_var['soliloquy-delay']		? 'true' : 'false';
		$preload	= isset( $post_var['soliloquy-preloader'] )		&& 'true' == $post_var['soliloquy-preloader']	? true : false;

		/** These actions need to be performed if certain settings are set to true */
		if ( empty( $fitvids ) )
			$video = 'false';

		if ( 'true' == $video )
			$css = 'false'; // Set to false regardless when an embedded video is present

		/** Get the pre-script init hook information */
		ob_start();
		do_action( 'tgmsp_before_slider_init', $post_var );
		$callback_preinit = ob_get_clean();

		/** Get the post-script init hook information */
		ob_start();
		do_action( 'tgmsp_after_slider_init', $post_var );
		$callback_postinit = ob_get_clean();

		/** Get the callback script hook information */
		ob_start();
		do_action( 'tgmsp_slider_script', $post_var );
		$callback_script = ob_get_clean();

		/** Get the callback start hook information */
		ob_start();
		do_action( 'tgmsp_callback_start_' . $id );
		$callback_start = ob_get_clean();

		/** Get the callback before hook information */
		ob_start();
		do_action( 'tgmsp_callback_before_' . $id );
		$callback_before = ob_get_clean();

		/** Get the callback after hook information */
		ob_start();
		do_action( 'tgmsp_callback_after_' . $id );
		$callback_after = ob_get_clean();

		/** Get the callback end hook information */
		ob_start();
		do_action( 'tgmsp_callback_end_' . $id );
		$callback_end = ob_get_clean();

		/** Get the callback added hook information */
		ob_start();
		do_action( 'tgmsp_callback_added_' . $id );
		$callback_added = ob_get_clean();

		/** Get the callback removed hook information */
		ob_start();
		do_action( 'tgmsp_callback_removed_' . $id );
		$callback_removed = ob_get_clean();

		/** Prepare the preloading script */
		$script = 'var soliloquy_holder = jQuery(slider).find("img.soliloquy-item-image");';
		$script .= 'if(0 !== soliloquy_holder.length){';
			$script .= 'var soliloquy_images = ([]).concat(soliloquy_holder.splice(0,2), soliloquy_holder.splice(-2,2), jQuery.makeArray(soliloquy_holder));';
			$script .= 'jQuery.each(soliloquy_images, function(i,el){';
				$script .= 'if(typeof jQuery(this).attr("data-soliloquy-src") == "undefined" || false == jQuery(this).attr("data-soliloquy-src")) return;';
				$script .= '(new Image()).src = jQuery(this).attr("data-soliloquy-src");';
				$script .= 'jQuery(this).attr("src", jQuery(this).attr("data-soliloquy-src")).removeAttr("data-soliloquy-src");';
			$script .= '});';
		$script .= '}';

		// Prepare the preloader script if we are using it.
		$preload_script = false;
		if ( $preload )
			$preload_script = 'jQuery(document).ready(function($){$("#soliloquy-container-' . absint( $id ) . '").css({"height":(Math.round($("#soliloquy-container-' . absint( $id ) . '").width() / ' . $ratio . '))});});';

		$init = '';
		$init .= $callback_preinit;
		$init .= '<script type="text/javascript">';
		$init .= $preload_script ? $preload_script : '';
		$init .= 'jQuery("#soliloquy-preview-wrap ' . apply_filters( 'tgmsp_slider_selector', '#soliloquy-' . absint( $id ), $id, $slider, $post_var ) . '").' . $fitvids . 'soliloquy({';
		$init .= 'animation: \'' . $animation . '\',';
		$init .= $slide;
		$init .= 'slideshow: ' . $animate . ',';
		$init .= 'slideshowSpeed: ' . $post_var['soliloquy-speed'] . ',';
		if ( version_compare( Tgmsp::get_instance()->version, '1.3.0', '>=' ) )
			$init .= 'animationSpeed: ' . $post_var['soliloquy-duration'] . ',';
		else
			$init .= 'animationDuration: ' . $post_var['soliloquy-duration'] . ',';
		$init .= 'directionNav: ' . $direction . ',';
		$init .= 'controlNav: ' . $control . ',';
		if ( version_compare( Tgmsp::get_instance()->version, '1.3.0', '>=' ) )
			$init .= 'keyboard: ' . $keyboard . ',';
		else
			$init .= 'keyboardNav: ' . $keyboard . ',';
		$init .= 'mousewheel: ' . $mouse . ',';
		$init .= 'pausePlay: ' . $pauseplay . ',';
		$init .= 'randomize: ' . $randomize . ',';
		if ( version_compare( Tgmsp::get_instance()->version, '1.3.0', '>=' ) )
			$init .= 'startAt: ' . absint( $post_var['soliloquy-number'] ) . ',';
		else
			$init .= 'slideToStart: ' . absint( $post_var['soliloquy-number'] ) . ',';
		$init .= 'animationLoop: ' . $loop . ',';
		$init .= 'pauseOnAction: ' . $action . ',';
		$init .= 'pauseOnHover: ' . $hover . ',';
		$init .= 'controlsContainer: \'' . apply_filters( 'tgmsp_slider_controls', '#soliloquy-container-' . $id, $id, $post_var ) . '\',';
		$init .= 'manualControls: \'' . apply_filters( 'tgmsp_manual_controls', '', $id, $post_var ) . '\',';
		if ( version_compare( Tgmsp::get_instance()->version, '1.3.0', '>=' ) ) {
			$init .= 'multipleKeyboard: ' . $multi . ',';
			$init .= 'video: ' . $video . ',';
			$init .= 'useCSS: ' . $css . ',';
			$init .= 'reverse: ' . $reverse . ',';
			$init .= 'smoothHeight: ' . $smooth . ',';
			$init .= 'touch: ' . $touch . ',';
			$init .= 'initDelay: ' . absint( $post_var['soliloquy-delay'] ) . ',';
		}
		$init .= 'namespace:\'soliloquy-\',';
		$init .= 'selector:\'.soliloquy-slides > li\',';
		$init .= $callback_script;
		$init .= 'start: function(slider) { soliloquySlider' . $id . ' = slider; ' . apply_filters( 'tgmsp_slider_preload', $script, $id, $post_var ) . ' ' . $callback_start . ' },';
		$init .= 'before: function(slider) { ' . $callback_before . ' },';
		$init .= 'after: function(slider) { ' . $callback_after . ' },';
		$init .= 'end: function(slider) { ' . $callback_end . ' },';
		$init .= 'added: function(slider) { ' . $callback_added . ' },';
		$init .= 'removed: function(slider) { ' . $callback_removed . ' }';
		$init .= '});';
		$init .= '</script>';
		$init .= $callback_postinit;

		/** Force IE hover states on embedded videos */
		if ( 'true' == $video && ! empty( $fitvids ) ) {
			$init .= '<!--[if IE]>';
				$init .= '<script type="text/javascript">jQuery(document).ready(function($){$(".soliloquy-container").each(function(i, el){$(el).hover(function(){$(this).addClass("soliloquy-hover");},function(){$(this).removeClass("soliloquy-hover");});});});</script>';
			$init .= '<![endif]-->';
		}

		/** Build the video initialization code if needed */
		if ( $this->youtube || $this->vimeo ) {
			$init .= '<script type="text/javascript">';
				/** Check to make sure the object hasn't already been setup by a previous ajax request */
				$init .= 'var soliloquy_video_data 	= {};'; // Object to store our video data
				$init .= 'var soliloquy_video_count = 0;'; 	// Incremental variable to hold number of video players on the page

				/** Only load the following vars if the YouTube flag is set to true */
				if ( $this->youtube ) {
					$init .= 'var soliloquy_youtube_holder = {};'; 	// Holds all YouTube player IDs
					$init .= 'var soliloquy_youtube_players = {};'; // Holds all the YouTube player objects on the page
				}

				/** Only load the following vars if the Vimeo flag is set to true */
				if ( $this->vimeo ) {
					$init .= 'var soliloquy_vimeo_holder = {};'; 	// Holds all Vimeo player IDs
					$init .= 'var soliloquy_vimeo_players = {};'; 	// Holds all the Vimeo player objects on the page
				}

				/** Store video player ID and type in our object */
				$init .= 'jQuery(document).ready(function($){';
					/** Loop through available slides and find all video instances */
					$init .= '$("#soliloquy-' . $id . ' .soliloquy-slides li:not(.clone)").find("iframe").each(function(i, el){';
						$init .= 'soliloquy_video_data[parseInt(soliloquy_video_count)] = {';
							$init .= 'type: $(this).attr("rel"),';
							$init .= 'id: $(this).attr("id")';
						$init .= '};';
						$init .= 'soliloquy_video_count += parseInt(1);';
					$init .= '});';

					/** Loop through the object and do our stuff */
					$init .= '$.each(soliloquy_video_data, function(i, el){';
						/** Only load if a YouTube video is present */
						if ( $this->youtube ) {
							$init .= 'soliloquy_youtube_holder[el.id] = el.id;';
						}

						/** Only load if a Vimeo video is present */
						if ( $this->vimeo ) {
							$init .= 'if ( "vimeo" == el.type ) {';
								$init .= 'soliloquy_vimeo_holder[el.id] = el.id;';
								$init .= 'soliloquyLoadVimeoVideo(el.id, $);';
							$init .= '}';
						}
					$init .= '});';

					if ( $this->youtube ) {
						/** Delete leftover YT object so all code can load */
						$init .= 'if ( typeof YT == "object" && YT.Player ) delete YT.Player;';

						/** Load the YouTube IFrame API asynchronously */
						$init .= 'var tag = document.createElement("script");';
      					$init .= 'tag.src = "http://www.youtube.com/player_api";';
      					$init .= 'tag.async = true;';
      					$init .= 'var firstScriptTag = document.getElementsByTagName("script")[0];';
      					$init .= 'firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);';
      				}
				$init .= '});';

				/** Only load if a YouTube video is present */
				if ( $this->youtube ) {
      				/** Now initialize the API and add event listeners */
      				$init .= 'function onYouTubePlayerAPIReady() {';
      					$init .= 'jQuery.each(soliloquy_youtube_holder, function(key, el){';
      						$init .= 'delete soliloquy_youtube_players[el];';
      						$init .= 'soliloquy_youtube_players[el] = new YT.Player(el, {';
								$init .= 'events: {';
									$init .= '"onStateChange": soliloquyCreateYTEvent(el)';
								$init .= '}';
							$init .= '});';
      					$init .= '});';
					$init .= '}';

					$init .= 'function soliloquyCreateYTEvent(playerID) {';
						$init .= 'return function(event) {';
							/** If the video is being played or is buffering, pause the slider */
							$init .= 'if ( 1 == event.data || 3 == event.data ) ';
								if ( isset( $post_var['soliloquy-action'] ) && 'true' == $post_var['soliloquy-action'] ) {
									$init .= 'if ( ! soliloquySlider' . $id . '.animating ) ';
										$init .= 'soliloquySlider' . $id . '.pause();';
								} else {
									$init .= 'soliloquySlider' . $id . '.pause();';
								}
						$init .= '}';
					$init .= '}';
				}

				/** Only load if a Vimeo video is present */
				if ( $this->vimeo ) {
					$init .= 'function soliloquyLoadVimeoVideo(playerID, $) {';
						$init .= '$.each(soliloquy_vimeo_holder, function(key, el){';
      						/** Setup the Vimeo player object and add event listeners */
      						$init .= 'delete soliloquy_vimeo_players[el];';
      						$init .= 'soliloquy_vimeo_players[el] = $f(el);';
							$init .= 'soliloquy_vimeo_players[el].addEvent("ready", soliloquyVimeoPausePlay);';
						$init .= '});';
					$init .= '}';

					$init .= 'function soliloquyVimeoPausePlay(playerID) {';
						$init .= 'soliloquy_vimeo_players[playerID].addEvent("play", function(data){';
							if ( isset( $post_var['soliloquy-action'] ) && 'true' == $post_var['soliloquy-action'] ) {
								$init .= 'if ( ! soliloquySlider' . $id . '.animating ) ';
									$init .= 'soliloquySlider' . $id . '.pause();';
							} else {
								$init .= 'soliloquySlider' . $id . '.pause();';
							}
						$init .= '});';
					$init .= '}';
				}
			$init .= '</script>';
		}

		/** Append the re-init to the slider output */
		$slider 	= $slider . $init;
		$scripts 	= array();

		/** Load the Vimeo API here */
		if ( $this->vimeo )
			$scripts[] = 'http://a.vimeocdn.com/js/froogaloop2.min.js';

		/** If the mousewheel option is selected, load the Mousewheel jQuery plugin */
		if ( isset( $post_var['soliloquy-mousewheel'] ) && 'true' == $post_var['soliloquy-mousewheel'] && version_compare( Tgmsp::get_instance()->version, '1.3.0', '>=' ) )
			$scripts[] = plugins_url( '/js/mousewheel.js', Tgmsp::get_file() );

		/** Json encode the new slider code, allow it to be filtered by addons and die */
		$data = array(
			'slider' 	=> $slider,
			'post_data' => $post_var,
			'extra' 	=> $extra,
			'scripts'	=> $scripts
		);
		$data = apply_filters( 'tgmsp_preview_send_to_script', $data, $id, $images, $soliloquy_data, absint( $soliloquy_count ), $post_var );
		echo json_encode( $data );
		die;

	}

	/**
	 * Helper function to generate the correct video embed code and necessary scripts
	 * to facilitate the interactions between the video and the slider.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type The type of video (YouTube or Vimeo)
	 * @param string|int $video The unique video ID
	 * @param int $id The current slider ID
	 * @param int $i The current slide position
	 * @param int $width The width of the slider
	 * @param int $height The height of the slider
	 * @return string $slide Amended slide code with video code attached
	 */
	public function get_video_code( $type = '', $video, $id, $i, $width, $height, $post_var ) {

		/** Generate code based on the type of video being viewed */
		switch ( $type ) {
			case 'youtube' :
				$query_args = apply_filters( 'tgmsp_youtube_query_args', array(
					'enablejsapi' 		=> '1',
					'version'			=> '3',
					'wmode'				=> 'transparent',
					'rel'				=> '0',
					'showinfo'			=> '0',
					'modestbranding'	=> '1'
				), $id, $i, $post_var );
				$slide = '<div class="soliloquy-touch-left"></div><iframe id="soliloquy-video-' . $id . '-' . $i . '" src="' . add_query_arg( $query_args, 'http://www.youtube.com/embed/' . $video ) . '" width="' . absint( $width ) . '" height="' . absint( $height ) . '" frameborder="0" rel="youtube" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe><div class="soliloquy-touch-right"></div>';
				break;
			case 'vimeo' :
				$query_args = apply_filters( 'tgmsp_vimeo_query_args', array(
					'api' 		=> '1',
					'player_id'	=> 'soliloquy-video-' . $id . '-' . $i,
					'wmode'		=> 'transparent',
					'byline'	=> '0',
					'title'		=> '0',
					'portrait'	=> '0'
				), $id, $i, $post_var );
				$slide = '<div class="soliloquy-touch-left"></div><iframe id="soliloquy-video-' . $id . '-' . $i . '" src="' . add_query_arg( $query_args, 'http://player.vimeo.com/video/' . $video ) . '" width="' . absint( $width ) . '" height="' . absint( $height ) . '" frameborder="0" rel="vimeo" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe><div class="soliloquy-touch-right"></div>';
				break;
		}

		/** Return the new slider code with all the necessary video components loaded */
		return $slide;

	}

	/**
	 * Callback function to pause an embedded video when moving to another slide.
	 *
	 * @since 1.0.0
	 */
	public function pause_video() {

		/** Store output in a variable */
		$output = 'var pause_video = jQuery(slider).find("li:not(.clone)");';
		$output .= 'jQuery(pause_video).find("iframe").each(function(i, el){';
			$output .= 'if ( "youtube" == jQuery(this).attr("rel") ) {';
				$output .= 'var yt_player = soliloquy_youtube_players[jQuery(this).attr("id")];';
				$output .= 'if ( typeof yt_player == "undefined" || false == yt_player ) {';
					$output .= 'return;'; // This is to prevent errors when the video hasn't yet initialized but the slider is already proceeding to it
				$output .= '} else {';
					$output .= 'if ( typeof yt_player.getPlayerState == "function" ){';
						$output .= 'if ( 1 == yt_player.getPlayerState() ) ';
							$output .= 'yt_player.pauseVideo();';
					$output .= '}';
				$output .= '}';
			$output .= '}';
			$output .= 'if ( "vimeo" == jQuery(this).attr("rel") ) {';
				$output .= 'var vm_player = soliloquy_vimeo_players[jQuery(this).attr("id")];';
				$output .= 'if ( typeof vm_player == "undefined" || false == vm_player ) {';
					$output .= 'return;';
				$output .= '} else {';
					$output .= 'if ( typeof vm_player.api == "function" )';
						$output .= 'vm_player.api("pause");';
				$output .= '}';
			$output .= '}';
		$output .= '});';

		/** Echo the output */
		echo $output;

	}

	/**
	 * Getter method for retrieving custom slider classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $post_var The current $_POST data
	 * @return array $classes Custom slider classes
	 */
	public function get_custom_slider_classes( $post_var ) {

		$classes = array();

		/** Set the default soliloquy-container */
		$classes[] = 'soliloquy-container';

		/** Set a class for the type of transition being used */
		$classes[] = sanitize_html_class( 'soliloquy-' . strtolower( $post_var['soliloquy-transition'] ), '' );

		/** Now add a filter to addons can access and add custom classes */
		return 'class="' . implode( ' ', apply_filters( 'tgmsp_slider_classes', $classes, absint( $post_var['id'] ), $post_var ) ) . '"';

	}

	/**
	 * Shuffle the associative array of images if the user has chosen to do it.
	 *
	 * @since 1.1.0
	 *
	 * @return array $random Shuffled array of images
	 */
	private function shuffle( $images ) {

		// Return early if the $images passed is not an array.
		if ( ! is_array( $images ) )
			return $images;

		$random = array();
		$keys 	= array_keys( $images );

		// Shuffle the keys and loop through them to create a new, randomized array of images.
		shuffle( $keys );
		foreach ( $keys as $key )
			$random[$key] = $images[$key];

		// Return the randomized image array.
		return $random;

	}

	/**
	 * Removes the fixed height and preloader image once the slider has initialized.
	 *
	 * @since 1.1.0
	 */
	public function preloader( $id ) {

		echo 'jQuery("#soliloquy-container-' . absint( $id ) . '").css({ "background" : "transparent", "height" : "auto" });';

	}

	/**
	 * Adds the preloader class to the slider to signify use of a preloading image.
	 *
	 * @since 1.1.0
	 *
	 * @param array $classes Array of slider classes
	 * @return array $classes Amended array of slider classes
	 */
	public function preloader_class( $classes ) {

		$classes[] = 'soliloquy-preloader';
		return array_unique( $classes );

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