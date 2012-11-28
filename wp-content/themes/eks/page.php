<div id="main">	

	<?php appthemes_before_page_loop(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

	<?php appthemes_before_page(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php appthemes_before_page_title(); ?>

		<header>
			<h1><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		</header>

		<?php appthemes_after_page_title(); ?>

		<section id="overview">

			<?php appthemes_before_page_content(); ?>

			<?php the_content(); ?>

			<?php appthemes_after_page_content(); ?>

		</section>
	<?php edit_post_link( __( 'Edit', APP_TD ), '<span class="edit-link">', '</span>' ); ?>
	</article>

	<?php appthemes_after_page(); ?>

	<?php endwhile; ?>

	<?php appthemes_after_page_loop(); ?>

</div><!-- /#main -->

<!--<div id="sidebar" class="threecol last">-->
	<?php
if (is_page_template('page-my-registration.php') || strpos('-'.$_SERVER["REQUEST_URI"], 'volunteer') || is_page_template('form-registration.php') || is_page_template('form-login.php') || strpos('-'.$_SERVER["REQUEST_URI"], 'coordinator')) {
	get_sidebar( ); //app_template_base() 
	?>
<style>
	/*#content-inner {
		padding-right:305px;
		background: url('images/bg-stage-shade.png') repeat-x 0 0;
	}*/
</style>
<?php
} else {
	?>
<style>
#content-inner {
	padding-right:0;
	
}
#content-mid {
	background-image: none;
}
</style>
<?php	
}
?>
<!--</div>-->	