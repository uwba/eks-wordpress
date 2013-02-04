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
<div id="main" class="clear" >
    <div id="leftcontent">
        <article>
            <h1>Volunteer Registration</h1>

<?php if (is_volunteer()) { ?>
                You are registered already!!
            <?php
            } else {
                if (is_user_logged_in()) {
                    $step = 2;
                } else {
                    $step = 1;
                }

                // embed the javascript file that makes the AJAX request
                wp_enqueue_script('volunteer-registration', plugins_url() . '/volunteer/js/registration.js', array('jquery', 'jquery-ui-dialog'));

                /* form processing */
                wp_enqueue_script('json-form', plugins_url() . '/volunteer/js/jquery.form.js', array('jquery'));
                wp_enqueue_script('validate', plugins_url() . '/volunteer/js/jquery.validate.min.js', array('jquery'));
                wp_enqueue_script('maskedinput', plugins_url() . '/volunteer/js/jquery.maskedinput-1.3.min.js', array('jquery', 'validate'));
                
                // To avoid tables.  Sadly using Bootstrap broke the existing theme due to CSS collisions...
                wp_enqueue_style('yui', 'http://yui.yahooapis.com/3.8.1/build/cssgrids/grids-min.css');

                // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
                // http://site.local/wp-admin/admin-ajax.php?action=myajax-submit&postID=1
                wp_localize_script('volunteer-registration', 'Volunteer', array('ajaxurl' => admin_url('admin-ajax.php'), 'step' => $step, 'action' => 'myajax-submit', 'success_url' => site_url('edit-profile'))); //thank-you
                wp_localize_script('volunteer-registration', 'TaxSearch', array('ajaxurl' => admin_url('admin-ajax.php'), 'action' => 'tax_search'));
                ?>
                <p>Help families file taxes, claim credits, and build assets.</p>

                <a id="volunteer_register" href="<?php echo site_url('wp-admin/admin-ajax.php?action=myajax-submit'); ?>">Volunteer Sign Up</a>
                <div id="volunteer_dialog">
                    <form id="step1" class="step" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
                        <div class="errors"></div>
                        <p>All fields are required.</p>
                        <div class="yui3-g">
                            <div class="yui3-u-1-2">
                                <label>Name: <input type="text" name="name" id="name" size="30" maxlength="100" value="<?php echo empty($_SESSION['volunteer']['name']) ? '' : $_SESSION['volunteer']['name'] ?>" minlength="2" required /></label>
                            </div>
                            <div class="yui3-u-1-2">
                                <label>Username <input type="text" name="username" id="username" size="30" maxlength="100" value="<?php echo empty($_SESSION['volunteer']['username']) ? '' : $_SESSION['volunteer']['username'] ?>" required /></label>
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
                                <label>Password: <input type="text" name="password" id="password" size="30" maxlength="100" value="<?php echo empty($_SESSION['volunteer']['password']) ? '' : $_SESSION['volunteer']['password'] ?>" required /></label>
                            </div>
                            <div class="yui3-u-1-2">
                                <label>Confirm Password: <input type="text" name="password_confirm" id="password_confirm" size="30" maxlength="100" value="<?php echo empty($_SESSION['volunteer']['password_confirm']) ? '' : $_SESSION['volunteer']['password_confirm'] ?>" required /></label>
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
                        <div class="errors"></div>
                        <label><input type="checkbox" name="position[]" value="preparer" id="position" class="position" <?php if (!empty($_SESSION['volunteer']['position'][0]) && $_SESSION['volunteer']['position'][0] == 'preparer') echo 'selected="selected" ' ?>/> Tax Preparer<br/><span>Tax preparers assist taxpayers with their tax returns during the tax season. Volunteers will complete a training course and become IRS certified to prepare taxes. Bilingual tax preparers are always in high demand.</span></label><br/>
                        <div id="preparer-sub">Experience:  
                            <label><input type="radio" name="preparer" value="new" <?php if (!empty($_SESSION['volunteer']['preparer']) && $_SESSION['volunteer']['preparer'] == 'new') echo 'checked ' ?> />New tax preparer</label>
                            <label><input type="radio" name="preparer" value="returning" <?php if (!empty($_SESSION['volunteer']['preparer']) && $_SESSION['volunteer']['preparer'] == 'returning') echo 'checked ' ?> />Returning tax preparer</label><br/>
                        </div>
                        <label><input type="checkbox" name="position[]" value="screener" id="screener" class="position" <?php if (!empty($_SESSION['volunteer']['position'][0]) && $_SESSION['volunteer']['position'][0] == 'screener') echo 'selected="selected" ' ?>/> Screener<br/><span>Screeners are responsible for assisting clients with the intake form and ensuring taxpayers have the correct tax documents and identification. This is a critical role as it is the initial point at which important tax return information is gathered and verified.</span></label><br/>
                        <label><input type="checkbox" name="position[]" value="greeter" id="greeter" class="position" <?php if (!empty($_SESSION['volunteer']['position'][0]) && $_SESSION['volunteer']['position'][0] == 'greeter') echo 'selected="selected" ' ?>/> Greeter<br/><span>Greeters welcome tax filers to the tax site during the tax season, and may also inform taxpayers about other available community resources. Training is provided by each tax site.</span></label><br/>
                        <label><input type="checkbox" name="position[]" value="interpreter" id="interpreter" class="position" <?php if (!empty($_SESSION['volunteer']['position'][0]) && $_SESSION['volunteer']['position'][0] == 'interpreter') echo 'selected="selected" ' ?>/> Interpreter<br/><span>Interpreters aid taxpayers by working alongside the tax clients and tax preparers. Spanish speaking Volunteers are always needed. Training is provided by each tax site.</span></label><br/>
                        <input type="submit" value="Next"/>
    <?php if (false && $step == 1) { ?><input type="button" class="back" value="Back"/><?php } ?>
                    </form>

                    <div id="step3" class="step">
                        <form id="step31" action="<?php echo admin_url('admin-ajax.php'); ?>" method="get" class="mti_font_element">
                            <h2>LOCATION: I want to volunteer near...</h2>
                            <div id="main-search" class="mti_font_element">
                                <div class="search-for mti_font_element">
                                    <label for="search-text" class="mti_font_element">
                                        <span class="search-title mti_font_element">Search For </span><span class="search-help mti_font_element">(Enter zipcode or keyword to find a free tax site.</span>
                                    </label>
                                    <div class="input-cont h39 mti_font_element">
                                        <div class="left h39 mti_font_element"></div>
                                        <div class="mid h39 mti_font_element">
                                            <input type="text" value="" class="text" id="search-text" name="search_terms">
                                        </div>
                                        <div class="right h39 mti_font_element"></div>
                                    </div>
                                    <div>
                                        <input type="radio" name="searchphrase" checked="checked" value="any">Any Words
                                        <input type="radio" name="searchphrase" value="all">All Words
                                        <input type="radio" name="searchphrase" value="exact">Exact phrase
                                    </div>
                                </div>
                                <div class="drop-location">
                                    OR
    <?php
    wp_dropdown_categories(array(
        'taxonomy' => VA_LISTING_CATEGORY,
        'hide_empty' => false,
        'hierarchical' => true,
        'name' => VA_LISTING_CATEGORY,
        'selected' => empty($_GET[VA_LISTING_CATEGORY]) ? '' : $_GET[VA_LISTING_CATEGORY],
        'show_option_none' => __('Select County', APP_TD),
        'class' => 'required',
        'orderby' => 'name',
            //                'include' => $listing_cat
    ));
    ?>
                                    AND
                                    <select id="field_city" class="inputbox" size="1" name="field_city">
                                        <option value="0">&nbsp;---- select ----</option>
                                        <option value="Alameda">Alameda</option>
                                        <option value="Albany">Albany</option>
                                        <option value="American Canyon">American Canyon</option>
                                        <option value="Antioch">Antioch</option>
                                        <option value="Bay Point">Bay Point</option>
                                        <option value="Belmont">Belmont</option>
                                        <option value="Benicia">Benicia</option>
                                        <option value="Berkeley">Berkeley</option>
                                        <option value="Brentwood">Brentwood</option>
                                        <option value="Burlingame">Burlingame</option>
                                        <option value="Calistoga">Calistoga</option>
                                        <option value="Castro Valley">Castro Valley</option>
                                        <option value="Concord">Concord</option>
                                        <option value="Daly City">Daly City</option>
                                        <option value="Danville">Danville</option>
                                        <option value="Dixon">Dixon</option>
                                        <option value="Dublin">Dublin</option>
                                        <option value="East Palo Alto">East Palo Alto</option>
                                        <option value="El Cerrito">El Cerrito</option>
                                        <option value="El Granada">El Granada</option>
                                        <option value="Emeryville">Emeryville</option>
                                        <option value="Fairfax">Fairfax</option>
                                        <option value="Fairfield">Fairfield</option>
                                        <option value="Foster City">Foster City</option>
                                        <option value="Fremont">Fremont</option>
                                        <option value="Half Moon Bay">Half Moon Bay</option>
                                        <option value="Hayward">Hayward</option>
                                        <option value="Kentfield">Kentfield</option>
                                        <option value="Livermore">Livermore</option>
                                        <option value="Marin City">Marin City</option>
                                        <option value="Martinez">Martinez</option>
                                        <option value="Menlo Park">Menlo Park</option>
                                        <option value="Mill Valley">Mill Valley</option>
                                        <option value="Millbrae">Millbrae</option>
                                        <option value="Napa">Napa</option>
                                        <option value="Newark">Newark</option>
                                        <option value="Novato">Novato</option>
                                        <option value="Oakland">Oakland</option>
                                        <option value="Oakley">Oakley</option>
                                        <option value="Pacheco">Pacheco</option>
                                        <option value="Pacifica">Pacifica</option>
                                        <option value="Pescadero">Pescadero</option>
                                        <option value="Pinole">Pinole</option>
                                        <option value="Pittsburg">Pittsburg</option>
                                        <option value="Pleasant Hill">Pleasant Hill</option>
                                        <option value="Pleasanton">Pleasanton</option>
                                        <option value="Point Reyes Station">Point Reyes Station</option>
                                        <option value="Redwood City">Redwood City</option>
                                        <option value="Richmond">Richmond</option>
                                        <option value="Rio Vista">Rio Vista</option>
                                        <option value="Rodeo">Rodeo</option>
                                        <option value="Saint Helena">Saint Helena</option>
                                        <option value="San Bruno">San Bruno</option>
                                        <option value="San Carlos">San Carlos</option>
                                        <option value="San Francisco">San Francisco</option>
                                        <option value="San Leandro">San Leandro</option>
                                        <option value="San Mateo">San Mateo</option>
                                        <option value="San Pablo">San Pablo</option>
                                        <option value="San Rafael">San Rafael</option>
                                        <option value="San Ramon">San Ramon</option>
                                        <option value="South San Francisco">South San Francisco</option>
                                        <option value="St Helena">St Helena</option>
                                        <option value="Suisun City">Suisun City</option>
                                        <option value="Union City">Union City</option>
                                        <option value="Vacaville">Vacaville</option>
                                        <option value="Vallejo">Vallejo</option>
                                        <option value="Walnut Creek">Walnut Creek</option>
                                        <option value="Yountville">Yountville</option>
                                    </select>
                                </div>


                                <!--div class="search-location mti_font_element">
                                        <label for="search-location" class="mti_font_element">
                                                <span class="search-title mti_font_element">Near </span><span class="search-help mti_font_element">(Zip code or keyword)</span>
                                        </label>
                                        <div class="input-cont h39 mti_font_element">
                                                <div class="left h39 mti_font_element"></div>
                                                <div class="mid h39 mti_font_element">
                                                        <input type="text" value="" class="text" id="search-location" name="location">
                                                </div>
                                                <div class="right h39 mti_font_element"></div>
                                        </div>
                                </div-->

                                <div class="search-button mti_font_element">
                                        <!-- <input type="image" src="http://eks.local/wp-content/themes/vantage/images/search.png" value="Search" /> -->

                                    <button class="rounded-small mti_font_element" id="search-submit" type="submit">Search</button>
                                </div>
                            </div>
                        </form>
                        <form id="step32" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
                            <div id="results"></div>
                            <input type="button" class="back" value="Back"/>
                            <!--<input type="submit" value="Next"/>-->
                        </form>
                    </div>

                    <form id="step4" class="step" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
                        <div class="trainings"></div>
                        <input type="button" class="back" value="Back"/>
                        <input type="submit" value="Next"/>
                    </form>

                    <div id="step5" class="step">
                        <h2>Thank you for registering!</h2>
                        <a href="" id="volunteer_close">Go to My Volunteer Dashboard</a>
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
        </article>
    </div>
</div>

<?php get_sidebar(); ?>

