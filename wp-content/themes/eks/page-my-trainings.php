<?php
// Template Name: My Trainings
?>
<div id="main">
	<div class="section-head">
		<h1><?php _e('My Training', APP_TD); ?></h1>
	</div>

	<div class="categories-list">
            <p>If you would like to change your tax site or training assignment, <a href="/volunteer-registration">click here</a>.
		<?php
		//echo va_cat_menu_drop_down( 'dir' );
		$volunteer = get_volunteer();
		
		$trainings = get_post_meta($volunteer->ID, 'training');

		if (count($trainings)) {
			$args = array('numberposts' => -1, 'post_type' => 'training', 'post__in' => $trainings);
			$myposts = get_posts($args);
			foreach ($myposts as $post) {
				setup_postdata($post);
				// the_post();
				// var_dump($post);
				?><article id="post-<?php the_ID(); ?>" class="post-<?php the_ID(); ?> listing training type-training status-publish hentry"><?php
				get_template_part('content-training');
				?></article><?php
			}
		} else {
			?><p>No Training selected. Please <a href="<?php echo site_url('volunteer-registration'); ?>">register</a></p><?php
		}
		
		
		?>
	</div>

</div>

	<?php get_sidebar(); ?>
