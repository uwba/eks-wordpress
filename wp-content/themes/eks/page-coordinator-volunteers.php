<?php
// Template Name: Coordinator: Volunteers
?>

<div id="main">
    <div class="section-head">
        <h1><?php _e('Volunteers', APP_TD); ?></h1>
    </div>
    <div class="categories-list">
        
        <?php
        $volunteers = get_volunteers();
        if (count($volunteers) > 0) {
            $u = wp_get_current_user();
            foreach ($volunteers as $post) {
                if ($post->post_author) {
                    wp_set_current_user($post->post_author);
                    //			setup_postdata(get_userdata($post->post_author));
                    //				get_template_part('content-volunteer');
                    ?>
                    <article>
                        <div class="info">
                            <a href="<?php the_permalink($post->ID); ?>" rel="bookmark"><?php echo ($current_user->data->display_name ? $current_user->data->display_name : $current_user->data->name); ?></a>
                        </div>
            <!--			<div class="action"><input type="checkbox" name="volunteers[]" value="<?php echo $user_ID ?>" /></div>-->
                    </article>
                    <?php
                }
            }
            wp_set_current_user($u->ID);
        } else {
            ?><p>You do not yet have any volunteers assigned to your Tax Sites.</p><?php }
        ?>

    </div>
</div>
<?php get_sidebar(); ?>
