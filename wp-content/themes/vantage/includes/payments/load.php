<?php

add_action( 'after_setup_theme', '_appthemes_load_payments', 999 );

function _appthemes_load_payments() {
	if ( !current_theme_supports( 'app-payments' ) )
		return;

	require dirname( __FILE__ ) . '/load2.php';

	new APP_Order_Summary;

	extract( appthemes_payments_get_args(), EXTR_SKIP );

	if( !empty( $items ) ){
		foreach( $items as $item ){

			if( !isset( $item['type'] ) || !isset( $item['title'] ) )
				continue;

			if( !isset( $item['meta'] ) )
				$item['meta'] = array();

			APP_Item_Registry::register( $item['type'], $item['title'], $item['meta'] );

		}
	}

	if( $options )
		APP_Gateway_Registry::register_options( $options );
}

