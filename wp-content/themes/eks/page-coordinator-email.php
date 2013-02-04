<?php
// Template Name: Coordinator: Email all
?>
<?php 
// embed the javascript file that makes the AJAX request
wp_enqueue_script('volunteer-registration', '/wp-content/plugins/volunteer/js/registration.js', array('jquery','jquery-ui-dialog'));

/* form proccessing */
wp_enqueue_script('json-form', '/wp-content/plugins/volunteer/js/jquery.form.js', array('jquery'));

// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
// http://site.local/wp-admin/admin-ajax.php?action=myajax-submit&postID=1
wp_localize_script('volunteer-registration', 'Coordinator', array('ajaxurl' => admin_url('admin-ajax.php'), 'action' => 'email-all',));// 'success_url' => site_url('thank-you')
?>
<div id="main">
	<div class="section-head">
		<h1><?php _e('Email All Volunteers', APP_TD); ?></h1>
	</div>
    		
	<div class="categories-list">
        <?php
        $volunteers = get_volunteers();
        if (count($volunteers) > 0) { ?>
		<div id="result"></div>
		
		<form method="post" action="<?php echo admin_url('admin-ajax.php'); ?>" class="email-form" id="email-form">
			<div class="info">Volunteers</div>
			<div class="action">
				<input id="select_all" type="button" value="Select All" />
			</div>
                <?php
                
		$u = wp_get_current_user();
		foreach ($volunteers as $post) {
			if ($post->post_author) {
				set_current_user($post->post_author);
	//			setup_postdata(get_userdata($post->post_author));
				get_template_part('content-volunteer');
			}
		}
		set_current_user($u->ID);
                
		?>
			<div class="clearfix"></div>
			<label>Subject:
				<input type="text" id="subject" name="subject"/>
			</label>
			<label>Message:
				<textarea id="message" name="message">Hello !user
</textarea>
				<span class="description">Replacement: !user will be replaced with real user name.</span>
			</label>
			<input id="email_all" type="submit" value="email" class="clearfix"/>
		</form>
                <?php } else { ?><p>You do not yet have any volunteers assigned to your Tax Sites.</p><?php } ?>
	</div>
	<script>
		jQuery(document).ready(function($){
			$('#select_all').toggle(function(){
				$('input:checkbox').attr('checked', true);
				return false;
			},function(){
				$('input:checkbox').attr('checked', false);
				return false;
			});
		});
		jQuery(document).ready(function($){
			$('#email-form').ajaxForm({
				data: Coordinator,
				dataType: 'json',
				success : function(response, statusText, xhr, $form) {
//					alert(response.errors);
					if (response.errors.length) {
						$('#result')
							.removeClass().empty()
							.html('<span>' + response.errors + '</span>')
							.addClass('notice error');
					} else {
						$('#result')
							.removeClass().empty()
							.html('<span>Messages are sent successfully</span>')
							.addClass('notice success');
					}
				}
			});
		});
	</script>
        
</div>
<?php get_sidebar(); ?>
