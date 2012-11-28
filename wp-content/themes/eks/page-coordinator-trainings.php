<?php
// Template Name: Coordinator: Trainings
?>
<div id="main">
	<div class="section-head">
		<h1><?php _e('My Trainings', APP_TD); ?></h1>
	</div>

	<div class="categories-list">
		<?php
			global $current_user, $user_ID;
			get_currentuserinfo();

			$args = array('numberposts' => -1, 'post_type' => 'training', 'author' => $user_ID);
			$myposts = get_posts($args);
			
			foreach ($myposts as $post) {
				setup_postdata($post);
				// the_post();
				// var_dump($post);
				?><article id="post-<?php the_ID(); ?>" class="post-<?php the_ID(); ?> training type-training status-publish hentry"><?php
				get_template_part('content-training');
				?></article><?php
			}
			if (!count($myposts)) {
			?><p>No Training exist</p><?php
			} ?>
	</div>

	<a href="<?=site_url('edit')?>">Add Training</a>
</div>

	<?php get_sidebar(); ?>
