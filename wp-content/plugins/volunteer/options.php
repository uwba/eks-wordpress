<?php

function eks_register_mysettings() {
    //register our settings
    register_setting('eks-settings-group', 'volunteer_enable_registration');    
    register_setting('eks-settings-group', 'volunteer_email_body');
    register_setting('eks-settings-group', 'volunteer_email_subject');
    
    register_setting('eks-settings-group', 'terms_preparer');
    register_setting('eks-settings-group', 'terms_greeter');
    register_setting('eks-settings-group', 'terms_interpreter');
    register_setting('eks-settings-group', 'terms_screener');
    
    $counties = get_categories(
                        array(
                            'taxonomy' => VA_LISTING_CATEGORY,
                            'name' => VA_LISTING_CATEGORY,
                            'hide_empty' => false,
                            'orderby' => 'name'
                            ));
    foreach($counties as $el)
    {
        $key = 'email_notifications_' . $el->term_id;
        register_setting('eks-settings-group', $key);
    }
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
                <legend>Tax Site Email Notifications</legend>
                <p>These people will be notified when Tax Sites are added or updated in the given county.  Enter addresses in the form of a comma-separated list.</p>
                <?php $counties = get_categories(
                        array(
                            'taxonomy' => VA_LISTING_CATEGORY,
                            'name' => VA_LISTING_CATEGORY,
                            'hide_empty' => false,
                            'orderby' => 'name'
                            ));

                foreach($counties as $el)
                {
                    $key = 'email_notifications_' . $el->term_id;
                    ?>
                    <div class="label"><?php echo $el->name ?></div>
                    <input type="text" name="<?php echo $key ?>" style="width:100%" value="<?php echo get_option($key); ?>" placeholder="Enter a comma-separated list of email addresses" />
                <?php
                }
                ?>
            </fieldset>
            
            <fieldset>
                <legend>Volunteer Registration</legend>
                
                <div class="label">Enable Volunteers to Register?</div>
                <input type="hidden" name="volunteer_enable_registration" value="0" />
                <input type="checkbox" name="volunteer_enable_registration" value="1" <?php echo get_option('volunteer_enable_registration') == 1 ? 'checked' : '' ?> />
                       
                <p>Below is the content of the email that new volunteers and coordinators will receive upon registration.  For best results, only use plain text.  The special tokens <code>[USERNAME]</code> and <code>[PASSWORD]</code> will be replaced with the user's details when the email is sent.</p>
                <?php settings_fields('eks-settings-group'); ?>

                <div class="label">Email Subject</div>
                <input type="text" name="volunteer_email_subject" style="width:50%" value="<?php echo get_option('volunteer_email_subject'); ?>" />

                <div class="label">Email Body</div>
                <?php wp_editor(get_option('volunteer_email_body'), 'volunteer_email_body', array(
                    'media_buttons' => false
                )); ?>
            </fieldset>   
            
            <fieldset>
                <legend>Terms of Use</legend>
                <p>This is the copy that new volunteers will view during the registration process.</p>

                <div class="label">Tax Preparer</div>
                <?php wp_editor(get_option('terms_preparer'), 'terms_preparer', array(
                    'media_buttons' => false
                )); ?>
                
                <div class="label">Greeter</div>
                <?php wp_editor(get_option('terms_greeter'), 'terms_greeter', array(
                    'media_buttons' => false
                )); ?>
                                
                <div class="label">Interpreter</div>
                <?php wp_editor(get_option('terms_interpreter'), 'terms_interpreter', array(
                    'media_buttons' => false
                )); ?>
                                                
                <div class="label">Screener</div>
                <?php wp_editor(get_option('terms_screener'), 'terms_screener', array(
                    'media_buttons' => false
                )); ?>
            </fieldset>   
            <?php submit_button(); ?>

        </form>
    </div>
<?php } ?>