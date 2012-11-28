<?php
// Template Name: Coordinator: Calendar
?>
<div id="main">
	<div class="section-head">
		<h1><?php _e('Coordinator Calendar', APP_TD); ?></h1>
	</div>
	<div class="categories-list">
		<?php coordinator_calendar(); ?>
		
	</div>
		
	<a href="<?php echo admin_url('post-new.php?post_type=custom_events');?>">Add event</a>
</div>

	<?php get_sidebar(); ?>
