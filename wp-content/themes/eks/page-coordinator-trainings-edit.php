<?php
// Template Name: Coordinator: Trainings Edit

if (!is_user_logged_in()) {
//	echo 3333; exit;
    wp_redirect(wp_login_url(site_url('edit')));
} else {
    global $current_user, $user_ID;
    get_currentuserinfo();
    $update = isset($_REQUEST['postid']) && !empty($_REQUEST['postid']);

    function training_process()
    {


    } // END THE IF STATEMENT THAT STARTED THE WHOLE FORM

//function mywp_upload($key, $value, $post_parent) {
//	     // look only for uploded files
//      if ($_FILES['training_files']['error'][$key] == 0) {
//
//
//
//
//        $filetmp = $_FILES['training_files']['tmp_name'][$key];
//
//        //clean filename and extract extension
//        $filename = $_FILES['training_files']['name'][$key];
//
//        // get file info
//        // @fixme: wp checks the file extension....
//        $filetype = wp_check_filetype( basename( $filename ), null );
//        $filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
//        $filename = $filetitle . '.' . $filetype['ext'];
//        $upload_dir = wp_upload_dir();
//
//        /**
//         * Check if the filename already exist in the directory and rename the
//         * file if necessary
//         */
//        $i = 0;
//        while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
//          $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
//          $i++;
//        }
//        $filedest = $upload_dir['path'] . '/' . $filename;
//
//        /**
//         * Check write permissions
//         */
//        if ( !is_writeable( $upload_dir['path'] ) ) {
//          //$this->msg_e('Unable to write to directory %s. Is this directory writable by the server?');
//          return '<div class="notice error"><span>Cant upload file</span></div>';
//        }
//
//        /**
//         * Save temporary file to uploads dir
//         */
//        if ( !@move_uploaded_file($filetmp, $filedest) ){
////          $this->msg_e("Error, the file $filetmp could not moved to : $filedest ");
//		  return '<div class="notice error"><span>Cant move file</span></div>';
////          continue;
//        }
//
//        $attachment = array(
//          'post_mime_type' => $filetype['type'],
//          'post_title' => $filetitle,
//          'post_content' => '',
//          'post_status' => 'inherit',
//		  'post_parent' => $post_parent,
//        );
//
//
//		$attach_id = wp_insert_attachment( $attachment, $filedest );
//
//        require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
//        $attach_data = wp_generate_attachment_metadata( $attach_id, $filedest );
//        wp_update_attachment_metadata( $attach_id,  $attach_data );
//		return '<div class="notice success"><span>File is uploaded successfully</span></div>';
//      }
//}


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
        $contact = $_POST['contact'];
        $locations = $_POST['locations'];
        $directions = $_POST['directions'];
        $bring = $_POST['bring'];
        $tax_site = $_POST['tax_site'];
		$max_people = (int)$_POST['max_people'];
		$additional_instructions = trim($_POST['additional_instructions']);

        /* this code will save the title and description into the post_to_edit array */


        /* honestly i can't really remember why i added this code but it is a must */

        if($title && $description){
            if ($update) {
    //		echo 'updating...';
                $post_to_edit->post_title = $title;
                $post_to_edit->post_content = $description;
                $pid = wp_update_post($post_to_edit);
            } else {
    //		echo 'inserting...';
                $post_to_edit['post_title'] = $title;
                $post_to_edit['post_content'] = $description;
                $post_to_edit['post_status'] = 'publish';
                $post_to_edit['post_type'] = 'training';
                $post_to_edit['post_author'] = $user_ID;
                $post_to_edit['comment_status'] = 'closed';
                $pid = wp_insert_post($post_to_edit);

            }
        }
        if(!$title) $messages['title'] = 'Title is required';
        if(!$description) $messages['description'] = 'Description is required';

        if ($pid) {
//            exit;
            /* save taxonomies: post ID, form name, taxonomy name, if it appends(true) or rewrite(false) */
            /* here you have to change the "coupon_categories" and "coupon_tags" to the name of your taxonomies */

            //	wp_set_post_terms($pid, array($_POST['cat']),'coupons_categories',false);
            //	wp_set_post_terms($pid, array($_POST['post_tags']),'coupons_tags',false);

            //UPDATE CUSTOM FIELDS WITH THE NEW INFO
            //CHANGE TO YOUR CUSTOM FIELDS AND ADD AS MANY AS YOU NEED

            update_post_meta($pid, 'type', $type);
            update_post_meta($pid, 'contact', $contact);
            update_post_meta($pid, 'locations', $locations);
            update_post_meta($pid, 'directions', $directions);
            update_post_meta($pid, 'bring', $bring);
            update_post_meta($pid, 'tax_site', $tax_site);
			update_post_meta($pid, 'max_people', $max_people);
			update_post_meta($pid, 'additional_instructions', $additional_instructions);
			


            //INSERT OUR MEDIA ATTACHMENTS
            // THE FIRST LINE OF THE CODE AS TO DO WITH A LITTLE JAVASCRIPT THAT I WILL EXPLAIN LATER. IT CHECKS IF WE NEED TO CHANGE THE IMAGE or not
//		if (is_array($_FILES['training_files'])) {
//				foreach ($_FILES['training_files']['name'] as $key => $value) {
//					//mywp_upload($key, $value, $pid, 'training_files') ;
//					insert_attachment('training_files',$pid,false) ;
//				}
//		}

            foreach ($_FILES as $file => $array) {
                $newupload = insert_attachment($file, $pid);
                // $newupload returns the attachment id of the file that
                // was just uploaded. Do whatever you want with that now.
            }

            foreach ($_POST['attachment_delete'] as $attachment_delete) {
                // TODO: check author
                wp_delete_attachment($attachment_delete);
                delete_post_meta($pid, 'training_files', $attachment_delete);
            }

            //REDIRECT USER WHERE EVER YOU WANT AFTER DONE EDITING
//            wp_redirect(site_url('coordinator-trainings'));
            $messages['success'] = 'Saved Successfully';
             wp_redirect(site_url(get_permalink($pid)));

        } else {
            $messages['error'] = 'Please fill all fields!';
        }

    } // end check for errors


    ?>


