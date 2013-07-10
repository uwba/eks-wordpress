<div id="main">
	<div class="section-head">
		<h1><?php echo $title; ?></h1>
	</div>
<?php
$listings = va_get_dashboard_listings($dashboard_user->ID, (bool) $is_own_dashboard );

if ( $listings->post_count > 0 ) {
	while ( $listings->have_posts() ) : $listings->the_post();

	$post_status = $is_own_dashboard ? 'post-status' : '';
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( $post_status ); ?>>
		<?php if ( $is_own_dashboard ) { ?>
    	<div class="featured-head <?php echo 'post-status-'.get_post_status( get_the_ID() ).'-head'; ?>">
			<h3><?php echo va_get_dashboard_verbiage( get_post_status( get_the_ID() ) ); ?></h3>
        </div>
        <?php va_the_listing_expiration_notice(); ?>
        <?php } ?>

		<?php get_template_part( 'content-listing', get_post_status() ); ?>
	</article>
<?php
	endwhile;
} else {
?>
	<?php if( $is_own_dashboard ) { ?>
	<h3 class="dashboard-none"><?php 
        global $current_user;
        get_currentuserinfo();   
        if ($current_user->roles[0] == 'volunteer') 
            echo 'You have not chosen a Tax Site yet.';
        else
            echo __( 'You have no Tax Sites yet. ', APP_TD) . html_link( va_get_listing_create_url(), __( 'Add one now.', APP_TD ) ); ?>
        </h3>
	<?php } else { ?>
	<h3 class="dashboard-none"><?php printf(  __( '%s has no Tax Sites.', APP_TD ), $dashboard_user->display_name ); // !TODO - Style this text ?></h3>
<?php
	}
}

if ( $listings->max_num_pages > 1 ) { ?>
	<nav class="pagination">
		<?php appthemes_pagenavi( $listings ); ?>
	</nav>
<?php
}
?>
</div><!-- /#content -->
