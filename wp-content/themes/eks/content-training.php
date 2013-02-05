<h2><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

<?php $terms = get_the_terms( $listing_id, VA_LISTING_CATEGORY ); 
$cat = get_the_listing_category( $listing_id );
if ($cat) {
	$category = html_link(get_term_link( $cat ), $cat->name);
} else {
	$tax_site = get_post(get_post_meta(get_the_ID(), 'tax_site', true));
	$category = '<a href="' . get_permalink($tax_site->ID) .'">' . $tax_site->post_title . '</a>';
}




?>
<p class="listing-cat"><strong>County or Tax Site: </strong><?php echo $category; ?></p>

<p class="listing-phone"><?php echo esc_html( get_post_meta( get_the_ID(), 'phone', true ) ); ?></p>
<p class="listing-address"><?php the_listing_address(); ?></p>
<p class="listing-description"><strong><?php _e( 'Description:', APP_TD ); ?></strong> <?php the_excerpt(); ?> <?php echo html_link( get_permalink(), __( 'Read more...', APP_TD ) ); ?></p>
