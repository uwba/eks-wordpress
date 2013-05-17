<div id="main">

    <?php appthemes_before_blog_loop(); ?>

    <?php while (have_posts()) : the_post(); ?>

        <?php appthemes_before_blog_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <?php appthemes_before_blog_post_title(); ?>

            <h1 class="post-heading"><a href="<?php the_permalink(); ?>" rel="bookmark"></a><span class="left-hanger"><?php the_title(); ?></span></a></h1>
            <?php // comments_popup_link( "0", "1", "%", "comment-count" ); ?>

            <?php appthemes_after_blog_post_title(); ?>


            <section class="overview">
                <?php appthemes_before_blog_post_content(); ?>
                <?php the_content(); ?>

                <?php
                if (!empty($listing_id)) {
                    $terms = get_the_terms($listing_id, VA_LISTING_CATEGORY);
                    $cat = get_the_listing_category($listing_id);

                    // The County will only be set for County Public trainings
                    if ($cat) {
                        $category = html_link(get_term_link($cat), $cat->name);
                        ?>
                        <p class="listing-cat"><strong>Training County: </strong><?php echo $category; ?></p>
                        <?php
                    }
                }
                ?>
                <p><strong>Type:</strong><br/> <?php echo esc_html(get_post_meta(get_the_ID(), 'type', true)); ?></p>
                <p><strong>Address:</strong><br/> <?php echo esc_html(get_post_meta(get_the_ID(), 'address', true)); ?></p>
                <p><strong>Date(s):</strong><br/> <?php echo esc_html(get_post_meta(get_the_ID(), 'date', true)); ?></p>
                <p><strong>Time(s):</strong><br/> <?php echo esc_html(get_post_meta(get_the_ID(), 'times', true)); ?></p>
                <p><strong>Special Instructions:</strong><br/> <?php echo esc_html(get_post_meta(get_the_ID(), 'special_instructions', true)); ?></p>

                <?php appthemes_after_blog_post_content(); ?>
            </section>
            <br><br>
    <!--<small>Created at <?php // va_the_post_byline();   ?></small>-->
            <?php //edit_post_link( __( 'Edit', APP_TD ), '<span class="edit-link">', '</span>' );   ?>	
            <?php
            global $current_user, $user_ID, $post;
            get_currentuserinfo();
            if ($post->post_author == $user_ID) {
                ?>
                <span class="edit-link"><a href="<?php echo site_url('edit/?postid=' . get_the_ID()) ?>">Edit</a></span>
            <?php } ?>


            <?php comments_template(); ?>

        </article>

        <?php appthemes_after_blog_post(); ?>

    <?php endwhile; ?>

    <?php appthemes_after_blog_loop(); ?>

</div><!-- /#main -->


<?php get_sidebar(app_template_base()); ?>
