jQuery(document).ready(function() {
	jQuery('#pluginchief-plugin-admin-connect-learn-more').click(function() {
		jQuery('#pluginchief-hidden-form').toggle('slow', function() {});
	});
	jQuery('#pluginchief-post-subscribe').click(function() {
		jQuery.ajax({
			type: 'POST',
			url: 'http://pluginchief.com/pluginchief-postserver.php',
			data: {
				'useremail': jsFileVariables.useremail,
			},
			success: function(msg) {
				alert('wow' + msg);
			}
		});
	});

	jQuery('#pluginchief-post-subscribe').click(function() {

	});
}); //End jQuery