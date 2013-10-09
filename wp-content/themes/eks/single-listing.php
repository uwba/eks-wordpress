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
		<li><p class="listing-custom-field"><span class="custom-field-label">Address</span><span class="custom-field-sep">: </span><span class="custom-field-value"><?php the_listing_address(); ?></span></p></li>

                <li><p class="listing-custom-field"><span class="custom-field-label">Phone</span><span class="custom-field-sep">: </span><span class="custom-field-value"><?php echo esc_html( get_post_meta( get_the_ID(), 'phone', true ) ); ?></span></p>
                </li>
                <li><p class="listing-custom-field"><span class="custom-field-label">Hours</span><span class="custom-field-sep">: </span><br/><span class="custom-field-value"><?php echo get_formatted_hours_of_operation( get_post_meta( get_the_ID(), 'app_hoursofoperation', true ) ); ?></span></p>
                </li>
                
	<?php if ( $website ) : ?>
                <li><p class="listing-custom-field"><span class="custom-field-label">Website</span><span class="custom-field-sep">: </span><span class="custom-field-value"><a href="<?php echo esc_url( 'http://' . $website ); ?>" title="<?php _e( 'Website', APP_TD ); ?>" target="_blank"><?php echo esc_html( $website ); ?></a></span></li>
		<li><p class="listing-custom-field"><span class="custom-field-label">Email</span><span class="custom-field-sep">: </span><span class="custom-field-value"><a href="<?php echo  'mailto:' . $email ; ?>" title="<?php _e( 'Email', APP_TD ); ?>" target="_blank"><?php echo esc_html( $email ); ?></a></li>
	<?php endif; ?>


	<?php if ( $facebook or $twitter ) : ?>
		<li><p class="listing-custom-field"><span class="custom-field-label">Follow</span><span class="custom-field-sep">: </span>
			<?php if ( $facebook ) : ?>
                    <div>
                        <a href="<?php echo esc_url( 'http://facebook.com/' . $facebook ); ?>" title="<?php _e( 'Facebook', APP_TD ); ?>" target="_blank"><span class="facebook-icon">Facebook -</span> <span class="facebook-handle"><?php echo esc_html( 'http://facebook.com/' . $facebook ); ?></span></a>
                    </div>
			<?php endif; ?>
			<?php if ( $twitter ) : ?>
                    <div>
                        <a href="<?php echo esc_url( 'http://twitter.com/' . $twitter ); ?>" title="<?php _e( 'Twitter', APP_TD ); ?>" target="_blank"><div class="twitter-icon">Twitter -</div> <span class="twitter-handle">@<?php echo esc_html( $twitter ); ?></span></a>
                    </div>
			<?php endif; ?>
                </li>
	<?php endif; ?>

        </ul>
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

        jQuery(document).ready(function($) {
            var privateFields = ['Site Coordinator Name', 'Site Coordinator Email Address', 'Site Coordinator Phone Number', 'New Tax Preparer Training Required', 'Returning Tax Preparer Training Required', 'Number of Tax Preparers Needed', 'Number of Interpreters Needed', 'Number of Greeters Needed', 'Hours of Operation'];

            $('span.custom-field-label').each(function(){               
                if ($.inArray($(this).text(), privateFields) > -1)
                    $(this).parent().hide();
            });
        });
</script>
