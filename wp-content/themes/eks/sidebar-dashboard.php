<div id="sidebar">
	<!--<aside id="dashboard-side-nav">-->
			<?php
	global $current_user, $user_ID;
	get_currentuserinfo();
	$user_info = get_userdata($user_ID);
	//echo "<pre>";
//	var_dump($user_info);
//	echo ini_get('html_errors');
	//echo "</pre>";
	$role = $user_info->roles[0];
	$dashboard_user = $user_info;//va_get_dashboard_author();
	$is_own_dashboard = TRUE; //va_is_own_dashboard();
	
	?><h3><?php echo ucfirst($role); ?> dashboard</h3>
			<div class="user_meta">
				<p><?php $is_own_dashboard ? _e('Welcome, ', APP_TD) : ''; ?><b><?php echo $dashboard_user->display_name; ?></b></p>
				<p class="smaller"><?php _e('Member Since: ', APP_TD); ?><?php echo mysql2date(get_option('date_format'), $dashboard_user->user_registered); ?></p>
			</div>
			<ul class="links">
				<?php if ($is_own_dashboard) { ?>
					<li class="edit-profile"><?php echo html_link(appthemes_get_edit_profile_url(), __('My Info', APP_TD)); ?></li>
				<? }
	switch ($role) {
		case 'volunteer':
				?>
					<li><a href="<?php echo site_url('my-tax-sites'); ?>"><?php echo __('My Tax Sites', APP_TD); ?></a></li>
					<li><a href="<?php echo site_url('my-trainings'); ?>"><?php echo __('My Training', APP_TD); ?></a></li>
					<li><a href="<?php echo site_url('my-calendar'); ?>"><?php echo __('My Schedule', APP_TD); ?></a></li>
					<li><a href="<?php echo site_url('my-documents'); ?>"><?php echo __('Upload documents', APP_TD); ?></a></li>
					<li><a href="<?php echo site_url('my-playlist'); ?>"><?php echo __('VITA Video Playlist', APP_TD); ?></a></li>
					<li><a href="<?php echo wp_logout_url(); ?>" title="Logout">Logout</a></li>
					<?php
			break;

		case 'coordinator':?>
				<li class="view-listings"><?php
					if ($is_own_dashboard) {
						echo html_link(va_get_the_author_listings_url($dashboard_user->ID, true), __('View Tax Sites', APP_TD));
					} else {
						echo html_link(va_get_the_author_listings_url($dashboard_user->ID), __('View Tax Sites', APP_TD));
					}
					?></li>
				<?php ?>
						<li class="add-listings"><?php echo html_link(va_get_listing_create_url(), __('New Tax Site', APP_TD)); ?></li>
						<?php if (false && $dashboard_user->has_claimed) { ?>
							<li class="claimed-listings"><?php echo html_link(va_get_claimed_listings_url(), __('Claimed Listings', APP_TD)); ?></li>
					<?php } ?>
						<li><a href="<?php echo site_url('coordinator-trainings'); ?>"><?php echo __('View Trainings', APP_TD); ?></a></li>
						<li><span>My Volunteers</span><ul>
							<li><a href="<?php echo site_url('email-all'); ?>"><?php echo __('Email All', APP_TD); ?></a></li>
							<li><a href="<?php echo site_url('coordinator-calendar'); ?>"><?php echo __('Schedule', APP_TD); ?></a></li>
							<li><a href="<?php echo site_url('coordinator-documents'); ?>"><?php echo __('View Documents', APP_TD); ?></a></li>
						</ul></li>
						<li><a href="<?php echo wp_logout_url(); ?>" title="Logout">Logout</a></li>
					<?php 
			break;
		default:
			break;
	}
	?></ul>

	<!--</aside>-->
</div>