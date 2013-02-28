<?php
wp_enqueue_script('jquery-ui-datepicker');
?>
<div id="main">        
    <?php
    if ('POST' == $_SERVER['REQUEST_METHOD']) {
        global $wpdb;

        foreach (array_keys($_POST) as $k) {
            if ($k == 'tax_site') {
                update_post_meta($post->ID, $wpdb->escape($_POST['position']), $wpdb->escape($_POST[$k]));
            } else {
                update_post_meta($post->ID, $k, $wpdb->escape($_POST[$k]));
            }
        }
        ?>
        <div class="notice success">
            <span>The volunteer details have been updated.</span>
        </div>
    <?php
}
?>

    <?php if (!is_user_logged_in()) { ?>
        <div class="notice error">
            <span>You do not have access to this page.</span>
        </div>
<?php } else { // logged in, so display the volunteer information 
    ?>
        <?php appthemes_before_blog_loop(); ?>

        <?php
        while (have_posts()) {

            the_post();

            $volunteer = get_post(get_the_ID());
            ?>

            <?php appthemes_before_blog_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <?php appthemes_before_blog_post_title(); ?>

                <h1 class="post-heading"><a href="<?php the_permalink(); ?>" rel="bookmark"></a><span class="left-hanger"><?php the_title(); ?></span></a></h1>
                <?php // comments_popup_link( "0", "1", "%", "comment-count" );   ?>

                <?php appthemes_after_blog_post_title(); ?>


                <section class="overview">
                    <?php appthemes_before_blog_post_content(); ?>
                    <?php the_content(); ?>
                    <?php // HTML markup format copied from single-listing.php for consistency ?>
                    <ul>
                        <?php

                        /**
                         * Render a single <li> for the given metadata item.
                         * 
                         * @param string $label
                         * @param string $item_name
                         */
                        function display_volunteer_metadata_item($label, $item_name) {
                            ?>
                            <li>
                                <p class="listing-custom-field"><span class="custom-field-label"><?php echo $label ?></span><span class="custom-field-sep">: </span><span class="custom-field-value"><?php echo esc_html(get_post_meta(get_the_ID(), $item_name, true)); ?></span></p>
                            </li>
                            <?php
                        }

                        display_volunteer_metadata_item('Name', 'name');
                        display_volunteer_metadata_item('Phone', 'phone');
                        display_volunteer_metadata_item('Email', 'email');
                        display_volunteer_metadata_item('Experience', 'experience');
                        ?>
                    </ul>

                    <?php
                    $trainings = get_post_meta(get_the_ID(), 'training', false);
                    wp_reset_query();
                    $args = array('numberposts' => -1, 'post_type' => 'training', 'post__in' => $trainings);
                    $links = array();
                    foreach (get_posts($args) as $training) {
                        $links[] = '<a href="' . get_permalink($training->ID) . '">' . $training->post_title . '</a>';
                    }
                    ?>

                    <h2>Volunteer Documents</h2>
                    <?php
                    $files = get_posts(array('post_type' => 'attachment', 'author' => $post->post_author));
                    $items = array();
                    foreach ($files as $f) {
                        setup_postdata($f);
                        // As per http://wordpress.stackexchange.com/questions/20081/how-to-get-attachment-file-name-not-attachment-url
                        $filename = basename(get_attached_file($f->ID));
                        $items[] = array(
                            'file' => "<a href='" . get_permalink($f->ID) . "'>" . $filename . "</a>",
                            'date_modified' => strftime('%c', strtotime($f->post_modified))
                        );
                    }
                    echo OutputArrayToTable($items, array('File', 'Date Modified'));
                    ?>

                    <h2>Volunteer Details</h2>
                    <form method="POST">

                        <label for="tax_site">Assigned to Tax Site:</label><br/>
                        <select id="tax_site" name="tax_site">
                            <option value=""></option>
                            <?php
                            $sites = get_volunteer_tax_sites($volunteer->post_author);
                            $tax_site_ids = array_keys($sites);
                                
                            // Inspired by va_get_dashboard_listings()
                            $args = array(
                                'post_type' => VA_LISTING_PTYPE,
                                'post_status' => array('publish', 'pending', 'expired'),
                                'posts_per_page' => -1,
                                'orderby' => 'title',
                                'order' => 'ASC'
                            );
                            if (!eks_is_admin())
                                $args['author'] = $user_ID;

                            $listings = new WP_Query($args);

                            if ($listings->post_count > 0) {
                                while ($listings->have_posts()) {
                                    $listings->the_post();
                                    $tax_site_id = get_the_ID();
                                    ?>
                                    <option <?php echo $tax_site_id == $tax_site_ids[0] ? 'selected' : '' ?> value="<?php echo $tax_site_id ?>"><?php the_title(); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <?php // Add a hidden field with the position to save a lookup upon POST ?>
                        <input type="hidden" name="position" value="<?php echo $sites[$tax_site_ids[0]][0] ?>" />
                        <br/>

                        <label for="notes_contacted">Date Contacted:</label><br/>
                        <input type="text" id="contacted" value="<?php echo get_post_meta($volunteer->ID, 'notes_contacted', true); ?>" name="notes_contacted"/>
                        <br/>

                        <?php

                        function render_checkbox($volunteer_post, $name, $label) {
                            ?>
                            <div style="padding:5px 0;">                       
                                <input name="notes_<?php echo $name ?>" type="hidden" value="0"/>
                                <input name="notes_<?php echo $name ?>" type="checkbox" value="1" <?php echo get_post_meta($volunteer_post->ID, "notes_" . $name, true) == 1 ? 'checked="checked"' : ''; ?> />
                                <label for="notes_<?php echo $name ?>"><?php echo $label ?></label>
                            </div>
                            <?php
                            
                        }
                        render_checkbox($volunteer, 'signed_up_for_appropriate_training', 'Signed Up for Appropriate Training');
                        render_checkbox($volunteer, 'confirmed_as_my_volunteer', 'Confirmed as My Volunteer');
                        render_checkbox($volunteer, 'certified_in_ethics', 'Certified in Ethics');
                        render_checkbox($volunteer, 'certified_in_basic_level', 'Certified in Basic Level Tax Return Preparation');
                        render_checkbox($volunteer, 'certified_in_intermediate_level', 'Certified in Intermediate Level Tax Return Preparation');
                        render_checkbox($volunteer, 'certified_specialized', 'Certified Specialized Tax Return Preparation');
                        render_checkbox($volunteer, 'volunteered_at_my_site', 'Volunteered at My Site');
                        render_checkbox($volunteer, 'also_volunteers_at_another_vita_site', 'Also Volunteers at Another VITA Site');
                        ?>
                        <fieldset class="submit">
                            <input type="submit" value="Update" tabindex="40" />
                        </fieldset>
                    </form>

                    <?php appthemes_after_blog_post_content(); ?>
                </section>

                        <!--<small>Created at <?php // va_the_post_byline();    ?></small>-->
                <?php //edit_post_link( __( 'Edit', APP_TD ), '<span class="edit-link">', '</span>' );   ?>	

                <?php //comments_template();    ?>

            </article>

            <?php appthemes_after_blog_post(); ?>

        <?php } ?>

        <?php appthemes_after_blog_loop(); ?>

    <?php } ?>

</div><!-- /#main -->

<script>
    jQuery(document).ready(function($) {
        $("#contacted").datepicker();
    });
</script>


<?php get_sidebar(app_template_base()); ?>
