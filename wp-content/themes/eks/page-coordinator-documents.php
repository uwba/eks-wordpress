<?php
// Template Name: Coordinator: Documents
// http://kuttler.eu/code/simple-upload-field-for-wordpress-pluginsthemes/
?>

<div id="main">
    <div class="section-head">
        <h1><?php _e('Documents', APP_TD); ?></h1>
    </div>
    <div class="categories-list">
        <div id="result"></div>
        <h2>My Documents</h2>
        <?php
        echo fileupload_process();

        global $user_ID;
        get_currentuserinfo();

// List attachments I have posted to any of my Tax Sites
        $args = array('numberposts' => -1, 'post_type' => 'attachment', 'author' => $user_ID, 'post_status' => 'inherit');
        $myposts = get_posts($args);

        $items = array();
        $headers = array('Tax Site', 'File', 'Date Modified');
        foreach ($myposts as $post) {
            setup_postdata($post);
            // As per http://wordpress.stackexchange.com/questions/20081/how-to-get-attachment-file-name-not-attachment-url
            $filename = basename(get_attached_file($post->ID));
            $items[] = array(
                'tax_site' => $post->post_parent ? "<a href='" . get_permalink($post->post_parent) . "'>" . get_the_title($post->post_parent) . "</a>" : 'None',
                'file' => "<a href='" . get_permalink($post->ID) . "'>" . $filename . "</a>",
                'date_modified' => strftime('%c', strtotime($post->post_modified))
            );
        }
        echo OutputArrayToTable($items, $headers);
        ?>
        <h2>Volunteer Documents</h2>
        <?php
// List attachments any of my volunteers have posted
        $volunteers = get_volunteers();

        $volunteer_user_ids = array();
        foreach ($volunteers as $el) {
            $volunteer_user_ids[] = $el->post_author;
        }

        $args = array(
            'numberposts' => -1,
            'post_type' => 'attachment',
            'author' => implode(',', $volunteer_user_ids),
            'post_status' => 'inherit'
        );

        $myposts = get_posts($args);

        $items = array();
        foreach ($myposts as $post) {
            setup_postdata($post);
            // As per http://wordpress.stackexchange.com/questions/20081/how-to-get-attachment-file-name-not-attachment-url
            $filename = basename(get_attached_file($post->ID));
            $items[] = array(
                'volunteer' => get_the_author_meta('user_nicename', $post->post_author),
                'file' => "<a href='" . get_permalink($post->ID) . "'>" . $filename . "</a>",
                'date_modified' => strftime('%c', strtotime($post->post_modified))
            );
        }
        echo OutputArrayToTable($items, array('Volunteer', 'File', 'Date Modified'));
        ?>

        <h2><?php _e('Upload New Document', APP_TD); ?></h2><?php
        wp_reset_query();
        $args = array('numberposts' => -1, 'post_type' => 'listing', 'author' => $user_ID, 'post_status' => array('publish', 'pending'));
        $myposts = get_posts($args);

        if (count($myposts)) {
            fileupload('File:', $myposts);
        } else {
            echo "<p>Please create at least one Tax Site first.</p>";
        }
        ?>
    </div>

</div>

<?php get_sidebar(); ?>