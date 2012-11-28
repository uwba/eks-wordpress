<?php
// Template Name: Volunteer: Calendar
?>
<div id="main">
	<div class="section-head">
		<h1><?php _e('My Schedule', APP_TD); ?></h1>
	</div>
	<div class="categories-list">
		<?php volunteer_calendar(); ?>
	</div>
	
	<script>
//		jQuery(document).ready(function($){
//			$('#select_all').toggle(function(){
//				$('input:checkbox').attr('checked', true);
//				return false;
//			},function(){
//				$('input:checkbox').attr('checked', false);
//				return false;
//			});
//		});

	</script>
</div>

	<?php get_sidebar(); ?>
