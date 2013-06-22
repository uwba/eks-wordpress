<?php

function eks_register_mysettings() {
	//register our settings
	register_setting( 'eks-settings-group', 'volunteer_email_body' );
	register_setting( 'eks-settings-group', 'volunteer_email_subject' );
}

function eks_settings_page() {
?>
<div class="wrap">
<h2>Volunteer Notification Email</h2>

<form method="post" action="options.php">
    <p>Here you can edit the content of the email that new volunteers and coordinators will receive upon registration.  For best results, only use plain text.  The special tokens <code>[USERNAME]</code> and <code>[PASSWORD]</code> will be replaced with the user's details when the email is sent.</p>
    <?php settings_fields( 'eks-settings-group' ); ?>

    <div style="font-weight:bold;padding-top:20px">Email Subject</div>
    <input type="text" name="volunteer_email_subject" style="width:50%" value="<?php echo get_option('volunteer_email_subject'); ?>" />
     
    <div style="font-weight:bold;padding-top:20px">Email Body</div>
    <?php wp_editor( get_option('volunteer_email_body'), 'volunteer_email_body' ); ?>
            
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>