<?php if ($update) $post_to_edit = get_post($_REQUEST['postid']); ?>
<?php $terms = get_the_terms($post_to_edit->ID, 'listing_categories'); ?>
<?php //$coupons_tags = strip_tags( get_the_term_list( $post_to_edit->ID, 'coupons_tags', '', ', ', '' ) ); ?>

<?php //$term_name = strip_tags( get_the_term_list( $post_to_edit->ID, 'coupons_categories', '', ', ', '' ) ); ?> <!-- get the category name of this post -->
<?php //$term_obj = get_term_by('name', $term_name, 'coupons_categories'); ?> <!-- get the current term object -->
<?php //$term_id = $term_obj->term_id ;?> <!-- get this post's term id -->
<?php $args = array(
        'selected' => $term_id,
        'name' => 'cat',
        'class' => 'postform',
//    'tab_index'          => 10,
//    'depth'				 => 2,
//	'hierarchical'		 => 1,
        'taxonomy' => 'listing_category',
        'hide_empty' => false); ?> <?php /* array for wp_dropdown_category to display with the current post category selected by default */ ?>

<div id="main">
    <div class="section-head">
        <h1><?php _e('My Trainings', APP_TD); ?></h1>
    </div>
    <? if($messages['success']): ?>
    <div class="notice success"><span><?= $messages['success'] ?></span></div>
    <? endif; ?>
    <? if($messages['error']): ?>
    <div class="notice error"><span><?= $messages['error'] ?></span></div>
    <? endif; ?>

    <div class="new-coupon-form">

        <!-- EDIT COUPON FORM -->
        <article class="training">
            <form id="edit_training_form" name="edit_training" method="POST" action="" accept-charset="utf-8"
                  enctype="multipart/form-data">

                <!-- post name -->
                <fieldset name="name">
                    <label for="title">Training Title:</label><br/>
                    <input type="text" id="title" value="<?php echo $post_to_edit->post_title; ?>" tabindex="5"
                           name="title"/> <!-- TITLE FIELD. NOTHING TO CHANGE -->
                    <? if($messages['title']): ?>
                         <div class="error"><?= $messages['title'] ?></div>
                    <? endif; ?>
                </fieldset>

                <!-- post Category -->
                <fieldset id="category">
                    <label for="cat">County </label><?php wp_dropdown_categories($args); ?>
                    <!-- DROP DOWN WITH THE $ARG THAT WE CREATED BEFORE -->
                </fieldset>

                <fieldset class="tax_site">
                    <label for="tax_site">Tax Site:</label><br/>
                    <?php $tax_sites = get_posts(array('post_type' => 'listing', 'author' => $user_ID));
                    $options = array('' => 'None');
                    foreach ($tax_sites as $tax_site) {
                        $options[$tax_site->ID] = $tax_site->post_title;
                    }
                    $value = $update ? get_post_meta($post_to_edit->ID, 'tax_site', true) : null;
                    echo html_options($options, $value, array('name' => 'tax_site'));
                    ?>
                </fieldset>

                <!-- post Content -->
                <fieldset class="content">
                    <label for="description">Description:</label><br/>
                    <textarea id="description" tabindex="15"
                              name="description"><?php echo $post_to_edit->post_content; ?></textarea>
                    <? if($messages['description']): ?>
                    <div class="error"><?= $messages['description'] ?></div>
                    <? endif; ?>
                    <!-- TEXT AREA OF CONTENT. NOTHING TO CHANGE -->
                </fieldset>

                <!-- images -->
                <?php //echo get_the_post_thumbnail( $post_to_edit->ID, array( 200, 150 ) ); ?><br/>
                <!-- WILL DISPLAY THE POST'S THUMBNAIL. YOU CAN CHANGE THE SIZE OF IT -->
                <!--	<input type="checkbox" name="c1" onclick="showMe('image', this)" >Change Image       SCRIPT CHECK IF CHANGING IMAGE OR NOT. NOTHING TO CHANGE
                    <fieldset id="image">
                        <label for="image">Choose Image:</label>
                        <input type="file" name="image" id="image" tabindex="30" value="" />
                    </fieldset>-->

                <h3> Training details: </h3>

                <!-- BELOW ARE THE CUSTOM FIELDS. CHANGE THEM ADD OR REMOVE -->

                <fieldset class="type">
                    <label for="type">Type:</label><br/>
                    <?php
                    $options = array(
                        'Onsite tax law training' => 'Onsite tax law training',
                        'County public training' => 'County public training',
                        'Link and Learn' => 'Link and Learn',
                        'Other onsite training or orientation' => 'Other onsite training or orientation',
                        'Info Nights' => 'Info Nights',
                    );
                    $value = $update ? get_post_meta($post_to_edit->ID, 'type', true) : null;
                    echo html_options($options, $value, array('name' => 'type')); ?>
                </fieldset>
                <fieldset class="contact">
                    <label for="contact">Instructor contact info:</label><br/>
                    <?php //$value = $update ? get_post_meta($post_to_edit->ID, 'contact', true) : null; ?>
                    <input type="text" value="<?php echo get_post_meta($post_to_edit->ID, 'contact', true); ?>"
                           id="contact" tabindex="20" name="contact"/>
                </fieldset>
                <fieldset class="locations">
                    <label for="locations">Location:</label><br/>
                    <input type="text" value="<?php echo get_post_meta($post_to_edit->ID, 'locations', true); ?>"
                           id="locations" tabindex="20" name="locations"/>
                </fieldset>
                <fieldset class="directions">
                    <label for="directions">Directions:</label><br/>
                    <input type="text" value="<?php echo get_post_meta($post_to_edit->ID, 'directions', true); ?>"
                           id="directions" tabindex="20" name="directions"/>
                </fieldset>
                <fieldset class="bring">
                    <label for="bring">What to bring:</label><br/>
                    <input type="text" value="<?php echo get_post_meta($post_to_edit->ID, 'bring', true); ?>" id="bring"
                           tabindex="20" name="bring"/>
                </fieldset>
				<fieldset class="max_people">
                    <label for="max_people">Max # of people:</label><br/>
                    <input type="text" value="<?php echo get_post_meta($post_to_edit->ID, 'max_people', true); ?>" id="max_people"
                           tabindex="20" name="max_people"/>
                </fieldset>
				<fieldset class="additional_instructions">
                    <label for="additional_instructions">Additional instructions:</label><br/>
                    <textarea id="additional_instructions" tabindex="20" name="additional_instructions"><?php echo get_post_meta($post_to_edit->ID, 'additional_instructions', true); ?></textarea>
                </fieldset>
                <fieldset class="training_files">
                    <label>Files:</label><br/>
                    <?php
                    if ($update) {
                        $files = get_posts(array('post_type' => 'attachment', 'author' => $user_ID, 'post_parent' => $post_to_edit->ID));
                        $items = array();
                        foreach ($files as $file) {
                            $item = array();
                            $item['title'] = '<a href="' . get_permalink($file->ID) . '" title="' . $file->post_title . '">' . truncate($file->post_title, 100, FALSE, TRUE) . '</a>';
                            $item['delete'] = '<input type="checkbox" name="attachment_delete[]" value="' . $file->ID . '"/>';
                            $items[] = $item;
                        }
                        echo OutputArrayToTable($items, array('Title', 'Delete'));

                    }
                    ?>

                    <label for="training_files">Upload File:</label>
                    <input type="file" name="training_files" id="training_files" tabindex="30" value=""/>
                </fieldset>


                <fieldset class="submit">
                    <input type="submit" value="Post" tabindex="40" id="edit_post_submit_btn" name="edit_post_submit"/>
                </fieldset>
                <input type="hidden" name="postid" value="<?php echo $post_to_edit->ID; ?>"/>
                <!-- DONT REMOVE OR CHANGE -->
                <input type="hidden" name="action" value="edit_post"/> <!-- DONT REMOVE OR CHANGE -->
                <!--<input type="hidden" name="change_cat" value="" />								 DONT REMOVE OR CHANGE -->
                <!--<input type="hidden" name="change_image" value="" />							 DONT REMOVE OR CHANGE -->
                <?php // wp_nonce_field( 'new-post' ); ?>
            </form>
        </article>
        <!-- END OF FORM -->

    </div>
    <!-- .entry-content -->
</div><!-- #post-## -->


<?php //} ?> <!-- user is logged in -->



















</div>

<?php get_sidebar();

}?>
