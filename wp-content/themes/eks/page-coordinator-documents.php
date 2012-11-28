<?php
// Template Name: Coordinator: Documents
// http://kuttler.eu/code/simple-upload-field-for-wordpress-pluginsthemes/
?>

<?php
/**
 * Form builder helper
 *
 * @param string $label Field label
 * @return none
 */
function fileupload( $label, $myposts = array() ) { ?>
      <form name="uploadfile" id="uploadfile_form" method="POST" enctype="multipart/form-data" action="<?php //echo $this->filepath.'#uploadfile'; ?>" accept-charset="utf-8" >
		  <label>Tax Site: <select name="tax_site">
			  <?php foreach ($myposts as $post) {
				  ?><option value="<?= $post->ID ?>"><?= $post->post_title ?></option>
			   <?php } ?>
			  </select> <span></span></label><span>Uploaded documents will be displayed at volunteers attached to selected Tax Site</span><br/>
		  <label><?php echo $label; ?><input type="file" name="uploadfiles[]" id="uploadfiles" size="35" class="uploadfiles" /></label>
        <input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="Upload"  />
      </form>
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
		  'post_parent' => $_REQUEST['tax_site'],
        );

        $attach_id = wp_insert_attachment( $attachment, $filedest );
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
		$args = array('numberposts' => -1,  'post_type' => 'attachment', 'author' => $user_ID, 'post_status' => 'inherit');
		$myposts = get_posts($args);
		$items = array();
		$headers = array('Tax site', 'File');	
		foreach ($myposts as $post) {
			setup_postdata($post);
//				var_dump($post);
			$items[] = array(
				'tax_site' => $post->post_parent ? "<a href='".get_permalink($post->post_parent)."'>".get_the_title($post->post_parent)."</a>" : 'None',
				'document' => "<a href='".get_permalink($post->ID)."'>".(strlen($post->post_name) ? truncate(rawurldecode($post->post_name), 40, FALSE, TRUE) : 'File')."</a>",
				);
		}
		echo OutputArrayToTable($items, $headers);
		?><br/><h2><?php _e('Upload New Document', APP_TD); ?></h2><?php
		
		
		wp_reset_query();
		$args = array('numberposts' => -1, 'post_type' => 'listing', 'author' => $user_ID, 'post_status' => array('publish', 'pending', 'draft', 'trash'));
		$myposts = get_posts($args);
		 
		if (count($myposts)) {
			fileupload('File:', $myposts);
		} else { 
			echo "<p>Please create at least one Tax Site</p>";
		}
?>


	</div>
	<script>

//		jQuery(document).ready(function($){
////			$('.trickbox').tricbox();
//			$('.trickbox').click(function(){
//				tb_show('', '<?php echo site_url(); ?>/wp-admin/media-upload.php?type=image&amp;TB_iframe=true');
//				return false;
//			});
//		});
	</script>

</div>

	<?php get_sidebar(); ?>


