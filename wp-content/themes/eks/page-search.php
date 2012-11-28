<?php
// Template Name: Page search and Listings
//wp_enqueue_script('volunteer-registration', plugins_url() . '/volunteer/js/registration.js', array('jquery'));
?>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script>
	function initializeMap(lat, lng, id) {
  var latlng = new google.maps.LatLng(lat, lng);
  var myOptions = {
    zoom: 13,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  var map = new google.maps.Map(document.getElementById(id), myOptions);
  
  // Creating a marker and positioning it on the map    
  var marker = new google.maps.Marker({    
    position: latlng,    
    map: map    
  });  
}

function codeAddress(address, id) {
	var geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
			initializeMap(results[0].geometry.location.Xa, results[0].geometry.location.Ya, id);
      } else {
        //alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }
	</script>
<div id="main" class="list">
<div class="section-head">
    <h1><?php _e('Tax Sites', APP_TD); ?></h1>
</div>

<article class="filter">
<form name="sobiSearchFormContainer" method="get" accept-charset="utf-8" id="sobiSearchFormContainer">
<table class="sobi2eSearchForm">
    <tbody>
    <tr>
        <td id="sobi2eSearchLabel" colspan="2">Enter zipcode or keyword to find a free tax site.</td>
    </tr>
    <tr>
        <td id="sobi2eSearchBox">
            <input onblur="if (this.value == '') this.value = 'Search ... ';"
                   onclick="if (this.value == 'Search ... ') this.value = '';" value="<?= isset($_GET['search_terms']) ? $_GET['search_terms'] : 'Search ...' ?>" class="inputbox"
                   id="search_terms" name="search_terms">
        </td>
        <td id="sobi2eSearchButton">
            <input type="submit" value="Search" class="button" onkeydown="$('SobiSearchPage').value = 0"
                   onmousedown="$('SobiSearchPage').value = 0" name="search" id="sobiSearchSubmitBt">
        </td>
    </tr>
    <tr>
        <td id="sobi2eSearchPhrases" colspan="3">
            <input type="radio" value="any" id="searchphraseany" name="searchphrase" checked="checked">
            <label for="searchphraseany">Any words</label>
            <input type="radio" value="all" id="searchphraseall" name="searchphrase">
            <label for="searchphraseall">All words</label>
            <input type="radio" value="exact" id="searchphraseexact" name="searchphrase">
            <label for="searchphraseexact">Exact phrase</label>
        </td>
    </tr>
    <tr>
        <td id="sobi2eSearchButtonLine" colspan="3">
            <input type="button" onclick="resetSobi2SearchForm()" value="Clear Selections"
                   title="Clear search form selections" name="sobiSearchFormReset" class="button"
                   id="sobiSearchFormReset">
            <br><br>
        </td>
    </tr>
    </tbody>
</table>
<div>
    <table>
        <tbody>
        <tr>
            <td style="vertical-align:top;">Select County</td>
            <td colspan="2">
                <div style="margin-left: 0px;" id="SobiSearchForm2dropsy">
                    <div id="sdrops_0">
                        <?php
                        wp_dropdown_categories(array(
                            'taxonomy' => VA_LISTING_CATEGORY,
                            'hide_empty' => false,
                            'hierarchical' => true,
                            'name' => VA_LISTING_CATEGORY,
                            'selected' => $_GET['listing_category'],
                            'show_option_none' => __('Select County', APP_TD),
                            'class' => 'required',
                            'orderby' => 'name',
                            //                'include' => $listing_cat
                        )); ?>
                    </div>

                </div>
            </td>
        </tr>
        <tr>
            <td class="sobi2eSearchLabel">Site Name</td>
            <td colspan="2">
                <input type="text" name="site_name" value="<?= isset($_GET['site_name']) ? $_GET['site_name'] : '' ?>"/>
            </td>
        </tr>
        <tr>
            <td class="sobi2eSearchLabel">City</td>
            <td colspan="2">
				<?php 
					$field_cities = array(
						"" => "&nbsp;---- select ----&nbsp;",
                    "Alameda" => "Alameda",
                    "Albany" => "Albany",
                    "American Canyon" => "American Canyon",
                    "Antioch" => "Antioch",
                    "Bay Point" => "Bay Point",
                    "Belmont" => "Belmont",
                    "Benicia" => "Benicia",
                    "Berkeley" => "Berkeley",
                    "Brentwood" => "Brentwood",
                    "Burlingame" => "Burlingame",
                    "Calistoga" => "Calistoga",
                    "Castro Valley" => "Castro Valley",
                    "Concord" => "Concord",
                    "Daly City" => "Daly City",
                    "Danville" => "Danville",
                    "Dixon" => "Dixon",
                    "Dublin" => "Dublin",
                    "East Palo Alto" => "East Palo Alto",
                    "El Cerrito" => "El Cerrito",
                    "El Granada" => "El Granada",
                    "Emeryville" => "Emeryville",
                    "Fairfax" => "Fairfax",
                    "Fairfield" => "Fairfield",
                    "Foster City" => "Foster City",
                    "Fremont" => "Fremont",
                    "Half Moon Bay" => "Half Moon Bay",
                    "Hayward" => "Hayward",
                    "Kentfield" => "Kentfield",
                    "Livermore" => "Livermore",
                    "Marin City" => "Marin City",
                    "Martinez" => "Martinez",
                    "Menlo Park" => "Menlo Park",
                    "Mill Valley" => "Mill Valley",
                    "Millbrae" => "Millbrae",
                    "Napa" => "Napa",
                    "Newark" => "Newark",
                    "Novato" => "Novato",
                    "Oakland" => "Oakland",
                    "Oakley" => "Oakley",
                    "Pacheco" => "Pacheco",
                    "Pacifica" => "Pacifica",
                    "Pescadero" => "Pescadero",
                    "Pinole" => "Pinole",
                    "Pittsburg" => "Pittsburg",
                    "Pleasant Hill" => "Pleasant Hill",
                    "Pleasanton" => "Pleasanton",
                    "Point Reyes Station" => "Point Reyes Station",
                    "Redwood City" => "Redwood City",
                    "Richmond" => "Richmond",
                    "Rio Vista" => "Rio Vista",
                    "Rodeo" => "Rodeo",
                    "Saint Helena" => "Saint Helena",
                    "San Bruno" => "San Bruno",
                    "San Carlos" => "San Carlos",
                    "San Francisco" => "San Francisco",
                    "San Leandro" => "San Leandro",
                    "San Mateo" => "San Mateo",
                    "San Pablo" => "San Pablo",
                    "San Rafael" => "San Rafael",
                    "San Ramon" => "San Ramon",
                    "South San Francisco" => "South San Francisco",
                    "St Helena" => "St Helena",
                    "Suisun City" => "Suisun City",
                    "Union City" => "Union City",
                    "Vacaville" => "Vacaville",
                    "Vallejo" => "Vallejo",
                    "Walnut Creek" => "Walnut Creek",
                    "Yountville" => "Yountville",
					);
					echo html_options($field_cities, $_REQUEST['field_city'], array('name' => 'field_city'));
					?>
            </td>
        </tr>
        <tr>
            <td class="sobi2eSearchLabel">Zip Code</td>
            <td colspan="2">
                <input type="text" name="zip_code" value="<?= isset($_GET['zip_code']) ? $_GET['zip_code'] : '' ?>"/>
            </td>
        </tr>
        <tr>
            <td class="sobi2eSearchLabel">Closest BART Station</td>
            <td colspan="2">
<!--                <select name="app_closestbartstations" class="required valid">
                    <option selected="selected" value="0">&nbsp;---- select ----&nbsp;</option>
                    <option value="Powell">Powell</option>
                    <option value="Montgomery">Montgomery</option>
                    <option value="Civic Center">Civic Center</option>
                    <option value="Colma">Colma</option>
                    <option value="Embarcadero">Embarcadero</option>-->
                <!--</select>-->
				<?php 
					$app_closestbartstations = array(
						0 => '&nbsp;---- select ----&nbsp;',
	"12th St. Oakland City Center" => "12th St. Oakland City Center",
	"16th St. Mission (SF)" => "16th St. Mission (SF)",
	"19th St. Oakland" => "19th St. Oakland",
	"24th St. Mission (SF)" => "24th St. Mission (SF)",
	"Ashby (Berkeley)" => "Ashby (Berkeley)",
	"Balboa Park (SF)" => "Balboa Park (SF)",
	"Bay Fair (San Leandro)" => "Bay Fair (San Leandro)",
	"Castro Valley" => "Castro Valley",
	"Civic Center (SF)" => "Civic Center (SF)",
	"Coliseum/Oakland Airport" => "Coliseum/Oakland Airport",
	"Colma" => "Colma",
	"Concord" => "Concord",
	"Daly City" => "Daly City",
	"Downtown Berkeley" => "Downtown Berkeley",
	"Dublin/Pleasanton" => "Dublin/Pleasanton",
	"El Cerrito del Norte" => "El Cerrito del Norte",
	"El Cerrito Plaza" => "El Cerrito Plaza",
	"Embarcadero (SF)" => "Embarcadero (SF)",
	"Fremont" => "Fremont",
	"Fruitvale (Oakland)" => "Fruitvale (Oakland)",
	"Glen Park (SF)" => "Glen Park (SF)",
	"Hayward" => "Hayward",
	"Lafayette" => "Lafayette",
	"Lake Merritt (Oakland)" => "Lake Merritt (Oakland)",
	"MacArthur (Oakland)" => "MacArthur (Oakland)",
	"Millbrae" => "Millbrae",
	"Montgomery St. (SF)" => "Montgomery St. (SF)",
	"North Berkeley" => "North Berkeley",
	"North Concord/Martinez" => "North Concord/Martinez",
	"Orinda" => "Orinda",
	"Pittsburg/Bay Point" => "Pittsburg/Bay Point",
	"Pleasant Hill" => "Pleasant Hill",
	"Powell St. (SF)" => "Powell St. (SF)",
	"Richmond" => "Richmond",
	"Rockridge (Oakland)" => "Rockridge (Oakland)",
	"San Bruno" => "San Bruno",
	"San Leandro" => "San Leandro",
	"South Hayward" => "South Hayward",
	"South San Francisco" => "South San Francisco",
	"Union City" => "Union City",
	"Walnut Creek" => "Walnut Creek",
	"West Oakland" => "West Oakland",
					);
					echo html_options($app_closestbartstations, $_REQUEST['app_closestbartstations'], array('name' => 'app_closestbartstations'));
					?>
            </td>
        </tr>
        <tr>
            <td class="app_adaaccessible">Site is ADA-Accessible?</td>
            <td colspan="2">
                <select id="app_adaaccessible" class="inputbox" size="1" name="app_adaaccessible">
                    <option value="0">&nbsp;---- select ----&nbsp;</option>
                    <option value="Yes" <?= $_REQUEST['app_adaaccessible']=='Yes'?'selected ':''?>>Yes</option>
                    <option value="No" <?= $_REQUEST['app_adaaccessible']=='No'?'selected ':''?>>No</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="sobi2eSearchLabel">Language (other than English)</td>
            <td colspan="2">
                <select id="field_language" class="inputbox" size="1" name="field_language">
                    <option value="0">&nbsp;---- select ----&nbsp;</option>
                    <option value="Spanish" <?= $_REQUEST['field_language']=='Spanish'?'selected ':''?>>Spanish</option>
                    <option value="Cantonese" <?= $_REQUEST['field_language']=='Cantonese'?'selected ':''?>>Cantonese</option>
                    <option value="Mandarin" <?= $_REQUEST['field_language']=='Mandarin'?'selected ':''?>>Mandarin</option>
                    <option value="Tagalog" <?= $_REQUEST['field_language']=='Tagalog'?'selected ':''?>>Tagalog</option>
                    <option value="Vietnamese" <?= $_REQUEST['field_language']=='Vietnamese'?'selected ':''?>>Vietnamese</option>
                    <option value="Russian" <?= $_REQUEST['field_language']=='Russian'?'selected ':''?>>Russian</option>
                    <option value="Farsi" <?= $_REQUEST['field_language']=='Farsi'?'selected ':''?>>Farsi</option>
                    <option value="Khmer (Cambodian)" <?= $_REQUEST['field_language']=='Khmer (Cambodian)'?'selected ':''?>>Khmer (Cambodian)</option>
                    <option value="American Sign Language" <?= $_REQUEST['field_language']=='American Sign Language'?'selected ':''?>>American Sign Language</option>
                    <option value="Other" <?= $_REQUEST['field_language']=='Other'?'selected ':''?>>Other</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="sobi2eSearchLabel">Certifying Acceptance Agent</td>
            <td colspan="2">
                <select id="app_certifyingacceptanceagent" class="inputbox" size="1" name="app_certifyingacceptanceagent">
                    <option value="">&nbsp;---- select ----&nbsp;</option>
                    <option value="No" <?= $_REQUEST['app_certifyingacceptanceagent']=='No'?'selected ':''?>>No</option>
                    <option value="Yes" <?= $_REQUEST['app_certifyingacceptanceagent']=='Yes'?'selected ':''?>>Yes</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="sobi2eSearchLabel">Additional Tax Forms/Schedules Processed</td>
            <td colspan="2">
				<?php 
					$additional_tax_forms = array(
						"" => '&nbsp;---- select ----&nbsp;',
						"Schedule C or Schedule C-EZ (self)" => "Schedule C or Schedule C-EZ (self)",
						"Schedule A (itemized deductions)" => "Schedule A (itemized deductions)",
						"Health Savings Accounts (HSA)" => "Health Savings Accounts (HSA)",
						"Cancellation of Debt" => "Cancellation of Debt",
					);
					echo html_options($additional_tax_forms, $_REQUEST['app_specialtaxformsorschedulespreparedatyoursiteinadditiontothestandard1040taxreturn'], array('name' => 'app_specialtaxformsorschedulespreparedatyoursiteinadditiontothestandard1040taxreturn'));
					?>
            </td>
        </tr>
        <tr>
            <td class="sobi2eSearchLabel">Prior Year Tax Returns Processed</td>
            <td colspan="2">
                <select id="app_taxreturnsprocessedforthefollowingyearsinadditiontocurrenttaxyear" class="inputbox" size="1" name="app_taxreturnsprocessedforthefollowingyearsinadditiontocurrenttaxyear">
                    <option value="">&nbsp;---- select ----&nbsp;</option>
					<?php for($i=2003; $i<2012; $i++){
						?><option value="<?=$i?>" <?= $_REQUEST['app_taxreturnsprocessedforthefollowingyearsinadditiontocurrenttaxyear']==$i?' selected ':'' ?>><?=$i?></option><?php
					}?>
                </select>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</form>
</article>

<?php
if (isset($_GET['search'])) {

//if ($featured || is_page()) {
//    $args = $wp_query->query;
//
//    // The template is loaded as a page, not as an archive
//    if (is_page())
//        $args['post_type'] = VA_LISTING_PTYPE;
//
//    // Don't want to show featured listings a second time
//    if ($featured)
//        $args['post__not_in'] = wp_list_pluck($featured->posts, 'ID');
//
//    query_posts($args);
//}


//    1. County
//    2. Site Name + $
//    4. City + $ *
//    5. Zip Code + $
//    10. Closest BART Station + $
//    11. ADA Accessible +$
//    18.Additional Languages (other than English) + $
//    19. Certifying Acceptance Agent $
//    20. Special Tax Forms or Schedules prepared at your site (in addition to the standard 1040 tax return)$
//    21. Tax Returns Processed for the following years (in addition to current tax year): $

//var_dump($_GET);

global $wpdb;
$query = "SELECT p.*, bart.meta_value as bartstations, t.name as county,
        GROUP_CONCAT(DISTINCT l.meta_value) as languages, a.meta_value as address,
        CONCAT_WS('; ', ID, post_title, post_excerpt, post_content, bart.meta_value, t.name, GROUP_CONCAT(DISTINCT l.meta_value), a.meta_value) as search"
    . " FROM `wp_posts` as p"
    . " LEFT JOIN wp_postmeta as bart ON ID = bart.post_id AND bart.meta_key = 'app_closestbartstations'"
    . " LEFT JOIN wp_postmeta as ada ON ID = ada.post_id AND ada.meta_key = 'app_adaaccessible'"
    . " LEFT JOIN wp_postmeta as l ON ID = l.post_id AND l.meta_key = 'app_additionallanguagescheckallthatapply'"
    . " LEFT JOIN wp_postmeta as a ON ID = a.post_id AND a.meta_key = 'address'"
	. " LEFT JOIN wp_postmeta as specialtax ON ID = specialtax.post_id AND specialtax.meta_key = 'app_specialtaxformsorschedulespreparedatyoursiteinadditiontothestandard1040taxreturn'"
	. " LEFT JOIN wp_postmeta as certifying ON ID = certifying.post_id AND certifying.meta_key = 'app_certifyingacceptanceagent'"
	. " LEFT JOIN wp_postmeta as taxreturns ON ID = taxreturns.post_id AND taxreturns.meta_key = 'app_taxreturnsprocessedforthefollowingyearsinadditiontocurrenttaxyear'"

    . " LEFT JOIN wp_term_relationships ON ID = object_id"
    . " LEFT JOIN wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id AND wp_term_taxonomy.taxonomy = 'listing_category'"
    . " LEFT JOIN wp_terms AS t USING(term_id)" //for full text search

    . " WHERE post_status = 'publish' AND post_type = 'listing'"


//        ." AND meta_key = 'app_closestbartstations'  "
;



if ($_GET['listing_category'] > 0) $query .= " AND term_id = '" . $wpdb->escape($_GET['listing_category']) . "'";
if ($_GET['site_name']) $query .= " AND p.post_title like '%" . $wpdb->escape($_GET['site_name']) . "%'";
if ($_GET['field_city']) $query .= " AND a.meta_value like '%" . $wpdb->escape($_GET['field_city']) . "%'";
if ($_GET['zip_code']) $query .= " AND a.meta_value like '%" . $wpdb->escape($_GET['zip_code']) . "%'";
if ($_GET['app_closestbartstations']) $query .= " AND bart.meta_value = '" . $wpdb->escape($_GET['app_closestbartstations']) . "'";
if ($_GET['app_adaaccessible']) $query .= " AND ada.meta_value = '" . $wpdb->escape($_GET['app_adaaccessible']) . "'";
if ($_GET['field_language']) $query .= " AND l.meta_value like '%" . $wpdb->escape($_GET['field_language']) . "%'";
if ($_GET['app_specialtaxformsorschedulespreparedatyoursiteinadditiontothestandard1040taxreturn']) $query .= " AND specialtax.meta_value like '%" . $wpdb->escape($_GET['app_specialtaxformsorschedulespreparedatyoursiteinadditiontothestandard1040taxreturn']) . "%'";
if ($_GET['app_certifyingacceptanceagent']) $query .= " AND certifying.meta_value like '%" . $wpdb->escape($_GET['app_certifyingacceptanceagent']) . "%'";
if ($_GET['app_taxreturnsprocessedforthefollowingyearsinadditiontocurrenttaxyear']) $query .= " AND taxreturns.meta_value like '%" . $wpdb->escape($_GET['app_taxreturnsprocessedforthefollowingyearsinadditiontocurrenttaxyear']) . "%'";


$query .= " GROUP BY ID";

if ($_GET['search_terms'] && trim($_GET['search_terms']) != 'Search ...') {
    if ($_GET['searchphrase'] != 'exact') {
        $terms = split(' ', $_GET['search_terms']);
    } else {
        $terms = array($_GET['search_terms']);
    }
    foreach ($terms as $term) {
        $term = mysql_real_escape_string($term);
        $whereclauses[] = "search like '%$term%'";
    }
    if ($_GET['searchphrase'] == 'any') {
        $cond = 'OR';
    } else { //if 'all'
        $cond = 'AND';
    }
    $query .= " having (" . implode(" $cond ", $whereclauses) . ")";
};

$distance = 10000; // 10 km
//$postal_code; //
if (is_numeric($_GET['zip_code'])) {
	//$postal_code = (int) $_GET['zip_code'];
}

if ($postal_code && $distance) {
    $location = location_get_postalcode_data(array(
        'postal_code' => $postal_code,
        'country' => 'us',
    ));
    if (is_array($location) && isset($location['lat']) && isset($location['lon'])) {
        $distance = _location_convert_distance_to_meters($distance, 'mile');
        $latrange = earth_latitude_range($location['lon'], $location['lat'], $distance);
        $lonrange = earth_longitude_range($location['lon'], $location['lat'], $distance);

        // from location_views_handler_filter_proximity.inc
        $sql = "SELECT node.nid AS nid,
  node.title AS title,
  node_revisions.body AS body,
  activity.field_activity_participants_value AS participants,
  activity.field_activity_address_value address,
  activity.field_activity_date_value date,
  location.latitude latitude,
  location.longitude longitude
  FROM {node} node
  LEFT JOIN {location_instance} location_instance ON node.vid = location_instance.vid
  LEFT JOIN {location} location ON location_instance.lid = location.lid
  LEFT JOIN {node_revisions} node_revisions ON node.vid = node_revisions.vid
  LEFT JOIN {content_type_activity} activity ON node.vid = activity.vid
  WHERE (node.type in ('activity')) AND (node.status = 1) AND ";

        // In case we go past the 180/-180 mark for longitude.
        if ($lonrange[0] > $lonrange[1]) {
            $where = "location.latitude > %f AND location.latitude < %f AND ((location.longitude < 180 AND location.longitude > %f) OR (location.longitude < %f AND location.longitude > -180))";
        } else {
            $where = "location.latitude > %f AND location.latitude < %f AND location.longitude > %f AND location.longitude < %f";
        }
        $sql .= $where;

        // Add radius check.
        $sql .= ' AND ' . earth_distance_sql($location['lon'], $location['lat'], 'location') . ' < %f';
        $result = db_query(db_rewrite_sql($sql), $latrange[0], $latrange[1], $lonrange[0], $lonrange[1], $distance);
        $items = array();
        while ($data = db_fetch_object($result)) {
            $items[] = array(
                'nid' => $data->nid,
                'title' => $data->title,
                'url' => url('node/' . $data->nid),
                'description' => trim_text($data->body, 100, TRUE),
                'participants' => $data->participants,
                'address' => $data->address,
                'date' => $data->date,
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
            );
        }
        $search_result = theme('activity_search_result', $postal_code, $distance, $items);
    } else {
        $search_result = theme('activity_search_result', $postal_code, $distance, array());
    }
}


//echo $query;
$data = $wpdb->get_results($query, 'OBJECT');


if (count($data)) {
	foreach ($data as $post) {
	//	var_dump($item);
		if (setup_postdata($post)) {
				//the_post();?>
			<article id="post-<?php the_ID(); ?>" <?php //post_class(); ?>><?php
			
			//get_template_part('content-listing');
	
			
			
			// TODO: USE select + join instead get_post_meta
			
			$lat =  get_post_meta(get_the_ID(), 'lat', true);
$lng =  get_post_meta(get_the_ID(), 'lng', true);
$T = get_the_title();
$T = $T[0];
if ($lat && $lng) { 
	$center = $lat . ','. $lng;
} else {
	$center = esc_html(get_post_meta(get_the_ID() , 'address', true));
} ?>
<div class="result-item">
<div id="map-<?php the_ID(); ?>" class="map"><img src="http://maps.googleapis.com/maps/api/staticmap?center=<?php print $center; ?>&zoom=13&size=160x160&maptype=roadmap&markers=color:red%7Clabel:<?php echo $T; ?>%7C<?php echo $center; ?>&sensor=false" title="Click to explain"/></div><!--  -->
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#map-<?php the_ID() ?>').click(function(){
			var lat = '<?php print $lat; ?>';
			var lng = '<?php print $lng; ?>';
			if (! (lat && lng)) {
				codeAddress('<?=$center?>', 'map-<?php the_ID() ?>');
			} else {
				initializeMap(lat , lng, 'map-<?php the_ID() ?>');
			}
		});
	});
	
</script>


<h2><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

<p class="listing-cat"><?php the_listing_category(); ?></p>
<p class="listing-phone">Phone: <?php echo esc_html( get_post_meta( get_the_ID(), 'phone', true ) ); ?></p>
<p class="listing-address">Address: <?php the_listing_address(); ?></p>
<p>Opening/closing dates: <?php echo esc_html( get_post_meta( get_the_ID(), 'app_openingdate01012013', true ) ); ?> |
	<?php echo esc_html( get_post_meta( get_the_ID(), 'app_closingdate04152012', true ) ); ?>
</p>
<p class="listing-hours">Hours of operation:<br/>
	<?php echo esc_html( get_post_meta( get_the_ID(), 'app_hoursofoperation', true ) ); ?></p>
<!--<p class="listing-coordinator">
Coordinator info: <?php //echo esc_html( get_post_meta( get_the_ID(), 'app_sitecoordinatorname', true ) ); ?>
 <?php //echo esc_html( get_post_meta( get_the_ID(), 'app_sitecoordinatorphonenumber', true ) ); ?>
 <?php //echo esc_html( get_post_meta( get_the_ID(), 'app_sitecoordinatoremailaddress', true ) ); ?>
</p>-->

<p class="listing-transportation">
Parking: <?php echo esc_html( implode(', ', get_post_meta( get_the_ID(), 'app_parking', false )) ); ?> | 
Transit Agency: <?php echo esc_html( get_post_meta( get_the_ID(), 'app_busshuttlesincludetransitagencyname', true ) ); ?> |
Bart stations: <?php echo esc_html( get_post_meta( get_the_ID(), 'app_closestbartstations', true ) ); ?>
</p>
<p>ADA Accessible: <?php echo esc_html(get_post_meta(get_the_ID(), 'app_adaaccessible', true));?></p>
<p class="listing-openclose">
<p>Availability: <?php echo esc_html(implode(', ', get_post_meta(get_the_ID(), 'app_availability', false)));?></p>
<p>Special closed dates during your regularly open days/times: <?php echo esc_html(get_post_meta(get_the_ID(), 'app_specialcloseddatesduringyourregularlyopendaystimes', true));?></p>
<p>Special Notes or Instructions for your tax site: <?php echo esc_html(get_post_meta(get_the_ID(), 'app_specialnotesorinstructionsforyourtaxsite', true));?></p>
<p class="listing-lang">Addition languages: <?php echo esc_html(implode(', ', get_post_meta(get_the_ID(), 'app_additionallanguagescheckallthatapply', false))); // the_terms(get_the_ID(), 'listing_tag', 'Languages: ', ' ', ''); ?></p>
<p>Certifying Acceptance Agent: <?php echo esc_html(get_post_meta(get_the_ID(), 'app_certifyingacceptanceagent', true));?></p>
<p>Special Tax Forms or Schedules prepared at your site (in addition to the standard 1040 tax return): <?php echo esc_html(implode(', ', get_post_meta(get_the_ID(), 'app_specialtaxformsorschedulespreparedatyoursiteinadditiontothestandard1040taxreturn', false)));?></p>


</div>
<?php
			
			
			
			
			?></article><?php
		} else {
			//echo 'error';
		}
	}
	

} else {
	?><article class="listing">
        <h2><?php printf(__('Sorry, No Tax Sites Found', APP_TD)); ?></h2>
    </article><?php
}



if ($wp_query->max_num_pages > 1) : ?>
	<nav class="pagination">
		<?php appthemes_pagenavi(); ?>
	</nav>
<?php endif; 

}
?>

	
	
	
</div>





    <?php get_sidebar(app_template_base()); ?>