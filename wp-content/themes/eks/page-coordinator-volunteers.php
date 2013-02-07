<?php
// Template Name: Coordinator: Volunteers
?>

<div id="main">
    <div class="section-head">
        <h1><?php _e('My Volunteers', APP_TD); ?></h1>
    </div>
    <div class="categories-list">
        
        <?php
        $volunteers = get_volunteers();
        if (count($volunteers) > 0) {
            
            $items = array();
            
            // Store the current Coordinator user. We'll temporarily set the user to each Volunteer below, so we can render the Display Name.
            $u = wp_get_current_user();
            foreach ($volunteers as $post) {
                
                if ($post->post_author) {
                    wp_set_current_user($post->post_author);
                    $items[] = array(
                            'name' => '<a href="' . get_permalink($post->ID) .'" rel="bookmark">'.$current_user->data->user_nicename.'</a>',
                            'username' => $current_user->data->display_name
                        );
                }
            }
            wp_set_current_user($u->ID);
            echo OutputArrayToTable($items, array('Volunteer', 'Username'));
        } else {
            ?><p>You do not yet have any volunteers assigned to your Tax Sites.</p><?php }
        ?>

    </div>
</div>
<?php get_sidebar(); ?>
