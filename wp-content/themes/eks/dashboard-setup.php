<?php

$dashboard_type = va_get_dashboard_type();
$dashboard_name = va_get_dashboard_name();
$dashboard_author = va_get_dashboard_author();


$args = array(
	'title' => sprintf( __( "%s's %s", APP_TD ), $dashboard_author->display_name, $dashboard_name ),
	'dashboard_type' => $dashboard_type,
	'dashboard_user' => $dashboard_author,
	'is_own_dashboard'=> va_is_own_dashboard()
);

if (is_volunteer())
{
    appthemes_load_template( "page-my-tax-sites.php", $args );
} else {
    appthemes_load_template( "dashboard-$dashboard_type.php", $args );
    ?><div id="sidebar"><?php appthemes_load_template( 'sidebar.php', $args ); ?></div>
    <?php
}