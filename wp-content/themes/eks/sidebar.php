<?php if (is_post_type_archive('listing') || is_search()) : ?>
	<div id="sidebar" class="threecol last">
		
		
		<aside class="widget">
			<h4>What to bring?</h4>
<p>When you are going to get your taxes prepared, be sure to bring with you the following items:</p>
<ul>
<li>Photo identification (for you and your spouse, if filing jointly)</li>
<li>Social Security card or ITIN for each family member</li>
<li>W-2 forms for all jobs held in 2011 and all 1099 or 1098 forms</li>
<li>Child care provider information</li>
<li>Landlord's Name, Address and Phone number for the CA renter's credit</li>
<li>A voided check for direct deposit</li>
<li>
Last year’s tax return
<br>
<br>
</li>
</ul>
		</aside>
		
	</div>
	<?php dynamic_sidebar('search-listing'); ?>

<?php else: ?>

	<?php //dynamic_sidebar( 'main' ); // Vantage - Recent Listing,  Create Listing Buttom, Recent Reviews ?>
	<?php
	if (is_user_logged_in()) {
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

		?><div id="sidebar" class="threecol last">
			<?php if (is_page_template('page-search.php')) echo recent_searches();?>
			<h3><?php echo ucfirst($role); ?> Dashboard</h3>
				<div class="user_meta">
					<p><?php $is_own_dashboard ? _e('Welcome, ', APP_TD) : ''; ?><b><?php echo $dashboard_user->display_name; ?></b></p>
					<p class="smaller"><?php _e('Member Since: ', APP_TD); ?><?php echo mysql2date(get_option('date_format'), $dashboard_user->user_registered); ?></p>
				</div>
				<ul class="links">
<?php
		switch ($role) {
			case 'volunteer':
					?>
						<li><a href="<?php echo site_url('my-tax-sites'); ?>"><?php echo __('My Tax Sites', APP_TD); ?></a></li>
						<!--<li><a href="<?php echo site_url('my-trainings'); ?>"><?php echo __('My Training', APP_TD); ?></a></li>
						<li><a href="<?php echo site_url('my-calendar'); ?>"><?php echo __('My Schedule', APP_TD); ?></a></li>-->
						<li><a href="<?php echo site_url('my-documents'); ?>"><?php echo __('Upload Documents', APP_TD); ?></a></li>
						<li><a href="<?php echo site_url('my-playlist'); ?>"><?php echo __('VITA Video Playlist', APP_TD); ?></a></li>
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
					<li class="add-listings"><?php echo html_link(va_get_listing_create_url(), __('New Tax Site', APP_TD)); ?></li>
					<?php if (false && $dashboard_user->has_claimed) { ?>
					<li class="claimed-listings"><?php echo html_link(va_get_claimed_listings_url(), __('Claimed Listings', APP_TD)); ?></li>
					<?php } ?>
					<!--<li><a href="<?php echo site_url('coordinator-trainings'); ?>"><?php echo __('View Trainings', APP_TD); ?></a></li>-->
					<li><a href="<?php echo site_url('coordinator-volunteers'); ?>"><?php echo __('My Volunteers', APP_TD); ?></a><ul>
						<li><a href="<?php echo site_url('email-all'); ?>"><?php echo __('Email Volunteers', APP_TD); ?></a></li>
						<!--<li><a href="<?php echo site_url('coordinator-calendar'); ?>"><?php echo __('Schedule', APP_TD); ?></a></li>-->
						<li><a href="<?php echo site_url('coordinator-documents'); ?>"><?php echo __('View Documents', APP_TD); ?></a></li>
					</ul></li>
					
				<?php

				break;
			default:
				break;
		}
                    if ($is_own_dashboard) { ?>
                    <li class="edit-profile"><?php echo html_link(appthemes_get_edit_profile_url(), __('My Account', APP_TD)); ?></li>
                    <?php } ?>
                 </ul></div><?php
	} elseif (is_page_template('page-my-registration.php') || strpos('-'.$_SERVER["REQUEST_URI"], 'volunteer') || is_page_template('form-registration.php') || strpos('-'.$_SERVER["REQUEST_URI"], 'coordinator')) { ?>
		<div id="sidebar" class="threecol last">
                        <h3>Login</h3>
                    	<?php if (is_page_template('page-my-registration.php') || strpos('-'.$_SERVER["REQUEST_URI"], 'volunteer')) { ?>
                            <p>Volunteers, if you already have an account, please login below to review or make changes.</p>

                            <?php if (strstr($_SERVER["REQUEST_URI"], "volunteer-registration") == null) { ?>
                            <p>Otherwise, <a href="/volunteer-registration/">create an account now</a>.</p>

                            <?php } ?>
                            <p>Tax Site Coordinators, <a href="/coordinators">click here</a>.</p>
                        <?php } ?>
                                
                        <?php if (is_page_template('form-registration.php') || strpos('-'.$_SERVER["REQUEST_URI"], 'coordinator')) { ?>
                            <p>Tax Site Coordinators, if you already have tax site or sites, please login below to review or make changes.</p>

                            <?php if (strstr($_SERVER["REQUEST_URI"], "register") == null) { ?>
                            <p>Otherwise, <a href="/register/?role=coordinator">create an account now</a>.</p>
                            <?php } ?>

                            <p>Volunteers, <a href="/volunteer-registration">click here</a>.</p>
                        <?php } ?>
		
			<?php do_action( 'appthemes_notices' ); 
			

// set a redirect for after logging in
$redirect = site_url('dashboard');
if ( isset( $_REQUEST['redirect_to'] ) ) {
	$redirect = $_REQUEST['redirect_to'];
}		
			?>
	
			<form action="<?php echo APP_Login::get_url(); ?>" method="post" class="login-form" id="login-form">
				<fieldset>
					<div class="form-field">
						<label>
							<?php _e('Username:', APP_TD ); ?>
							<input type="text" name="log" class="text regular-text required" tabindex="1001" id="login_username" value="" />
						</label>
					</div>

					<div class="form-field">
						<label>
							<?php _e('Password:', APP_TD ); ?>
							<input type="password" name="pwd" class="text regular-text required" tabindex="1002" id="login_password" value="" />
						</label>
					</div>	

					<div class="form-field">
						<input tabindex="4" type="submit" id="login" name="login" value="<?php _e('Login', APP_TD ); ?>" />
						<input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect); ?>" />
						<input type="hidden" name="testcookie" value="1" />															
					</div>

					<div class="form-field">
						<input type="checkbox" name="rememberme" class="checkbox" tabindex="3" id="rememberme" value="forever" checked="checked"/>
						<label for="rememberme"><?php _e('Remember me', APP_TD ); ?></label>
					</div>

					<div class="form-field">	
						<a href="<?php echo appthemes_get_password_recovery_url(); ?>"><?php _e('Lost your password?', APP_TD ); ?></a>
					</div>

					<?php //wp_register('<div class="form-field" id="register">','</div>'); ?>

					<?php do_action('login_form'); ?>
				</fieldset>

				<!-- autofocus the field -->
				<script type="text/javascript">try{document.getElementById('login_username').focus();}catch(e){}</script>
			</form>
		</div>
	<?php } else if (is_page_template('form-login.php')) { // In practice, you only get here upon failed login I think ?>
		<div id="sidebar" class="threecol last">
			<!--<h3>Welcome, Guest</h3>-->

			<p>If you don't have an account, you can create one as a <a href="/volunteer-registration">volunteer</a> or <a href="/coordinators">coordinator</a>.</p>
		</div>
	<?php	
	} elseif(is_page_template('page-search.php')) {
		?><div id="sidebar" class="threecol last"><?php
		echo recent_searches();
		?></div><?php
	}
	?>

<?php endif;
