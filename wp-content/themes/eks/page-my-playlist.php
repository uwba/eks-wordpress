<?php
// Template Name: Volunteer: Playlist
?>
<?php wp_enqueue_script('volunteer-registration', '/wp-content/plugins/volunteer/js/playlist.js'); ?>
<div id="main">
	<div class="section-head">
		<h1><?php _e('VITA Video Playlist', APP_TD); ?></h1>
	</div>
	<div class="categories-list">
		<div id="player">
        </div>
	</div>

	<a target="_blank" class="youtube-link" href="http://www.youtube.com/user/mlb">Watch on YouTube</a>
</div>

	<?php get_sidebar(); ?>


