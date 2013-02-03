<?php
// Template Name: Volunteer: Documents
// http://kuttler.eu/code/simple-upload-field-for-wordpress-pluginsthemes/


// TODO - why can only volunteers delete attachments.  Also it doesn't seem to work, and there's no confirmation message.
if ( !empty($_GET["id"]) )
	wp_delete_attachment($_GET["id"]);

?>
<div id="main">
	<div class="section-head">
		<h1><?php _e('My Documents', APP_TD); ?></h1>
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
		$headers = array('Document');	
		$postIDs = array();

		foreach ($myposts as $post) {
			setup_postdata($post);
			$items[] = array(
				'document' => "<a href='".get_permalink($post->ID)."'>".(strlen($post->post_name) ? truncate($post->post_name, 40, FALSE, TRUE) : 'File')."</a>",

				'delete' => "<a class='deletelink' href='?id=".$post->ID."'>Delete</a>",
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
				
				'delete' => "<a class='deletelink' href='?id=".$post->ID."'>Delete</a>",
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
			if (!empty($volunteer_meta[$position])) {
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
	<script>

		jQuery(document).ready(function($){
			$("a.deletelink").click(function(e) {
				e.preventDefault();
				var largeImgPath = $(this).attr("href");
				var answer = confirm("Are you sure you want to delete this document?")
				if (answer){
					window.location = largeImgPath;
				}
			});
		});

	</script>
        
	<?php get_sidebar(); ?>