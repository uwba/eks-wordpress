<?php
// Template Name: Volunteer: Documents
// http://kuttler.eu/code/simple-upload-field-for-wordpress-pluginsthemes/
?>


<?php

if ( $_GET["id"] )
	wp_delete_attachment($_GET["id"]);
/**
 * Form builder helper
 *
 * @param string $label Field label
 * @return none
 */
function fileupload( $label) { ?>
      <form name="uploadfile" id="uploadfile_form" method="POST" enctype="multipart/form-data" action="<?php //echo $this->filepath.'#uploadfile'; ?>" accept-charset="utf-8" >
		  
		<label><?php echo $label; ?><input type="file" name="uploadfiles[]" id="uploadfiles" size="35" class="uploadfiles" /></label>
		<label><input class="check_public" type="checkbox" name="check_public" id="check_public" value="Public"  />Public</label>
        <input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="Upload"  />
        
      </form>
	
	<script>

		jQuery(document).ready(function($){
			$(".table a").click(function(e) {
				e.preventDefault();
				var largeImgPath = $(this).attr("href");
				var answer = confirm("Are you sure you want to delete this document?")
				if (answer){
					window.location = largeImgPath;
				}
			});
		});

	</script>

 <?php
}

/**
 * Handle file uploads
 *
 * @todo check nonces
 * @todo check file size
 *
 * @return none
 */
function fileupload_process() { 
  $uploadfiles = $_FILES['uploadfiles'];

  if (is_array($uploadfiles)) {

    foreach ($uploadfiles['name'] as $key => $value) {

      // look only for uploded files
      if ($uploadfiles['error'][$key] == 0) {

        $filetmp = $uploadfiles['tmp_name'][$key];

        //clean filename and extract extension
        $filename = $uploadfiles['name'][$key];

        // get file info
        // @fixme: wp checks the file extension....
        $filetype = wp_check_filetype( basename( $filename ), null );
        $filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
        $filename = $filetitle . '.' . $filetype['ext'];
        $upload_dir = wp_upload_dir();

        /**
         * Check if the filename already exist in the directory and rename the
         * file if necessary
         */
        $i = 0;
        while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
          $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
          $i++;
        }
        $filedest = $upload_dir['path'] . '/' . $filename;

        /**
         * Check write permissions
         */
        if ( !is_writeable( $upload_dir['path'] ) ) {
          //$this->msg_e('Unable to write to directory %s. Is this directory writable by the server?');
          return '<div class="notice error"><span>Cant upload file</span></div>';
        }

        /**
         * Save temporary file to uploads dir
         */
        if ( !@move_uploaded_file($filetmp, $filedest) ){
//          $this->msg_e("Error, the file $filetmp could not moved to : $filedest ");
		  return '<div class="notice error"><span>Cant move file</span></div>';
//          continue;
        }

        $attachment = array(
          'post_mime_type' => $filetype['type'],
          'post_title' => $filetitle,
          'post_content' => '',
          'post_status' => 'inherit',
        );

		if ( isset($_POST['check_public']))
		{
	        $attach_id = wp_insert_attachment( $attachment, $filedest, 0, true );
		}
		else
		{
		    $attach_id = wp_insert_attachment( $attachment, $filedest );
		}

        require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filedest );
        wp_update_attachment_metadata( $attach_id,  $attach_data );
		return '<div class="notice success"><span>File is uploaded successfully</span></div>';
      }
    }
  }
  return '';
}

?>
<div id="main">
	<div class="section-head">
		<h1><?php _e('Documents', APP_TD); ?></h1>
	</div>
	<div class="categories-list">
		<div id="result"></div>

		<?php 
		echo fileupload_process(); 
		
		global $user_ID;
		get_currentuserinfo();
		$args = array('numberposts' => -1, 'post_type' => 'attachment', 'author' => $user_ID, 'post_status' => 'inherit');
		$myposts = get_posts($args);
		$items = array();
		$headers = array('File');	
		$postIDs = array();

		foreach ($myposts as $post) {
			setup_postdata($post);
			$items[] = array(
				'document' => "<a href='".get_permalink($post->ID)."'>".(strlen($post->post_name) ? truncate($post->post_name, 40, FALSE, TRUE) : 'File')."</a>",

				'delete' => "<a href='?id=".$post->ID."'>delete</a>",
				);

			$postIDs[] = array( 'postID' => $post->ID );
		}

		$args = array('numberposts' => -1, 'post_type' => 'public', 'post_status' => 'public');
		$myposts = get_posts($args);

		foreach ($myposts as $post) {
			setup_postdata($post);

			foreach ( $postIDs as $postID)
			{
				if ($postID['postID'] == $post->ID)
				{
					continue;
				}
			}

			$items[] = array(
				'document' => "<a href='".get_permalink($post->ID)."'>".(strlen($post->post_name) ? truncate(rawurldecode($post->post_name), 40, FALSE, TRUE) : 'File')."</a>",
				
				'delete' => "<a href='?id=".$post->ID."'>delete</a>",
				);

			$postIDs[] = $post->ID;
		}

		echo OutputArrayToTable($items, $headers);

		?><br/>
		
		<h2><?php _e('Coordinator Documents', APP_TD); ?></h2><?php
		wp_reset_query();
		$volunteer = get_volunteer();
		
		$volunteer_meta = get_post_meta($volunteer->ID);

		$tax_sites = array();
		foreach (array('preparer', 'interpreter', 'screener', 'greeter') as $position) {
			if (is_array($volunteer_meta[$position])) {
				foreach ($volunteer_meta[$position] as $tax_site) {
					$tax_sites[$tax_site][] = $position;
				}
			}
		}
		

		$mydocs = array();
		foreach (array_keys($tax_sites) as $tax_site) {
			$args = array('numberposts' => -1, 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_parent' => $tax_site);
			$mydocs += get_posts($args);
		}
		$items = array();
		foreach ($mydocs as $post) {
			$items[] = array(
				'document' => "<a href='".get_permalink($post->ID)."'>".(strlen($post->post_name) ? truncate(rawurldecode($post->post_name), 40, FALSE, TRUE) : 'File')."</a>",
			);
		}
		echo OutputArrayToTable($items);
		?><br/>
		<h2><?php _e('Upload New Document', APP_TD); ?></h2><?php
		 
		fileupload('File:');
?>
	</div>


</div>

	<?php get_sidebar(); ?>


