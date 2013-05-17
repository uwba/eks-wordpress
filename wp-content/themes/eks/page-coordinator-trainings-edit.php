<?php
// Template Name: Coordinator: Trainings Edit or Add

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(site_url('edit')));
} else {
    global $current_user, $user_ID;
    get_currentuserinfo();

    $update = isset($_REQUEST['postid']) && !empty($_REQUEST['postid']);

// END THE IF STATEMENT THAT STARTED THE WHOLE FORM

    $messages = array();
    if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action']) && $_POST['action'] == "edit_post") {

        if ($update) {
            $post_to_edit = get_post($_REQUEST['postid']);
        } else {
            $post_to_edit = array();
        }
        /* these are the fields that we are editing in the form below. you have to change them to your fields and you can add as many as you need. */

        $title = $_POST['title'];
        $description = $_POST['description'];
        $type = $_POST['type'];
        $cat = empty($_POST['cat']) ? null : $_POST['cat'];
        $address = $_POST['address'];
        $date = $_POST['date'];
        $times = $_POST['times'];
        $special_instructions = $_POST['special_instructions'];

        /* this code will save the title and description into the post_to_edit array */

        $messages['error'] = 'The training could not be saved.';

        if (!empty($title)) {
            if ($update) {
                $post_to_edit->post_title = $title;
                $post_to_edit->post_content = $description;
                $pid = wp_update_post($post_to_edit);
            } else {
                $post_to_edit['post_title'] = $title;
                $post_to_edit['post_content'] = $description;
                $post_to_edit['post_status'] = 'publish';
                $post_to_edit['post_type'] = 'training';
                $post_to_edit['post_author'] = $user_ID;
                $post_to_edit['comment_status'] = 'closed';
                $pid = wp_insert_post($post_to_edit);
            }
        } else {
            $messages['error'] .= '  Please enter a title.';
        }

        if ($pid) {           
            update_post_meta($pid, 'type', $type);
            if (!empty($cat))
                update_post_meta($pid, 'cat', $cat);
            update_post_meta($pid, 'address', $address);
            update_post_meta($pid, 'date', $date);
            update_post_meta($pid, 'times', $times);
            update_post_meta($pid, 'special_instructions', $special_instructions);

            //REDIRECT USER WHERE EVER YOU WANT AFTER DONE EDITING
            $messages['success'] = 'The training was saved. <a href="' . get_permalink($pid) . '">View Training</a>';
            $messages['error'] = null;

            $post_to_edit = get_post($pid);
        }
    } // end check for errors
    ?>


    <?php if ($update) $post_to_edit = get_post($_REQUEST['postid']); ?>

    <div id="main">
        <div class="section-head">
            <h1><?php echo $update ? 'Edit Training' : 'Add Training' ?></h1>
        </div>
        <?php if (!empty($messages['success'])): ?>
            <div class="notice success"><span><?php echo $messages['success'] ?></span></div>
        <?php endif; ?>
        <?php if (!empty($messages['error'])): ?>
            <div class="notice error"><span><?php echo $messages['error'] ?></span></div>
        <?php endif; ?>

        <div class="new-coupon-form">

            <!-- EDIT TRAINING FORM -->
            <article class="training">
                <form id="create-listing" name="edit_training" method="POST" action="" accept-charset="utf-8"
                      enctype="multipart/form-data">

                    <!-- post name -->
                    <fieldset name="name">
                        <label for="title">Training Title:</label>
                        <input type="text" id="title" value="<?php echo empty($post_to_edit->post_title) ? '' : $post_to_edit->post_title; ?>" tabindex="5"
                               name="title"/> <!-- TITLE FIELD. NOTHING TO CHANGE -->
                    </fieldset>

                    <!-- post Content -->
                    <fieldset class="content">
                        <label for="description">Description:</label>
                        <textarea id="description" tabindex="15"
                                  name="description"><?php echo empty($post_to_edit->post_content) ? '' : $post_to_edit->post_content; ?></textarea>
                        <!-- TEXT AREA OF CONTENT. NOTHING TO CHANGE -->
                    </fieldset>

                    <fieldset class="type">
                        <label for="type">Type:</label>
                        <?php
                        $options = array(
                            'Onsite Training' => 'Onsite Training'
                        );
                        if (eks_is_admin()) {
                            $options['County Public Training'] = 'County Public Training';
                            $options['Link and Learn'] = 'Link and Learn';
                        }
                        $value = $update ? get_post_meta($post_to_edit->ID, 'type', true) : null;
                        echo html_options($options, $value, array('name' => 'type'));
                        ?>
                    </fieldset>

                    <?php 
                    // Cat (county) is only needed for admins to set up County Public Trainings 
                    if (eks_is_admin()) { ?>
                        <fieldset id="category">
                            <label for="cat">County:</label>
                            <?php
                            $selected_cat = '';
                            if (!empty($post_to_edit->ID)) 
                                $selected_cat = get_post_meta($post_to_edit->ID, 'cat', true);
                            wp_dropdown_categories(array(
                                'selected' => $selected_cat,
                                'name' => 'cat',
                                'class' => 'postform',
                                'taxonomy' => 'listing_category',
                                'hide_empty' => false));
                            ?>
                        </fieldset>
                    <script type="text/javascript">
                         jQuery(document).ready(function($) {            
                            $('#type').change(function() {
                                if ($('#type option:selected').text() == 'County Public Training')
                                    $('#category').show();
                                else
                                    $('#category').hide();
                            }).change();
                        });
                    </script>
                    <?php } ?>

                    <fieldset class="contact">
                        <label for="address">Address:</label>
    <?php //$value = $update ? get_post_meta($post_to_edit->ID, 'contact', true) : null;   ?>
                        <input type="text" value="<?php if (!empty($post_to_edit->ID)) echo get_post_meta($post_to_edit->ID, 'address', true); ?>"
                               id="address" name="address"/>
                    </fieldset>
                    <fieldset class="date">
                        <label for="date">Date(s):</label>
                        <input type="text" value="<?php if (!empty($post_to_edit->ID)) echo get_post_meta($post_to_edit->ID, 'date', true); ?>"
                               id="date" name="date"/>
                    </fieldset>
                    <fieldset class="times">
                        <label for="times">Time(s):</label>
                        <input type="text" value="<?php if (!empty($post_to_edit->ID)) echo get_post_meta($post_to_edit->ID, 'times', true); ?>"
                               id="times" name="times"/>
                    </fieldset>

                    <fieldset class="special_instructions">
                        <label for="special_instructions">Special Instructions:</label>
                        <textarea id="special_instructions" tabindex="20" name="special_instructions"><?php if (!empty($post_to_edit->ID)) echo get_post_meta($post_to_edit->ID, 'special_instructions', true); ?></textarea>
                    </fieldset>

                    <fieldset class="submit">
                        <input type="submit" value="Submit" tabindex="40" id="edit_post_submit_btn" name="edit_post_submit"/>
                    </fieldset>
                    <input type="hidden" name="postid" value="<?php if (!empty($post_to_edit->ID)) echo $post_to_edit->ID; ?>"/>
                    <!-- DONT REMOVE OR CHANGE -->
                    <input type="hidden" name="action" value="edit_post"/> <!-- DONT REMOVE OR CHANGE -->
                    <!--<input type="hidden" name="change_cat" value="" />								 DONT REMOVE OR CHANGE -->
                    <!--<input type="hidden" name="change_image" value="" />							 DONT REMOVE OR CHANGE -->
    <?php // wp_nonce_field( 'new-post' );   ?>
                </form>
            </article>
            <!-- END OF FORM -->

        </div>
        <!-- .entry-content -->
    </div><!-- #post-## -->

    </div>

    <?php
    get_sidebar();
} // User is logged in 
?>