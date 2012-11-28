<?php

require APP_FRAMEWORK_DIR . '/load-p2p.php';

require dirname( __FILE__ ) . '/currencies.php';

// Orders
require dirname( __FILE__ ) . '/order-class.php';
require dirname( __FILE__ ) . '/order-draft-class.php';
require dirname( __FILE__ ) . '/order-receipt-class.php';
require dirname( __FILE__ ) . '/order-functions.php';
require dirname( __FILE__ ) . '/order-upgrade.php';

require_once APP_FRAMEWORK_DIR . '/includes/tables.php';
require dirname( __FILE__ ) . '/order-view.php';

require dirname( __FILE__ ) . '/item-registry.php';

if( is_admin() ){
	require_once APP_FRAMEWORK_DIR . '/admin/class-meta-box.php';
	require_once APP_FRAMEWORK_DIR . '/admin/class-tabs-page.php';

	require dirname( __FILE__ ) . '/admin/admin.php';
	require dirname( __FILE__ ) . '/admin/orders.php';
	require dirname( __FILE__ ) . '/admin/settings.php';
}

/// Gateways
require dirname( __FILE__ ) . '/gateways/gateway-class.php';
require dirname( __FILE__ ) . '/gateways/boomerang-class.php';
require dirname( __FILE__ ) . '/gateways/gateway-registry.php';
require dirname( __FILE__ ) . '/gateways/gateway-functions.php';

require dirname( __FILE__ ) . '/gateways/paypal/paypal.php';
require dirname( __FILE__ ) . '/gateways/paypal/paypal-pdt.php';



if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
	require dirname( __FILE__ ) . '/gateways/test.php';
	require dirname( __FILE__ ) . '/gateways/bank-transfer.php';
}

function appthemes_payments_get_args(){

	if( !current_theme_supports( 'app-payments' ) )
		return array();

	list($args) = get_theme_support( 'app-payments' );
	$defaults = array(
		'items' => array(),
		'items_post_types' => array( 'post' ),
		'options' => false
	);

	return wp_parse_args( $args, $defaults );

}
