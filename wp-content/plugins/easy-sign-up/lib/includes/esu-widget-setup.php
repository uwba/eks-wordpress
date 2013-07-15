<?php
	
		/* Widget settings. */
		$widget_ops = array( 
			'classname' => 'easysignup', 
			'description' => sprintf(__('A widget that displays the %s form.','esu_lang'),ESU_NAME) );

		/* Widget control settings. */
		$control_ops = array('id_base' => 'easysignup-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'easysignup-widget',ESU_NAME, $widget_ops, $control_ops );
