<?php global $va_options; ?>

<?php the_listing_thumbnail(); ?>



<?php appthemes_before_post_title( VA_LISTING_PTYPE ); ?>
<h2><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
<?php appthemes_after_post_title( VA_LISTING_PTYPE ); ?>

<p class="listing-cat"><?php the_listing_category(); ?></p>
<?php if ( function_exists('sharethis_button') && $va_options->listing_sharethis ): ?>
	<div class="listing-sharethis"><?php sharethis_button(); ?></div>
	<div class="clear"></div>
<?php endif; ?>
<p class="listing-phone"><?php echo esc_html( get_post_meta( get_the_ID(), 'phone', true ) ); ?></p>
<p class="listing-address"><?php the_listing_address(); ?></p>
<p class="listing-description"><strong><?php _e( 'Description:', APP_TD ); ?></strong> <?php the_excerpt(); ?> <?php echo html_link( get_permalink(), __( 'Read more...', APP_TD ) ); ?></p>
