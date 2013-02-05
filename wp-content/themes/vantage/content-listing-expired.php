<?php the_listing_thumbnail(); ?>

<div class="review-meta">
	<?php the_listing_star_rating(); ?>

	<p class="reviews"><?php the_review_count(); ?></p>
</div>

<?php appthemes_before_post_title( VA_LISTING_PTYPE ); ?>
<h2><?php the_title(); ?></h2>
<?php appthemes_after_post_title( VA_LISTING_PTYPE ); ?>

<p class="listing-cat"><?php the_listing_category(); ?></p>
<p class="listing-phone"><?php echo esc_html( get_post_meta( get_the_ID(), 'phone', true ) ); ?></p>
<p class="listing-address"><?php the_listing_address(); ?></p>

<p class="listing-description"><strong><?php _e( 'Description:', APP_TD ); ?></strong> <?php the_excerpt(); ?></p>

<div class="expired-notice">
	<?php _e( 'This listing has expired.', APP_TD ); ?>
	<?php the_listing_edit_link( 0, __( 'Renew Listing!', APP_TD ) ); ?>
</div>
