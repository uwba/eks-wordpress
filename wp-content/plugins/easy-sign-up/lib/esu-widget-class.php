<?php
/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'esu_load_widgets' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function esu_load_widgets() {
	register_widget( 'EsuWidget_Widget' );
}

/**
 * Easy sign up Widget  class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class EsuWidget_Widget extends WP_Widget {

  /**	
   * Widget setup
   */
  function EsuWidget_Widget() {
    include ESU_PATH.'lib/includes/esu-widget-setup.php';
  }

  /**
   * 
   * How to display the widget on the screen.
   */ 
	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		/* Our variables from the widget settings. */
		$title = apply_filters( 'widget_title', $title );
		/* Before widget (defined by themes). */
		echo $before_widget;
		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )	echo $before_title . $title . $after_title;
		$form_id      ='esu';
		/* The Display the form */
		// esu_form_html($form_id='esu',$title=false, $fnln=true, $phone=true, $esu_label=null, $esu_class='', $old_form=false)
		$fnln  = (isset($fnln) ) ? $fnln  : false ;
		$phone = (isset($phone)) ? $phone : false ;
		$label = (isset($label)) ? $label : null ;
		echo EsuForms::esu_form_html($form_id,false,$fnln,$phone,$label,'esu-widget');
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/** 
	 * 	
	 * 	Update the widget settings.
	 */	
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['fnln'] = strip_tags( $new_instance['fnln'] );
		$instance['phone'] = strip_tags( $new_instance['phone'] );
		$instance['label'] = strip_tags( $new_instance['label'] );
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array( 
			'title' => ESU_NAME,
			'label' => null,
			'phone' => '1',
			'fnln' => '1'
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','esu_lang'); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<!-- Form Label: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'label' ); ?>"><?php _e('Hidden Label:','esu_lang'); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'label' ); ?>" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $instance['label']; ?>" />
		</p>
		<p class="description"><?php _e("The label will be included in a hidden form field, allowing you to keep track of different campaigns.",'esu_lang') ?></p>
	<?php	/* New options: $fnln,$phone,$old_form,$esu_class	*/ ?>
		<p> 
			<label><input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name( 'fnln' );  ?>" value="1" <?php if($instance['fnln'] == '1'){  echo 'checked="checked"';} ?>> <?php _e('Separate the first and last name fields','esu_lang'); ?></label><br>
			<label><input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name( 'phone' ); ?>" value="1" <?php if($instance['phone']== '1'){ echo 'checked="checked"';} ?>> <?php _e('Show phone the field','esu_lang'); ?></label>
		</p>
	<?php
	}

}