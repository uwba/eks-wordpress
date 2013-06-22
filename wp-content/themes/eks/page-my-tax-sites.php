<?php
// Template Name: My Tax Sites
?>
<div id="main">
	<div class="section-head">
		<h1><?php _e('My Tax Site', APP_TD); ?></h1>
	</div>
    

    
	<div class="categories-list">
            <p>If you would like to change your tax site or training assignment, <a href="/volunteer-registration">click here</a>.
		<?php
		//echo va_cat_menu_drop_down( 'dir' );
		$tax_sites = get_volunteer_tax_sites();
//		var_dump($tax_sites);
		if (count($tax_sites)) {
			$args = array('numberposts' => -1, 'post_type' => 'listing', 'post__in' => array_keys($tax_sites));
			$myposts = get_posts($args);
			foreach ($myposts as $post) {
				setup_postdata($post);
	//			the_post();
				?><article id="post-<?php the_ID(); ?>" class="post-<?php the_ID(); ?> listing type-listing status-publish hentry"><?php
				get_template_part('content-listing');
				?>
			<p class="listing-description"><strong>Position: </strong><?php echo implode(', ', $tax_sites[get_the_ID()]);?></p></article><?php
			}
		} else {
			?><p>No Tax Sites selected. Please <a href="<?php echo site_url('volunteer-registration'); ?>">register</a></p><?php
		}
		?>
	</div>

</div>

	<?php get_sidebar(); ?>
