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
        // Also add a hidden field for tax_input since this now seems to be required in the latest Vantage theme version.
        ?>
        <input type="hidden" name="app_adaaccessible" value="No" />
        <input type="hidden" name="app_certifyingacceptanceagent" value="No" />
        <input type="hidden" name="app_closestbartstation" value="" />
        <input name="tax_input[<?php echo VA_LISTING_TAG; ?>]" type="hidden" value="<?php the_listing_tags_to_edit( $listing->ID ); ?>" />

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
                        'name' => '_' . VA_LISTING_CATEGORY,
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
                $('#app_hoursofoperation').hide().parent().append('<div id="scheduler" style="padding:1em 0" />');
                var days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                
                var options = '';

                var times = ["", "7:00 am", "7:30 am", "8:00 am", "8:30 am", "9:00 am", "9:30 am", "10:00 am", "10:30 am", "11:00 am", "11:30 am", 
                    "12:00 pm", "12:30 pm", "1:00 pm", "1:30 pm", "2:00 pm", "2:30 pm", "3:00 pm", "3:30 pm", "4:00 pm", "4:30 pm", "5:00 pm",
                    "5:30 pm", "6:00 pm", "6:30 pm", "7:00 pm", "7:30 pm", "8:00 pm", "8:30 pm", "9:00 pm"];
                for (var i = 0; i < times.length; i++) {

                        options += '<option value="' + times[i] + '">' + times[i] + '</option>'
                }
     
                var html = '';
                for (var i=0; i<days.length; i++) {
                    html += '<div><div style="width:20%;float:left;text-align:right">' + days[i] + '&nbsp;</div> <select id="Schedule' + days[i] + 'Start1">' + options + '</select> - <select id="Schedule' + days[i] + 'End1">' + options + '</select> and <select id="Schedule' + days[i] + 'Start2">' + options + '</select> - <select id="Schedule' + days[i] + 'End2">' + options + '</select></div>';
                }
                $('#scheduler').html(html);
                
                // Parse the schedule string
                var obj = $.parseJSON($('#app_hoursofoperation').val());
                if (obj)
                {
                    for (var i=0; i<days.length; i++) {
                        $('#Schedule' + days[i] + 'Start1').val(obj[days[i]]['Start1']);
                        $('#Schedule' + days[i] + 'End1').val(obj[days[i]]['End1']);
                        $('#Schedule' + days[i] + 'Start2').val(obj[days[i]]['Start2']);
                        $('#Schedule' + days[i] + 'End2').val(obj[days[i]]['End2']);
                    }
                }
                
                $('#scheduler select').change(function(){             
                    var obj = {};
                    // Update the textarea
                    for (var i=0; i<days.length; i++) {
                        obj[days[i]] = {};
                        obj[days[i]]['Start1'] = $('#Schedule' + days[i] + 'Start1').val();
                        obj[days[i]]['End1'] = $('#Schedule' + days[i] + 'End1').val();
                        obj[days[i]]['Start2'] = $('#Schedule' + days[i] + 'Start2').val();
                        obj[days[i]]['End2'] = $('#Schedule' + days[i] + 'End2').val();
                    }
                    $('#app_hoursofoperation').val(JSON.stringify(obj));
                }).change();
            }
        });
    }
</script>

<?php get_sidebar(); ?>