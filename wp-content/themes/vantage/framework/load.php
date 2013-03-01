<?php

define( 'APP_FRAMEWORK_DIR', dirname(__FILE__) );

// scbFramework
require dirname( __FILE__ ) . '/scb/load.php';
scb_init();

require dirname( __FILE__ ) . '/kernel/functions.php';

appthemes_load_textdomain();

require dirname( __FILE__ ) . '/kernel/hooks.php';

require dirname( __FILE__ ) . '/kernel/view-types.php';
require dirname( __FILE__ ) . '/kernel/view-edit-profile.php';

function _appthemes_load_features() {

	if ( current_theme_supports( 'app-wrapping' ) )
		require dirname( __FILE__ ) . '/includes/wrapping.php';

	if ( current_theme_supports( 'app-geo' ) )
		require dirname( __FILE__ ) . '/includes/geo.php';

	if ( current_theme_supports( 'app-form-builder' ) )
		require dirname( __FILE__ ) . '/custom-forms/form-builder.php';

	if ( current_theme_supports( 'app-login' ) ) {
		require dirname( __FILE__ ) . '/includes/views-login.php';

		list( $templates ) = get_theme_support( 'app-login' );

		new APP_Login( $templates['login'] );
		new APP_Registration( $templates['register'] );
		new APP_Password_Recovery( $templates['recover'] );
		new APP_Password_Reset( $templates['reset'] );
	}

	if ( is_admin() && current_theme_supports( 'app-versions' ) )
		require dirname( __FILE__ ) . '/admin/versions.php';

	if ( current_theme_supports( 'app-term-counts' ) )
		require dirname( __FILE__ ) . '/includes/term-counts.php';
}

// Breadcrumbs plugin
if ( !is_admin() && !function_exists( 'breadcrumb_trail' ) ) {
	require dirname( __FILE__ ) . '/kernel/breadcrumb-trail.php';
}

if ( is_admin() ) {
	require dirname( __FILE__ ) . '/admin/functions.php';

	require dirname( __FILE__ ) . '/admin/class-dashboard.php';
	require dirname( __FILE__ ) . '/admin/class-tabs-page.php';

	if ( version_compare( $GLOBALS['wp_version'], '3.5-alpha', '<' ) ) {
		require dirname( __FILE__ ) . '/admin/taxonomy-columns.php';
	}
}

add_filter( 'wp_title', 'appthemes_title_tag', 9 );

add_action( 'wp_head', 'appthemes_favicon' );
add_action( 'admin_head', 'appthemes_favicon' );

add_action( 'after_setup_theme', '_appthemes_load_features', 999 );

