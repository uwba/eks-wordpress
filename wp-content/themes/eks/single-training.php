<div id="main">

	<?php appthemes_before_blog_loop(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

	<?php appthemes_before_blog_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		
			<?php appthemes_before_blog_post_title(); ?>

		<h1 class="post-heading"><a href="<?php the_permalink(); ?>" rel="bookmark"></a><span class="left-hanger"><?php the_title(); ?></span></a></h1>
			<?php // comments_popup_link( "0", "1", "%", "comment-count" ); ?>

			<?php appthemes_after_blog_post_title(); ?>
			
		
		<section class="overview">
			<?php appthemes_before_blog_post_content(); ?>
			<?php the_content(); ?>

<?php $terms = get_the_terms( $listing_id, VA_LISTING_CATEGORY ); 
$cat = get_the_listing_category( $listing_id );
if ($cat) {
	$category = html_link(get_term_link( $cat ), $cat->name);
} else {
	$tax_site = get_post(get_post_meta(get_the_ID(), 'tax_site', true));
	$category = '<a href="' . get_permalink($tax_site->ID) .'">' . $tax_site->post_title . '</a>';
}




?>
<p class="listing-cat"><strong>Training County: </strong><?php echo $category; ?></p>
<?php $tax_site = get_post_meta( get_the_ID(), 'tax_site', true ); 
if ($tax_site) {
$tax_site = get_post($tax_site);
?>
<p class="">Tax site: <a href="<?php echo get_permalink($tax_site->ID)?>"><?php echo $tax_site->post_title; ?></a></p>
<?php } ?>
<p class="">Type: <?php echo esc_html( get_post_meta( get_the_ID(), 'type', true ) ); ?></p>
<p class="">Contact: <?php echo esc_html( get_post_meta( get_the_ID(), 'contact', true ) ); ?></p>
<p class="">Location: <?php echo esc_html( get_post_meta( get_the_ID(), 'location', true ) ); ?></p>
<p class="">Directions: <?php echo esc_html( get_post_meta( get_the_ID(), 'directions', true ) ); ?></p>
<p class="">Bring: <?php echo esc_html( get_post_meta( get_the_ID(), 'bring', true ) ); ?></p>
<p class="">Training files:
    <?
    $files = get_posts(array('post_type' => 'attachment', 'post_parent' => get_the_ID()));
    $items = array();
    foreach ($files as $file) {
    $item = array();
    $item['title'] = '<a href="' . get_permalink($file->ID) . '" title="' . $file->post_title . '">' . truncate($file->post_title, 100, FALSE, TRUE) . '</a>';
    $items[] = $item;
    }
    echo OutputArrayToTable($items, array('Title'));
    ?>
</p>

			<?php  appthemes_after_blog_post_content(); ?>
		</section>
		
	<!--<small>Created at <?php // va_the_post_byline(); ?></small>-->
	<?php //edit_post_link( __( 'Edit', APP_TD ), '<span class="edit-link">', '</span>' ); ?>	
		<?php 
		global $current_user, $user_ID, $post;		get_currentuserinfo();
		if ($post->post_author  == $user_ID) {?>
		<span class="edit-link"><a href="<?php echo site_url('edit/?postid='.get_the_ID())?>">Edit</a></span>
		<?php } ?>

	
    <?php comments_template(); ?>

	</article>

	<?php appthemes_after_blog_post(); ?>

	<?php endwhile; ?>

	<?php appthemes_after_blog_loop(); ?>

</div><!-- /#main -->


	<?php get_sidebar( app_template_base() ); ?>
