<div id="main">

<?php the_post(); ?>

<?php do_action( 'appthemes_notices' ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php the_listing_image_gallery(); ?>

	<?php appthemes_before_post_title( VA_LISTING_PTYPE ); ?>
	<h1><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
	<p class="author"><?php printf( __( 'Added by %s', APP_TD ), va_get_the_author_listings_link() ); ?> </p>
	

	<?php appthemes_after_post_title( VA_LISTING_PTYPE ); ?>

	<?php $website = get_post_meta( get_the_ID(), 'website', true ); ?>
	<?php $email = get_post_meta( get_the_ID(), 'email', true ); ?>
	<?php $facebook = get_post_meta( get_the_ID(), 'facebook', true ); ?>
	<?php $twitter = get_post_meta( get_the_ID(), 'twitter', true ); ?>

	<ul>
		<li class="address"><?php the_listing_address(); ?></li>
		<li class="phone"><strong><?php echo esc_html( get_post_meta( get_the_ID(), 'phone', true ) ); ?></strong></li>
	<?php if ( $website ) : ?>
		<li id="listing-website"><a href="<?php echo esc_url( 'http://' . $website ); ?>" title="<?php _e( 'Website', APP_TD ); ?>" target="_blank"><?php echo esc_html( $website ); ?></a></li>
		<li id="listing-email"><a href="<?php echo  'mailto:' . $email ; ?>" title="<?php _e( 'Email', APP_TD ); ?>" target="_blank"><?php echo esc_html( $email ); ?></a></li>
	<?php endif; ?>
	</ul>

	<?php if ( $facebook or $twitter ) : ?>
		<div id="listing-follow">
			<p><?php _e( 'Follow:', APP_TD ); ?></p>
			<?php if ( $facebook ) : ?>
			<a href="<?php echo esc_url( 'http://facebook.com/' . $facebook ); ?>" title="<?php _e( 'Facebook', APP_TD ); ?>" target="_blank"><div class="facebook-icon">Facebook</div></a>
			<?php endif; ?>
			<?php if ( $twitter ) : ?>
			<a href="<?php echo esc_url( 'http://twitter.com/' . $twitter ); ?>" title="<?php _e( 'Twitter', APP_TD ); ?>" target="_blank"><div class="twitter-icon">Twitter -</div> <span class="twitter-handle">@<?php echo esc_html( $twitter ); ?></span></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="listing-fields">
		<?php the_listing_fields(); ?>
	</div>

<!--	<div class="single-listing listing-faves">
		<?php //the_listing_faves_link(); ?>
	</div>-->

	<div class="listing-actions">
		<?php the_listing_edit_link(); ?>
		<?php //the_listing_claimable_link(); ?>
		<?php //the_listing_purchase_link(); ?>
	</div>

	<div class="listing-share">
		<?php if ( function_exists( 'sharethis_button' ) ) sharethis_button(); ?>
	</div>

	<hr />
	<!--<div class="tags"><?php //the_listing_tags( '<span>' . __( 'Tags:', APP_TD ) . '</span> ' ); ?></div>-->

	<?php the_listing_files(); ?>

</article>

</div><!-- /#main -->

<div id="sidebar">
<?php get_sidebar( 'single-listing' ); ?>
</div>

    <script type="text/javascript">
        jQuery(document).ready(function($){
            var text = $('#listing-custom-field-hours-of-operation span.custom-field-value').html().split(' ');
            console.log(text);
            var new_next = '';
            for(var i=0;i<text.length;i++){
                if(i%12 == 0) { new_next += '<br>'}
                new_next += text[i] + ' ';
            }
            $('#listing-custom-field-hours-of-operation span.custom-field-value').html(new_next);

        });
    </script>