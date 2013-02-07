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
                    <?
                    $files = get_posts(array('post_type' => 'attachment', 'author' => $post->post_author));
                    $items = array();
                    foreach ($files as $post) {
                        setup_postdata($post);
                        // As per http://wordpress.stackexchange.com/questions/20081/how-to-get-attachment-file-name-not-attachment-url
                        $filename = basename(get_attached_file($post->ID));
                        $items[] = array(
                            'file' => "<a href='" . get_permalink($post->ID) . "'>" . $filename . "</a>",
                            'date_modified' => strftime('%c', strtotime($post->post_modified))
                        );
                    }
                    echo OutputArrayToTable($items, array('File', 'Date Modified'));
                    ?>
                </p>

                <?php
                if ('POST' == $_SERVER['REQUEST_METHOD']) {
                    global $wpdb;
//	 foreach(array() as $)
                    if (isset($_POST['contacted'])) {
                        update_post_meta($post->ID, 'contacted', $wpdb->escape($_POST['contacted']));
                    }
                }
                ?>

                <h2>Notes</h2>
                <form method="POST">
                    <fieldset class="contacted">
                        <label for="contacted">Contacted - enter date:</label><br/>
                        <input type="text" value="<?php echo get_post_meta($post->ID, 'contacted', true); ?>"
                               name="contacted"/>
                    </fieldset>

                    <fieldset class="signed_up_for_appropriate_training">
                        <label for="signed_up_for_appropriate_training">Signed up for appropriate training:</label><br/>
                        <input type="text" value="<?php echo get_post_meta($post->ID, 'signed_up_for_appropriate_training', true); ?>"
                               name="signed_up_for_appropriate_training"/>
                    </fieldset>

                    <fieldset class="confirmed_as_my_volunteer">
                        <label for="confirmed_as_my_volunteer">Confirmed as my volunteer:</label><br/>
                        <input type="text" value="<?php echo get_post_meta($post->ID, 'confirmed_as_my_volunteer', true); ?>"
                               name="confirmed_as_my_volunteer"/>
                    </fieldset>

                    <fieldset class="certified_in_ethics">
                        <label for="certified_in_ethics">Certified in Ethics:</label><br/>
                        <input type="text" value="<?php echo get_post_meta($post->ID, 'certified_in_ethics', true); ?>"
                               id="certified_in_ethics" name="certified_in_ethics"/>
                    </fieldset>

                    <fieldset class="certified_in_basic_level">
                        <label for="certified_in_basic_level">Certified in Basic Level Tax Return Preparation:</label><br/>
                        <input type="text" value="<?php echo get_post_meta($post->ID, 'certified_in_basic_level', true); ?>"
                               id="certified_in_basic_level" name="certified_in_basic_level"/>
                    </fieldset>

                    <fieldset class="certified_in_intermediate_level">
                        <label for="certified_in_intermediate_level">Certified in Intermediate Level Tax Return Preparation:</label><br/>
                        <input type="text" value="<?php echo get_post_meta($post->ID, 'certified_in_intermediate_level', true); ?>"
                               id="certified_in_intermediate_level" name="certified_in_intermediate_level"/>
                    </fieldset>

                    <fieldset class="certified_specialized">
                        <label for="certified_specialized">Certified Specialized Tax Return Preparation:</label><br/>
                        <input type="text" value="<?php echo get_post_meta($post->ID, 'certified_specialized', true); ?>"
                               id="certified_specialized" name="certified_specialized"/>
                    </fieldset>



                    <fieldset class="volunteered_at_my_site">
                        <label for="volunteered_at_my_site">Volunteered at my site:</label><br/>
                        <input type="text" value="<?php echo get_post_meta($post->ID, 'volunteered_at_my_site', true); ?>"
                               id="volunteered_at_my_site" name="volunteered_at_my_site"/>
                    </fieldset>

                    <fieldset class="also_volunteers_at_another_vita_site">
                        <label for="also_volunteers_at_another_vita_site">Also volunteers at another VITA Site:</label><br/>
                        <input type="text" value="<?php echo get_post_meta($post->ID, 'also_volunteers_at_another_vita_site', true); ?>"
                               id="also_volunteers_at_another_vita_site" name="also_volunteers_at_another_vita_site"/>
                    </fieldset>


                    <fieldset class="submit">
                        <input type="submit" value="Update" tabindex="40" />
                    </fieldset>
                </form>

                <?php appthemes_after_blog_post_content(); ?>
            </section>

        <!--<small>Created at <?php // va_the_post_byline();   ?></small>-->
            <?php //edit_post_link( __( 'Edit', APP_TD ), '<span class="edit-link">', '</span>' );  ?>	

            <?php //comments_template();   ?>

        </article>

        <?php appthemes_after_blog_post(); ?>

    <?php endwhile; ?>

    <?php appthemes_after_blog_loop(); ?>

</div><!-- /#main -->


<?php get_sidebar(app_template_base()); ?>
