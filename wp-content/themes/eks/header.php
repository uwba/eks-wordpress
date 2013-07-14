<?php wp_enqueue_style('yui', 'http://yui.yahooapis.com/3.8.1/build/cssgrids/grids-min.css'); ?>

<div id="masthead" class="container">
  <div class="row">
    <div style="text-align:right">
      <?php 
            if (!eks_is_admin()) {
                if (!is_user_logged_in()) { ?>
      <a href="/volunteer-registration">Volunteer Login</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/coordinators">Coordinator Login</a>
      <?php } else { ?>
      <a href="<?php echo wp_logout_url(home_url()) ?>">Logout</a>
      <?php } 
            } ?>
    </div>
  </div>
  <div class="row" style="margin-top:0">
    <hgroup>
      <?php va_display_logo(); ?>
    </hgroup>
    <div class="advert">
      <?php dynamic_sidebar( 'header' ); ?>
    </div>
  </div>
</div>
<div id="main-navigation" class="container">
  <div class="row">
  <div id="rounded-nav-box" class="rounded">
  <div id="rounded-nav-box-overlay">
  <?php wp_nav_menu( array(
					'theme_location' => 'header',
					'container_class' => 'menu rounded',
					'items_wrap' => '<ul>%3$s</ul>',
					'fallback_cb' => false,
					'walker' => new ZG_Nav_Walker(),
				) ); ?>
  <?php if(is_post_type_archive('listing') || is_search()){ ?>
  <!--****:::::::::::: start styled form :::::::::******-->
  <div id="search-container">
  <!-- //container for form-->
  <h3>Choose your search terms</h3>
  <!-- //Header for search box -->
  <form id="searchform" method="get" action="<?php bloginfo( 'url' ); ?>">
    <!-- ***** start form process when button is pressed ******** -->
    <!-- <div id="main-search" class=""> left in comment for reference -->
    <div class="formcolumn first">

<p><label for="search-text">Search for keyword <span style="font-size:10px">(zip code, street or site name)</span></label></p>
 <input type="text" name="ls" id="search-text" class="text" value="<?php va_show_search_query_var('ls');?>" />

<!-- // search for keyword -->
<p><label for="select-language">Desired Language</label></p>
<div class="styled-select"><select name="language" id="select-language">
              <option></option>
              <option>Spanish</option>
              <option>Cantonese</option>
              <option>Mandarin</option>
              <option>Vietnamese</option>
              <option>Khmer (Cambodian)</option>
              <option>Farsi</option>
              <option>American Sign Language</option>
              <option>Russian</option>
              <option>Tagalog</option>
            </select>
</div>
<!-- // search for language -->
</div><!-- // end formcolumn first -->

<div class="formcolumn middle">
<p><label for="select-itin">ITIN Applications Processed?</label></p>
<div class="styled-select"><select name="itin" id="select-itin">
              <option></option>
              <option>Yes</option>
              <option>No</option>
            </select></div>
<p><label for="select-ada">ADA Accessible?</label></p>
<div class="styled-select"><select name="ada" id="select-ada">
              <option></option>
              <option>Yes</option>
              <option>No</option>
            </select></div>
            
</div> <!-- // end formcolumn middle --> 
<div class="formcolumn last">

<p><label for="select-county">County</label></p>
<div class="styled-select"><select name="county" id="select-county">
              <option></option>
              <option>Alameda</option>
              <option>Contra Costa</option>
              <option>Marin</option>
              <option>Napa</option>
              <option>San Francisco</option>
              <option>San Mateo</option>
              <option>Solano</option>
            </select></div>

   <!-- //end id="cities" in case needed by jQuery -->         
<div id="cities">
<p><label for="select-city">City <span style="font-size:10px">(Choose your county first)</span></label></p>
<div class="styled-select"><select name="city" id="select-city" width="120"><option></option>
              <option>Alameda</option></select></div>
<!-- //end county and city selects -->
</div><!-- //end id="cities"  in case needed by jQuery  -->
<script>
                var firstrun = true;
        jQuery(document).ready(function($) {
            
            <?php 
            if (!empty($_GET['language'])) { ?>
                    $('#select-language').val('<?php echo $_GET['language'] ?>');
            <?php } 
            if (!empty($_GET['ada'])) { ?>
                    $('#select-ada').val('<?php echo $_GET['ada'] ?>');
            <?php } 
            if (!empty($_GET['itin'])) { ?>
                    $('#select-itin').val('<?php echo $_GET['itin'] ?>');
            <?php } ?>

            var cities = {
              'Alameda': [
                  'Alameda',
                  'Albany',
                  'Berkeley',
                  'Castro Valley',
                  'Dublin',
                  'El Cerrito',
                  'Emeryville',
                  'Fremont',
                  'Hayward',
                  'Livermore',
                  'Newark',
                  'Oakland',
                  'Pleasanton',
                  'San Leandro',
                  'Union City'
              ],
              'Contra Costa': [
                  'Antioch',
                  'Bay Point',
                  'Brentwood',
                  'Concord',
                  'Danville',
                  'Martinez',
                  'Oakley',
                  'Pacheco',
                  'Pinole',
                  'Pittsburg',
                  'Pleasant Hill',
                  'Rodeo',
                  'San Pablo',
                  'San Ramon',
                  'Walnut Creek'
              ],
              'Marin': [
                  'Fairfax',
                  'Kentfield',
                  'Mill Valley',
                  'Novato',
                  'Point Reyes Station',
                  'San Rafael'
              ],
              'Napa': [
                  'American Canyon',
                  'Calistoga',
                  'Napa',
                  'St. Helena',
                  'Yountville'
              ],
              'San Francisco': [
                  'San Francisco'
              ],
              'San Mateo': [
                  'Atherton',
                  'Belmont',
                  'Burlingame',
                  'Daly City',
                  'East Palo Alto',
                  'El Granada',
                  'Foster City',
                  'Half Moon Bay',
                  'Menlo Park',
                  'Millbrae',
                  'Pacifica',
                  'Pescadero',
                  'Redwood City',
                  'San Bruno',
                  'San Carlos',
                  'San Mateo',
                  'South San Francisco'
              ],
              'Solano': [
                  'Benecia',
                  'Dixon',
                  'Fairfield',
                  'Rio Vista',
                  'Suisun City',
                  'Vacaville',
                  'Vallejo'
              ]               
            };
            
            jQuery('#select-county').change(function(){
                var county = jQuery('#select-county').val();
                
                if (county != '')
                {
                    var options = '<option></option>';
                    for (var i=0; i < cities[county].length; i++)
                    {
                        options += '<option>' + cities[county][i] + '</option>';
                    }
                    jQuery('#select-city').html(options).closest('.search-location').show();
                    if (firstrun)
                    {
                        jQuery('#select-city').val('<?php echo empty($_GET['city']) ? '' : $_GET['city']; ?>');
                        firstrun = false;
                    }
                }
                else
                    jQuery('#select-city').html('').closest('.search-location').hide();

            }).val('<?php echo empty($_GET['county']) ? '' : $_GET['county']; ?>').change();

        });
</script>
<div class="buttons">

<div><button type="submit" id="search-submit" class="">Search</button>
</div>  <!--//end button -->
</div> <!--//end search formcolumn last -->
</form><!--end form-->
</div> <!-- // end search container -->

  </div> <!--???--> 
    </div>
    </div>
    </div>
    <?php if ( '' != $orderby = va_get_search_query_var( 'orderby' )){ ?>
    <input type="hidden" name="orderby" value="<?php echo $orderby; ?>" />
    <?php } ?>
    <?php if ( '' != $radius = va_get_search_query_var( 'radius' )){ ?>
    <input type="hidden" name="radius" value="<?php echo $radius; ?>" />
    <?php } ?>
    <?php if ( isset( $_GET['listing_cat'] ) ){ ?>
    <?php foreach ( $_GET['listing_cat'] as $k=>$listing_cat ) { ?>
    <input type="hidden" name="listing_cat[]" value="<?php echo $listing_cat; ?>" />
    <?php } ?>
    <?php } ?>
    </div>
  </form>
  <?php } ?>
</div>
</div>
</div>
</div>
<div id="breadcrumbs" class="container">
  <div class="row">
    <?php if (!is_front_page()){ 
      breadcrumb_trail( array(
			'separator' => '&raquo;',
			'before' => '',
			'show_home' => '<img src="' . get_template_directory_uri() . '/images/breadcrumb-home.png" />',
		) ); 
  } ?>
  </div>
</div>
