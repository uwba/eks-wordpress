<?php

function eks_register_mysettings() {
    //register our settings
    register_setting('eks-settings-group', 'volunteer_email_body');
    register_setting('eks-settings-group', 'volunteer_email_subject');
    
    register_setting('eks-settings-group', 'terms_preparer');
    register_setting('eks-settings-group', 'terms_greeter');
    register_setting('eks-settings-group', 'terms_interpreter');
    register_setting('eks-settings-group', 'terms_screener');
}

function eks_settings_page() {
    ?>
    <style type="text/css">
        form.options fieldset
        {
            border: 1px solid lightgray;
            margin: auto;
            margin-bottom: 2em;
            padding: 2em;
        }
        form.options legend
        {
            font-weight: bold;
            font-size: larger;
            padding: 0 1em;
        }
        form.options .label
        {
            font-weight: bold;
            padding-top:1em;
        }
    </style>

    <div class="wrap">
        <h2>Site Settings</h2>

        <p>Manage the EarnItKeepItSaveIt site settings below.</p>

        <form class="options" method="post" action="options.php">
            <fieldset>
                <legend>Volunteer Registration Email</legend>
                <p>This is the content of the email that new volunteers and coordinators will receive upon registration.  For best results, only use plain text.  The special tokens <code>[USERNAME]</code> and <code>[PASSWORD]</code> will be replaced with the user's details when the email is sent.</p>
                <?php settings_fields('eks-settings-group'); ?>

                <div class="label">Email Subject</div>
                <input type="text" name="volunteer_email_subject" style="width:50%" value="<?php echo get_option('volunteer_email_subject'); ?>" />

                <div class="label">Email Body</div>
                <?php wp_editor(get_option('volunteer_email_body'), 'volunteer_email_body'); ?>
            </fieldset>   
            
            <fieldset>
                <legend>Terms of Use</legend>
                <p>This is the copy that new volunteers will view during the registration process.</p>

                <div class="label">Tax Preparer</div>
                <?php wp_editor(get_option('terms_preparer'), 'terms_preparer'); ?>
                
                <div class="label">Greeter</div>
                <?php wp_editor(get_option('terms_greeter'), 'terms_greeter'); ?>
                                
                <div class="label">Interpreter</div>
                <?php wp_editor(get_option('terms_interpreter'), 'terms_interpreter'); ?>
                                                
                <div class="label">Screener</div>
                <?php wp_editor(get_option('terms_screener'), 'terms_screener'); ?>
            </fieldset>   
            <?php submit_button(); ?>

        </form>
    </div>
<?php } ?>