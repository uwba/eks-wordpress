<?php
// Template Name: My Trainings
?>
<div id="main">
    <div class="section-head">
        <h1><?php _e('My Training', APP_TD); ?></h1>
    </div>

    <div class="categories-list">

        <?php
        $volunteer = get_volunteer();

        if ($volunteer) {
            // Find their position
            $meta = get_post_meta($volunteer->ID);
            $is_preparer = in_array('preparer', array_keys($meta));

            if ($is_preparer) {

                $trainings = get_post_meta($volunteer->ID, 'training');
                $training_found = count($trainings) > 0;

                if ($training_found) {
                    $args = array('numberposts' => -1, 'post_type' => 'training', 'post__in' => $trainings);
                    $myposts = get_posts($args);
                    foreach ($myposts as $post) {
                        setup_postdata($post);
                        // the_post();
                        // var_dump($post);
                        ?><article id="post-<?php the_ID(); ?>" class="post-<?php the_ID(); ?> listing training type-training status-publish hentry"><?php
                            get_template_part('content-training');
                            ?></article><?php
                    }
                    ?>
                    <p>If you would like to change your tax site or training assignment, <a href="/volunteer-registration/#complete">click here</a>.</p>


                    <?php
                } else { // no training found
                    ?><p>Oops, you haven’t signed up for tax preparer training!
                    </p>
                    <p>
                        To sign up for training you will be asked to go back a few steps to choose your volunteer position and tax site again. You will then be prompted to select and sign up for a training.</p>
                    <p>
                        Sorry for the inconvenience!
                    </p>

                    <p><a href="/volunteer-registration/#complete">Click here</a> to sign up for training now.</p><?php
                }
            } else { // Not a preparer 
                ?>
                <p>Your Site Coordinator will be in contact with you regarding your training.</p>
                
                <p>If you would like to change your tax site, <a href="/volunteer-registration/#complete">click here</a>.</p>
            <?php
            }
        }
        ?>
    </div>

</div>

<?php get_sidebar(); ?>
