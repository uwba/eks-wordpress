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
            
            // Store the current Coordinator user. We'll temporarily set the user to each Volunteer below, so we can render the Display Name.
            $u = wp_get_current_user();
            foreach ($volunteers as $post) {
                
                if ($post->post_author) {
                    wp_set_current_user($post->post_author);
                    ?>
                    <article>
                        <div class="info">
                            <a href="<?php the_permalink($post->ID); ?>" rel="bookmark"><?php echo $current_user->data->display_name ?></a> (<?php echo $current_user->data->user_nicename; ?>)
                        </div>
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
