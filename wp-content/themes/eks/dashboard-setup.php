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


if (is_volunteer()) { ?>
<div id="main">
	<div class="section-head">
		<h1><?php _e('Volunteer Dashboard', APP_TD); ?></h1>
	</div>
        <div class="categories-list">
            <p>This is the volunteer dashboard landing page.</p>
        </div>     
</div>
<?php } else {   
    appthemes_load_template( "dashboard-$dashboard_type.php", $args );
} ?>

<div id="sidebar"><?php appthemes_load_template( 'sidebar.php', $args ); ?></div>
