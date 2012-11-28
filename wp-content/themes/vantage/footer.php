<div id="footer" class="container">
	<div class="row">
		<?php dynamic_sidebar( 'va-footer' ); ?>
	</div>
</div>
<div id="post-footer" class="container">
	<div class="row">
		<?php wp_nav_menu( array(
			'container' => false,
			'theme_location' => 'footer',
			'fallback_cb' => false
		) ); ?>

		<div id="theme-info">&copy; Vantage Theme, business directory software created by <a href="http://www.appthemes.com" target="_blank">AppThemes</a>, powered by <a href="http://www.wordpress.org" target="_blank">WordPress</a>.</div>
	</div>
</div>
