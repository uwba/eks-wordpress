<?php
// Template Name: Volunteer: Registration
?>
<?php
if (!isset($_SESSION['volunteer']['steps'])) {
    $_SESSION['volunteer']['steps'] = array();
}

$_SESSION['role'] = "volunteer";
?>

<!--<pre><?php //var_dump($_SESSION['volunteer']); var_dump($_POST);  ?></pre>-->

<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<div id="main" class="list">
	<div class="section-head">
		<h1><?php _e( 'Volunteer Registration', APP_TD ); ?></h1>
	</div>

    <div style="margin-left:30px">
<?php if (is_volunteer()) { ?>
                You are registered already!!
            <?php
            } else {
                $step = 1;
                if (is_user_logged_in()) {
                    $step = 2;
                }

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
                ?>
                <p>Help families file taxes, claim credits, and build assets.</p>

                <a id="volunteer_register" href="<?php echo site_url('wp-admin/admin-ajax.php?action=myajax-submit'); ?>">Volunteer Sign Up</a>
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
    <?
    //Login_Radius_Connect_button(); 
    //			Login_Radius_widget_Connect_button();
    ?>

                    </form>

                    <form id="step2" class="step" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
                        <h2>POSITION: I am interested in volunteering as a...</h2>
                        <div class="error"></div>
                        <label><input type="checkbox" name="position[]" value="preparer" id="position" class="position" <?php if (!empty($_SESSION['volunteer']['position'][0]) && $_SESSION['volunteer']['position'][0] == 'preparer') echo 'selected="selected" ' ?>/> Tax Preparer<br/><span>Tax preparers assist taxpayers with their tax returns during the tax season. Volunteers will complete a training course and become IRS certified to prepare taxes. Bilingual tax preparers are always in high demand.</span></label><br/>
                        <div id="preparer-sub">Experience:  
                            <label><input type="radio" name="preparer" value="new" <?php if (empty($_SESSION['volunteer']['preparer']) || (!empty($_SESSION['volunteer']['preparer']) && $_SESSION['volunteer']['preparer'] == 'new')) echo 'checked ' ?> />New tax preparer</label>
                            <label><input type="radio" name="preparer" value="returning" <?php if (!empty($_SESSION['volunteer']['preparer']) && $_SESSION['volunteer']['preparer'] == 'returning') echo 'checked ' ?> />Returning tax preparer</label><br/>
                        </div>
                        <label><input type="checkbox" name="position[]" value="screener" id="screener" class="position" <?php if (!empty($_SESSION['volunteer']['position'][0]) && $_SESSION['volunteer']['position'][0] == 'screener') echo 'selected="selected" ' ?>/> Screener<br/><span>Screeners are responsible for assisting clients with the intake form and ensuring taxpayers have the correct tax documents and identification. This is a critical role as it is the initial point at which important tax return information is gathered and verified.</span></label><br/>
                        <label><input type="checkbox" name="position[]" value="greeter" id="greeter" class="position" <?php if (!empty($_SESSION['volunteer']['position'][0]) && $_SESSION['volunteer']['position'][0] == 'greeter') echo 'selected="selected" ' ?>/> Greeter<br/><span>Greeters welcome tax filers to the tax site during the tax season, and may also inform taxpayers about other available community resources. Training is provided by each tax site.</span></label><br/>
                        <label><input type="checkbox" name="position[]" value="interpreter" id="interpreter" class="position" <?php if (!empty($_SESSION['volunteer']['position'][0]) && $_SESSION['volunteer']['position'][0] == 'interpreter') echo 'selected="selected" ' ?>/> Interpreter<br/><span>Interpreters aid taxpayers by working alongside the tax clients and tax preparers. Spanish speaking Volunteers are always needed. Training is provided by each tax site.</span></label><br/>
                        <input type="submit" value="Next"/>
                    </form>

                    <div id="step3" class="step">
                        <form id="step31" action="<?php echo admin_url('admin-ajax.php'); ?>" method="get" class="mti_font_element">
                            <h2>LOCATION: I want to volunteer near...</h2>
                            <div class="yui3-g">
                                <div class="yui3-u-3-4">
                                    <div id="main-search" class="mti_font_element">
                                        <div class="search-for mti_font_element">
                                            <label for="search-text" class="mti_font_element">
                                                <span class="search-title mti_font_element">Search For </span><span class="search-help mti_font_element">Enter zipcode or keyword to find a free tax site.</span>
                                            </label>
                                            <div>
                                                <input type="text" id="search-text" name="search_terms" size="30" style="width:100%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="yui3-u-1-4">
                                    <div class="search-button mti_font_element">
                                        <!-- <input type="image" src="http://eks.local/wp-content/themes/vantage/images/search.png" value="Search" /> -->
                                        <button class="rounded-small mti_font_element" id="search-submit" type="submit">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form id="step32" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
                             <div id="results" style="height:300px;overflow-y:scroll;border:1px solid #ccc;margin:10px 0;padding:10px"></div>
                             <input type="button" class="back" value="Back"/>
                             <!--<input type="submit" value="Next"/>-->
                        </form>                         
                        
                    </div>
                </div>

                <!--p>
                <a id="facebook_register" href="<?php echo site_url('wp-admin/admin-ajax.php?action=myajax-submit'); ?>">Social Login</a>
                </p>
                <div id="facebook_dialog">
                        <form id="facebook_form" class="step" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
                <?
                //Login_Radius_Connect_button(); 
                //			Login_Radius_widget_Connect_button();
                ?>

                        </form>


                        <div id="step5" class="step">
                                <h2>Thank you</h2>
                                <a href="" id="volunteer_close">Close</a>
                        </div>
                </div-->

            <?php } ?>

    </div>
</div>
<?php get_sidebar(); ?>

