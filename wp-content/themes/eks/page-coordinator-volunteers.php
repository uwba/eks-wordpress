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
                    
                    // Cloned from content-volunteer.php
                    $volunteer_tax_sites = get_volunteer_tax_sites($user_ID);
                    $tax_site_ids = array_keys($volunteer_tax_sites);
                    $role = $volunteer_tax_sites[$tax_site_ids[0]][0];
                    $tax_site = get_post($tax_site_ids[0]);   
                    $tax_site_link = '<a href="/listings/' . $tax_site->post_name . '">' . $tax_site->post_title . '</a>';
    
                    $items[] = array(
                            'name' => '<a href="' . get_permalink($post->ID) .'" rel="bookmark">'.$current_user->data->user_nicename.'</a>',
                            'tax_site' => $tax_site_link,
                            'role' => $role
                        );
                }
            }
            echo OutputArrayToTable($items, array('Volunteer', 'Tax Site', 'Role'));
            wp_set_current_user($u->ID);
            
        } else {
            ?><p>You do not yet have any volunteers assigned to your Tax Sites.</p><?php }
        ?>

    </div>
</div>
<?php get_sidebar(); ?>
