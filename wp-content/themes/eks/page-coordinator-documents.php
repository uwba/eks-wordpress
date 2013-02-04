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

<?php
echo fileupload_process();

global $user_ID;
get_currentuserinfo();

// This appears to also show attachments on any Tax Sites.  Not sure if this is desired or not.
$args = array('numberposts' => -1, 'post_type' => 'attachment', 'author' => $user_ID, 'post_status' => 'inherit');
$myposts = get_posts($args);

$items = array();
$headers = array('Tax Site', 'File', 'Date Modified');
foreach ($myposts as $post) {
    setup_postdata($post);
    // As per http://wordpress.stackexchange.com/questions/20081/how-to-get-attachment-file-name-not-attachment-url
    $filename = basename ( get_attached_file( $post->ID ) );
    $items[] = array(
        'tax_site' => $post->post_parent ? "<a href='" . get_permalink($post->post_parent) . "'>" . get_the_title($post->post_parent) . "</a>" : 'None',
        'document' => "<a href='" . get_permalink($post->ID) . "'>" . truncate(rawurldecode($filename), 40, FALSE, TRUE) . "</a>",
        'date_modified' => strftime('%c', strtotime($post->post_modified))
    );
}
echo OutputArrayToTable($items, $headers);
?><br/><h2><?php _e('Upload New Document', APP_TD); ?></h2><?php
        wp_reset_query();
        $args = array('numberposts' => -1, 'post_type' => 'listing', 'author' => $user_ID, 'post_status' => array('publish', 'pending'));
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