<?php
global $va_options;
wp_enqueue_script('maskedinput', plugins_url() . '/volunteer/js/jquery.maskedinput-1.3.min.js', array('jquery'));
wp_enqueue_script('jquery-ui-datepicker');
?>
<div id="main">

<?php do_action('appthemes_notices'); ?>

    <div class="section-head">
        <h1><?php echo $title; ?></h1>
    </div>

    <form id="create-listing" enctype="multipart/form-data" method="post" action="<?php echo va_get_listing_create_url(); ?>">
<?php wp_nonce_field('va_create_listing'); ?>
        <input type="hidden" name="action" value="<?php echo (!empty($listing->ID) ? 'edit-listing' : 'new-listing' ); ?>" />
        <input type="hidden" name="ID" value="<?php echo esc_attr($listing->ID); ?>" />

        <?php
        // Workaround a major bug in Vantage - default values for radio groups are not honored, and it's not setting a default hidden value (as a result?).  
        // So hardcode a hidden field with of the valid values configured on the Form Builder admin screen.
        ?>
        <input type="hidden" name="app_adaaccessible" value="No" />
        <input type="hidden" name="app_certifyingacceptanceagent" value="No" />
        <input type="hidden" name="app_closestbartstation" value="" />

        <fieldset id="essential-fields">

            <div class="form-field"><label>
<?php _e('Tax Site Name', APP_TD); ?>
                    <input name="post_title" type="text" value="<?php echo esc_attr($listing->post_title); ?>" class="required" />
                </label></div>

            <div class="form-field">
<?php $coord = appthemes_get_coordinates($listing->ID); ?>
                <input name="lat" type="hidden" value="<?php echo esc_attr($coord->lat); ?>" />
                <input name="lng" type="hidden" value="<?php echo esc_attr($coord->lng); ?>" />

                <label>
<?php _e('Address (Street, City, State, Zip code)', APP_TD); ?>
                    <input id="listing-address" name="address" type="text" value="<?php echo esc_attr($listing->address); ?>" class="required" />
                </label>
                <input id="listing-find-on-map" class="btn-small" type="button" value="<?php esc_attr_e('Find on map', APP_TD); ?>">

                <div id="listing-map"></div>
            </div>
        </fieldset>

        <?php
        // if categories are locked display only the current listing category
        if (va_categories_locked())
            $listing_cat = $listing->category;
        else
            $listing_cat = array();
        ?>
        
        <fieldset id="category-fields">
            <div class="featured-head"><h3><?php _e('Tax Site Details', APP_TD); ?></h3></div>
            <div class="form-field"><label>
                    <?php _e('County', APP_TD); ?>
                    <?php
                    wp_dropdown_categories(array(
                        'taxonomy' => VA_LISTING_CATEGORY,
                        'hide_empty' => false,
                        'hierarchical' => true,
                        'name' => VA_LISTING_CATEGORY,
                        'selected' => $listing->category,
                        'show_option_none' => __('Select Category', APP_TD),
                        'class' => 'required',
                        'orderby' => 'name',
                        'include' => $listing_cat
                    ));
                    ?>
                </label></div>    

            <div id="custom-fields">
                <?php
                if ($listing->category) {
                    the_listing_files_editor($listing->ID);

                    va_render_form((int) $listing->category, $listing->ID);
                }
                ?>
            </div>

        </fieldset>

        <fieldset id="contact-fields">
            <div class="featured-head"><h3><?php _e('Public Contact Information for Site', APP_TD); ?></h3></div>

            <div class="form-field phone"><label>
<?php _e('Phone Number (415-555-5555)', APP_TD); ?>
                    <input id="app_contactphone" name="phone" type="text" value="<?php echo esc_attr($listing->phone); ?>" />
                </label></div>

            <div class="form-field listing-urls web">
                <label>
<?php _e('Website', APP_TD); ?><br />
                    <span>http://</span><input name="website" type="text" value="<?php echo esc_attr($listing->website); ?>" />
                </label>
            </div>

            <div class="form-field email listing-urls">
                <label>
<?php _e('Email', APP_TD); ?><br />
                    <input name="email" type="text" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'email', true)); ?>" />
                </label>
            </div>

            <div class="form-field listing-urls twitter">
                <label>
<?php _e('Twitter', APP_TD); ?>
                    <span>@</span><input name="twitter" type="text" value="<?php echo esc_attr($listing->twitter); ?>" />
                </label>
            </div>

            <div class="form-field listing-urls facebook">
                <label>
<?php _e('Facebook', APP_TD); ?>
                    <span>facebook.com/</span><input name="facebook" type="text" value="<?php echo esc_attr($listing->facebook); ?>" />
                </label>
            </div>
        </fieldset>

        <fieldset id="misc-fields">
            <div class="featured-head"><h3><?php _e('Listing Images', APP_TD); ?></h3></div>

            <div class="form-field images">
        <?php the_listing_image_editor($listing->ID); ?>
            </div>
        </fieldset>

