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
		<h1>My EKS Dashboard</h1>
	</div>
        <div class="categories-list">
            <p>Thanks for joining the Earn It! Keep It! Save It! (EKS) coalition! You are part of a team of volunteers around the Bay Area helping to prepare thousands of tax returns for low income taxpayers in your community.</p>  
            <p>Your dashboard is meant to be a helpful resource for you. From here you can use the links to the right to access your tax site and training information, email your site coordinator, upload documents to share and make changes to your account.</p>
            <p>If you have any questions, you may contact your Site Coordinator or your <a href="/volunteer-registration">County Volunteer Maestro</a>.</p>
            <p><a href="/volunteer-registration">General volunteer information</a></p>
        </div>     
</div>
<?php } else {   
    appthemes_load_template( "dashboard-$dashboard_type.php", $args );
} ?>

<div id="sidebar"><?php appthemes_load_template( 'sidebar.php', $args ); ?></div>
