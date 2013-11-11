<?php
// Template Name: Volunteer: Registration
?>
<?php
if (!isset($_SESSION['volunteer']['steps'])) {
    $_SESSION['volunteer']['steps'] = array();
}

$_SESSION['role'] = "volunteer";
?>

<!--<pre><?php //var_dump($_SESSION['volunteer']); var_dump($_POST);     ?></pre>-->

<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<div id="main" class="list">
    <div class="section-head">
        <h1><?php _e('Volunteer Registration', APP_TD); ?></h1>
    </div>

    <div style="padding: 0 30px">
        <?php
        $step = 1;
        if (is_user_logged_in())
            $step = 2;

        // embed the javascript file that makes the AJAX request
        wp_enqueue_script('volunteer-registration', plugins_url() . '/volunteer/js/registration.js', array('jquery', 'jquery-ui-dialog'));

        /* form processing */
        wp_enqueue_script('json-form', plugins_url() . '/volunteer/js/jquery.form.js', array('jquery'));
        wp_enqueue_script('validate', plugins_url() . '/volunteer/js/jquery.validate.min.js', array('jquery'));
        wp_enqueue_script('maskedinput', plugins_url() . '/volunteer/js/jquery.maskedinput-1.3.min.js', array('jquery', 'validate'));

        // To avoid tables.  Sadly using Bootstrap broke the existing theme due to CSS collisions, so we have to use YUI instead...
        wp_enqueue_style('yui', 'http://yui.yahooapis.com/3.8.1/build/cssgrids/grids-min.css');

        // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
        // http://site.local/wp-admin/admin-ajax.php?action=myajax-submit&postID=1
        wp_localize_script('volunteer-registration', 'Volunteer', array('ajaxurl' => admin_url('admin-ajax.php'), 'step' => $step, 'action' => 'myajax-submit', 'success_url' => '/my-tax-sites'));
        wp_localize_script('volunteer-registration', 'TaxSearch', array('ajaxurl' => admin_url('admin-ajax.php'), 'action' => 'tax_search'));

        if (get_option('volunteer_enable_registration') == 1) {

            if (!is_volunteer()) { // Person is not a volunteer yet 
                ?>
                <p>Thank you for your interest in volunteering with Earn It! Keep It! Save It!  </p>
                <p>To complete your registration and confirm your commitment to volunteer for the upcoming tax season, 
                <?php } else { ?>
                <p>To update your volunteer registration, 
                <?php } ?>
                <a id="volunteer_register" href="<?php echo site_url('wp-admin/admin-ajax.php?action=myajax-submit'); ?>"><?php echo!(is_volunteer()) ? 'sign up to volunteer now' : 'click here' ?></a>.
            </p>

            <?php if (!is_volunteer()) { // Person is not a volunteer yet 
                ?>
            <p>&nbsp</p>
                <p>If you are interested but only want more information at this time, <a href="/volunteer">click here to find out more</a>.
                </p>
            <?php } ?>

            <div id="volunteer_dialog">
                <form id="step1" class="step" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
                    <div class="error"></div>
                    <p>All fields are required.</p>
                    <div class="yui3-g">
                        <div class="yui3-u-1-2">
                            <label>Full Name: <input type="text" name="name" id="name" size="30" maxlength="100" value="<?php echo empty($_SESSION['volunteer']['name']) ? '' : $_SESSION['volunteer']['name'] ?>" minlength="2" required /></label>
                        </div>
                        <div class="yui3-u-1-2">
                            <label>Username: <input type="text" name="username" id="username" size="30" maxlength="100" value="<?php echo empty($_SESSION['volunteer']['username']) ? '' : $_SESSION['volunteer']['username'] ?>" required /></label>
                        </div>
                        <div class="yui3-u-1">
                            <label>Daytime Phone: <input type="text" name="phone" id="phone" size="30" maxlength="14" value="<?php echo empty($_SESSION['volunteer']['phone']) ? '' : $_SESSION['volunteer']['phone'] ?>" /></label>                                
                        </div>
                        <div class="yui3-u-1-2">
                            <label>E-mail Address: <input type="text" name="email" id="email" size="30" maxlength="100" value="<?php echo empty($_SESSION['volunteer']['email']) ? '' : $_SESSION['volunteer']['email'] ?>" required/></label>                                
                        </div>
                        <div class="yui3-u-1-2">
                            <label>Confirm E-mail Address: <input type="text" name="email_confirm" id="email_confirm" size="30" maxlength="100" value="<?php echo empty($_SESSION['volunteer']['email_confirm']) ? '' : $_SESSION['volunteer']['email_confirm'] ?>" required /></label>
                        </div>
                        <div class="yui3-u-1-2">
                            <label>Password: <input type="password" name="password" id="password" size="30" maxlength="100" value="<?php echo empty($_SESSION['volunteer']['password']) ? '' : $_SESSION['volunteer']['password'] ?>" required /></label>
                        </div>
                        <div class="yui3-u-1-2">
                            <label>Confirm Password: <input type="password" name="password_confirm" id="password_confirm" size="30" maxlength="100" value="<?php echo empty($_SESSION['volunteer']['password_confirm']) ? '' : $_SESSION['volunteer']['password_confirm'] ?>" required /></label>
                        </div>
                    </div>
                    <input type="submit" value="Next"/>	
                </form>

                <form id="step2" class="step" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
                    <h3>Please choose the volunteer position you are interested in.</h3>
                    <div class="error"></div>

                    <label>
                        <input class="position" id="position" name="position[]" type="radio" value="preparer" <?php echo!empty($_SESSION['volunteer']['position'][0]) && $_SESSION['volunteer']['position'][0] == 'preparer' ? 'checked' : ''; ?> /> Tax Preparer<br/><span>Tax preparers assist taxpayers with their tax returns. Volunteers will complete a training course and become IRS certified to prepare taxes. Bilingual tax preparers are always in high demand.</span>
                    </label><br/>

                    <div id="preparer-sub">Experience:  
                        <label><input type="radio" name="preparer" value="new" <?php if (empty($_SESSION['volunteer']['preparer']) || (!empty($_SESSION['volunteer']['preparer']) && $_SESSION['volunteer']['preparer'] == 'new')) echo 'checked ' ?> />New tax preparer</label>
                        <label><input type="radio" name="preparer" value="returning" <?php if (!empty($_SESSION['volunteer']['preparer']) && $_SESSION['volunteer']['preparer'] == 'returning') echo 'checked ' ?> />Returning tax preparer</label><br/>
                    </div>

                    <label>
                        <input class="position" name="position[]" type="radio" value="greeter" id="greeter" <?php echo!empty($_SESSION['volunteer']['position'][0]) && $_SESSION['volunteer']['position'][0] == 'greeter' ? 'checked' : ''; ?> /> Greeter<br/><span>Greeters welcome tax filers to the tax site during the tax season, and may also inform taxpayers about other available community resources. Training is provided by each tax site.</span>
                    </label><br/>

                    <label>
                        <input class="position" name="position[]" type="radio" value="interpreter" id="interpreter" <?php echo!empty($_SESSION['volunteer']['position'][0]) && $_SESSION['volunteer']['position'][0] == 'interpreter' ? 'checked' : ''; ?> /> Interpreter<br/><span>Interpreters aid taxpayers by working alongside them and their tax preparers. Spanish-speaking volunteers are always needed. Training is provided by each tax site.</span>
                    </label><br/>

                    <input type="submit" value="Next"/>
                </form>

                <div id="step3" class="step">
                    <h3>Please read the Terms of Use below and click Next to confirm your agreement.</h3>
                    <form method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
                        <div style="height:300px;overflow-y:scroll;border:1px solid #ccc;margin:10px 0;padding:10px"></div>
                        <input type="button" class="back" value="Back"/>
                        <input type="submit" value="Next"/>
                    </form>     
                </div>

                <div id="step4" class="step">
                    <form id="step41" action="<?php echo admin_url('admin-ajax.php'); ?>" method="get" class="mti_font_element">
                        <h3>Please select a tax site you are interested in volunteering at by using the search box below.</h3>
                        <div class="yui3-g">
                            <div class="yui3-u-2-3">
                                <div id="main-search" class="mti_font_element">
                                    <div class="search-for mti_font_element">
                                        <label for="search-text" class="mti_font_element">
                                            <span class="search-help mti_font_element">Enter the county, city, or day of week you would like to volunteer.</span>
                                        </label>
                                        <div>
                                            <input type="text" id="search-text" name="search_terms" size="30" style="width:100%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="yui3-u-1-3">
                                <div class="search-button mti_font_element">
                                    <button class="rounded-small mti_font_element" id="search-submit" type="submit">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="step42" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
                        <!-- Tax Site Results -->
                        <div id="results" style="height:300px;overflow-y:scroll;border:1px solid #ccc;margin:10px 0;padding:10px"></div>
                        <input type="button" class="back" value="Back"/>
                    </form>                         

                </div>

                <div id="step5" class="step">
                    <form action="<?php echo admin_url('admin-ajax.php'); ?>" method="POST" class="mti_font_element">
                        <h3>Please select the <span></span> training you would like to view.</h3>
                        <!-- Training Results -->
                        <div class="results" style="height:300px;overflow-y:scroll;border:1px solid #ccc;margin:10px 0;padding:10px"></div>
                        <p style="padding-bottom:20px">For all other volunteer positions, your Site Coordinator will be in contact with you regarding your training.</p>
                        <input type="button" class="back" value="Back"/>
                        <input type="submit" value="Next"/>
                    </form>                         
                </div>

                <div id="step6" class="step">
                    <form action="<?php echo admin_url('admin-ajax.php'); ?>" method="POST" class="mti_font_element">
                        <h3></h3>
                        <div class="results"></div>
                        <p style="padding-bottom:20px" class="footer"></p>
                        <input type="button" class="back" value="Back"/>
                        <input type="submit" value="Confirm"/>

                    </form>                         
                </div>
                <div id="step7" class="step">
                    <h3>Your registration is complete!</h3>
                    <p>You will receive two emails: one with your EarnItKeepItSaveIt! account information, and one with your tax site and training details.</p>
                    <p>Log back in here to view your account at anytime!</p>
                    <p style="padding-bottom:20px">Thank you for signing up to volunteer with EarnItKeepItSaveIt!</p>
                    <input type="submit" value="Continue"/>                    
                </div>
            </div>
        <?php } else { // Volunteer registration is not enabled, so display the Easy Sign Up plugin.  ?>

            <p>Thank you for your interest, but we are not yet accepting volunteer registrations. Please fill out the form below and be notified when registration opens.</p>

            <?php echo do_shortcode('[easy_sign_up]') ?>


        <?php } ?>
    </div>
</div>
<?php get_sidebar(); ?>