<?php do_action('va_after_create_listing_form'); ?>

        <fieldset>
            <div class="form-field"><input type="submit" id="btn-submit" style="display:none" value="<?php echo esc_attr($action); ?>" /></div>
        </fieldset>

    </form>

</div><!-- #content -->

<script type="text/javascript">

<?php if (!empty($listing->ID)) { // Call onCategoryLoadComplete() as soon as the map is loaded, as this is an edit ?>
    function onMapLoadComplete()
    {
        onCategoryLoadComplete();
    }      
<?php } ?>

    // Called when the user has selected a County (Category), or upon load in an "edit" scenario
    function onCategoryLoadComplete()
    {
        jQuery(document).ready(function($) {
            
            $('#app_numberoftaxpreparersneeded,#app_numberofinterpretersneeded,#app_numberofgreetersneeded').rules("add", {
                'required' : true,
                'min': 0
            });
            
            $('#app_contactphone,#app_sitecoordinatorphonenumber').mask("999-999-9999");

            $("#app_openingdate,#app_closingdate").datepicker();

            renderScheduleCreatorWidget();
            
            $('#btn-submit').show();

            // Render "Hours of Operation" schedule creator widget
            function renderScheduleCreatorWidget()
            {     
                $('#app_hoursofoperation').wrap('<div id="schedule-wrapper">').parent().prepend('<div id="schedule-editor">');
                
                var text = $('#app_hoursofoperation').hide().text().split(' ');
                var n = (text.length - 1) / 12;
                n = Math.max(3, n);

                for (var i = 1; i <= n; i++) {
                    $('#schedule-editor').append(row(i));
                }
                //$('#schedule-wrapper').height($('#schedule-editor').height());

                var q = 0;
                $('#schedule-editor select').each(function() {
                    if (q < text.length) {
                        var col = (q + 1) % 12;
                        if (col == 4 || col == 7 || col == 10) {
                            q++;
                        }
                        $(this).val(text[q]);
                        //console.log('[' + q + '] = ' + text[q]);
                        q++;
                    }
                });

                $('#schedule-editor').append('<div><input type="button" id="schedule-add" value="+" class="btn-small" /></div>');

                $('#schedule-add').click(function() {
                    n++;
                    $(this).before(row(n));
                    //$('#schedule-wrapper').height($('#schedule-editor').height());
                });

                function row(i) {
                    function days(i) {
                        var days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        var days_select = '<select name="day' + i + '" id="day' + i + '">';
                        for (var key in days) {
                            var val = days [key];
                            days_select += '<option value="' + val + '">' + val + '</option>'
                        }
                        days_select += '</select>'
                        return days_select;
                    }
                    function time(i, j) {
                        var minutes = ['00', '15', '30', '45'];
                        var select = '<select name="time' + i + '_' + j + '" id="time' + i + '_' + j + '">';
                        for (var h = 0; h < 12; h++) {
                            for (var key in minutes) {
                                var val = h + ':' + minutes [key];
                                select += '<option value="' + val + '">' + val + '</option>'
                            }
                        }
                        select += '</select>'
                        return select;
                    }
                    function ampm(i, j) {
                        var ap = ['am', 'pm'];
                        var select = '<select name="ampm' + i + '_' + j + '" id="ampm' + i + '_' + j + '">';
                        for (var key in ap) {
                            var val = ap [key];
                            select += '<option value="' + val + '">' + val + '</option>'
                        }
                        select += '</select>'
                        return select;
                    }

                    var html = '<tr><td>' + days(i) + '</td><td>' + time(i, 1) + '</td><td>' + ampm(i, 1) + '</td><td> &nbsp;to&nbsp; </td><td>' + time(i, 2) + '</td><td>' + ampm(i, 2) + '</td>'
                            + '<td> &nbsp;and&nbsp; </td><td>' + time(i, 3) + '</td><td>' + ampm(i, 3) + '</td><td>&nbsp;to&nbsp;</td><td>' + time(i, 4) + '</td><td>' + ampm(i, 4) + '</td></tr>';
                    return html;
                }


                $('#schedule-editor select').live('change', function() {
                    var text = '';
                    var i = 1;
                    $('#schedule-editor tr').each(function() {
                        text += $('#day' + i).val() + ' ' + $('#time' + i + '_1').val() + ' ' + $('#ampm' + i + '_1').val() + ' to ' + $('#time' + i + '_2').val() + ' ' + $('#ampm' + i + '_2').val()
                                + ' and ' + $('#time' + i + '_3').val() + ' ' + $('#ampm' + i + '_3').val() + ' to ' + $('#time' + i + '_4').val() + ' ' + $('#ampm' + i + '_4').val() + ' <br>'
//                text += $(this).val() + ' ';
                        i++;
                    });
                    //console.log(text);

                    $('#app_hoursofoperation').html(text);
                });
            }
        });

    }
</script>

<?php get_sidebar(); ?>