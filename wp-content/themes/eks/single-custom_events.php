<div id="main">

	<?php appthemes_before_blog_loop(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

	<?php appthemes_before_blog_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		
			<?php appthemes_before_blog_post_title(); ?>

			<h1 class="post-heading"><span class="left-hanger"><?php the_title(); ?></span></h1>
			<?php // comments_popup_link( "0", "1", "%", "comment-count" ); ?>

			<?php appthemes_after_blog_post_title(); ?>
			
		
		<section class="overview">
			<?php appthemes_before_blog_post_content(); ?>
			<?php the_content(); ?>
			<?php 
			global $post;
			global $current_user, $user_ID;	get_currentuserinfo(); 
//			$info = get_post_meta(get_the_ID());
			$date = get_post_meta(get_the_ID(), 'date', TRUE);
			$date = date('F d, Y', mktime(0, 0, 0, substr($date, 4, 2), substr($date, 6, 2), substr($date, 0, 4)));
			$shift = get_post_meta(get_the_ID(), 'shift', TRUE);
			$tax_site = get_post_meta(get_the_ID(), 'tax_site', TRUE);
			$tax_site = get_post($tax_site);
			$tax_site = '<a href="'.get_permalink($tax_site).'">'. get_the_title($tax_site) . '</a>';
			$volunteer = get_post_meta(get_the_ID(), 'volunteer', TRUE);
			if ($volunteer) {
				$volunteer = get_userdata($volunteer);//var_dump($post);
				$volunteer = $volunteer->display_name . ' ';
				$status = get_post_meta(get_the_ID(), 'status', TRUE);
				if (!$status) {
					$status = 'pending';
				}
				$volunteer .= "({$status}) ";
				if ($current_user->roles[0] == 'coordinator' && $user_ID == $post->post_author){
//					$status = get_post_meta(get_the_ID(), 'status', TRUE);
					if ($status != 'approved') {
						$volunteer .= '<a class="approve" href="' . admin_url('admin-ajax.php') . '">Approve</a> | ';
					}
					
					$volunteer .= '<a class="decline" href="' . admin_url('admin-ajax.php') . '">Decline</a>';
				}
				
			} else {
				$volunteer = 'No volunteer assigned ';
				if ($current_user->roles[0] == 'volunteer') {
					$volunteer .= '<a class="take-part" href="' . admin_url('admin-ajax.php') . '">Take Part</a>';
				}
			}
			
//			var_dump($info);
			?>
			<p><span>Date:</span> <?= $date ?></p>
			<p><span>Shift:</span> <?= $shift ?></p>
			<p><span>Tax Site:</span> <?= $tax_site ?></p>
			<p><span>Volunteer:</span> <span><?= $volunteer ?></span></p>
			<?php appthemes_after_blog_post_content(); ?>
		</section>
		
	<small>Added at <?php va_the_post_byline(); ?></small>
	<?php //edit_post_link( __( 'Edit', APP_TD ), '<span class="edit-link">', '</span>' ); ?>
    <span class="edit-link"><a href="<?php echo site_url('coordinator-event/?postid='.get_the_ID())?>">Edit</a></span>
	

	
    <?php comments_template(); ?>

	</article>

	<?php appthemes_after_blog_post(); ?>

	<?php endwhile; ?>

	<?php appthemes_after_blog_loop(); ?>
	
	<script>
		jQuery(document).ready(function($){
			$('.take-part').click(function(e){
				e.preventDefault();
				node = $(this).parent();
				$.post(this.href,{
					action:'take_part',
					post_ID: <?php the_ID(); ?>
				}, function(){
					node.replaceWith('Please wait for coordinator approving');
				}, 'json');
			});
			$('.approve').click(function(e){
				e.preventDefault();
				node = $(this).parent();
				$.post(this.href,{
					action:'approve',
					post_ID: <?php the_ID(); ?>
				}, function(){
					node.html('Approved');
				}, 'json');
			});
			$('.decline').click(function(e){
				e.preventDefault();
				node = $(this).parent();
				$.post(this.href,{
					action:'decline',
					post_ID: <?php the_ID(); ?>
				}, function(){
					node.replaceWith('Declined');
				}, 'json');
			});
		});
	</script>

</div><!-- /#main -->


	<?php get_sidebar( app_template_base() ); ?>
