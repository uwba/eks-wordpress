<?php
// Template Name: Listings

/* AJAX check  */
//echo $_SERVER['HTTP_X_REQUESTED_WITH'];
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	/* special ajax here */
	//$result = array();
	$result = '';
	while (have_posts()) {
		the_post(); 
		$meta = get_post_meta($post->ID);
//		$counties = get_the_terms($post->ID, 'listing_category');
//		$tags = get_the_terms($post->ID, 'listing_tag');
//		var_dump($post);
//		var_dump($meta);
//		var_dump($counties);
//		var_dump($tags);
//		echo get_the_tag_list('', '', '', $post->ID);
		//$result[] = get_post($post->ID);
//1. Site Name
//2. County, Address and Phone number
//3. Hours of operation
//4. Site Coordinators Name, Phone # and Email
//5. Languages needed in addition to English
//6. Transportation (Parking, bus/shuttles, close BART stations, etc.)
//
//7. Opening date/closing date
//8. Click > SELECT THIS SITE
//		$result .= <<<HEREDOC
//<div class="result">
//	<h3><a href="{$post->guid}">{$post->post_title}</a></h3>
//	L
//</div>
HEREDOC;
	?>
<?php 
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

<button class="tax_sites" name="tax_sites" value="<?php the_ID(); ?>">SELECT THIS SITE</button>

<h2><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

<p class="listing-cat"><?php the_listing_category(); ?></p>
<p class="listing-phone">Phone: <?php echo esc_html( get_post_meta( get_the_ID(), 'phone', true ) ); ?></p>
<p class="listing-address">Address: <?php the_listing_address(); ?></p>
<p class="listing-hours">Hours of operation:<br/>
    <?php echo get_formatted_hours_of_operation( get_post_meta( get_the_ID(), 'app_hoursofoperation', true ) ); ?></p>
<p class="listing-coordinator">
Coordinator info: <?php echo esc_html( get_post_meta( get_the_ID(), 'app_sitecoordinatorname', true ) ); ?>
 <?php echo esc_html( get_post_meta( get_the_ID(), 'app_sitecoordinatorphonenumber', true ) ); ?>
 <?php echo esc_html( get_post_meta( get_the_ID(), 'app_sitecoordinatoremailaddress', true ) ); ?>
</p>
<p class="listing-lang">Addition languages: <?php echo esc_html(implode(', ', get_post_meta(get_the_ID(), 'app_additionallanguagescheckallthatapply', false))); // the_terms(get_the_ID(), 'listing_tag', 'Languages: ', ' ', ''); ?></p>
<p class="listing-transportation">
Parking: <?php echo esc_html( implode(', ', get_post_meta( get_the_ID(), 'app_parking', false )) ); ?> | 
Transit Agency: <?php echo esc_html( get_post_meta( get_the_ID(), 'app_busshuttlesincludetransitagencyname', true ) ); ?> |
Bart stations: <?php echo esc_html( get_post_meta( get_the_ID(), 'app_closestbartstations', true ) ); ?>
</p>
	<!--<p class="listing-openclose">
	Opening/closing dates: <?php //echo esc_html( get_post_meta( get_the_ID(), 'app_openingdate01012013', true ) ); ?> |
		<?php //echo esc_html( get_post_meta( get_the_ID(), 'app_closingdate04152012', true ) ); ?>
	</p>-->
<!--<p><label><input class="tax_sites" type="checkbox" name="tax_sites[]" value="<?php the_ID(); ?>" />Check to select this Tax site</label></p>-->

<!--<p><label><input class="tax_sites" type="radio" name="tax_sites" value="<?php the_ID(); ?>" />Check to select this Tax site</label></p>-->
<!--<p><label><input class="tax_sites" type="button" name="tax_sites" value="<?php the_ID(); ?>" />Check to select this Tax site</label></p>-->
<!--<form id="step2" class="step" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">-->
<!--				<p>POSITION: I am interested at volunteering as a...</p>
				<label><input type="checkbox" name="position[<?php the_ID(); ?>][]" value="preparer"  size="10" maxlength="100" <?php if ($_SESSION['volunteer']['position']['preparer']) echo 'checked="checked" ' ?>"/> Tax Preparer</label><br/>
				<label><input type="checkbox" name="position[<?php the_ID(); ?>][]" value="screener"  size="10" maxlength="100" <?php if ($_SESSION['volunteer']['position']['screener']) echo 'checked ' ?>"/> Screener</label><br/>
				<label><input type="checkbox" name="position[<?php the_ID(); ?>][]" value="greeter"  size="10" maxlength="100" <?php if ($_SESSION['volunteer']['position']['greeter']) echo 'checked ' ?>"/> Greeter</label><br/>
				<label><input type="checkbox" name="position[<?php the_ID(); ?>][]" value="interpreter"  size="10" maxlength="100" <?php if ($_SESSION['volunteer']['position']['interpreter']) echo 'checked ' ?>"/> Translator</label><br/>-->
<!--				<input type="submit" value="Next"/>
				<input type="button" class="back" value="Back"/>
			</form>-->
</div>
<?php
	}
	
//	header('Cache-Control: no-cache, must-revalidate');
//	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
//	header('Content-type: application/json');
//	echo json_encode($result); 
	echo $result;?>

<?php
	exit;
} else {
?>

<div id="main" class="list">
	<div class="section-head">
		<h1><?php _e( 'Tax Sites', APP_TD ); ?></h1>
	</div>
<?php
$term = va_get_search_query_var( 'ls' );
$county = empty($_GET['county']) ? '' : $_GET['county'];
$city = empty($_GET['city']) ? '' : $_GET['city'];
if (!empty($term) || !empty($county) || !empty($city))
{
if ( $featured = va_get_featured_listings() ) :
	while ( $featured->have_posts() ) : $featured->the_post();
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'featured' ); ?>>
		<div class="featured-head">
			<h3><?php _e( 'Featured', APP_TD ); ?></h3>
		</div>

		<?php get_template_part( 'content-listing' ); ?>
	</article>
<?php
	endwhile;
endif;

if ( $featured || is_page() ) :
	$args = $wp_query->query;

	// The template is loaded as a page, not as an archive
	if ( is_page() )
		$args['post_type'] = VA_LISTING_PTYPE;

	// Don't want to show featured listings a second time
	if ( $featured )
		$args['post__not_in'] = wp_list_pluck( $featured->posts, 'ID' );

	query_posts( $args );
endif;

if ( have_posts() ) : ?>
	
	<?php if ( is_search() ) : ?>
	<article class="listing">
		<h2><?php 
                
                $subheader = 'Tax Sites';             
                if (!empty($term))
                    $subheader .= ' matching "' . $term . '"';
                if (!empty($city))
                    $subheader .= " in $city";                             
                elseif (!empty($county))
                    $subheader .= " in $county County";
                echo $subheader; ?></h2>
	</article>
	<?php endif; ?>
	
<?php 
while ( have_posts() ) {
        the_post(); 
        ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php get_template_part( 'content-listing' ); ?>
	</article>
<?php } ?>

<?php elseif ( !$featured ) : ?>

	<?php if ( is_search() ) : ?>
	<article class="listing">
		<h2>We're sorry, there are no tax sites found with that search criteria.</h2>
	</article>
	<?php elseif ( is_archive() ) : ?>	
	<article class="listing">
		<h2>We're sorry, there are no tax sites found with that search criteria.</h2>
	</article>
	<?php endif; ?>

<?php endif; ?>

<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<nav class="pagination">
		<?php appthemes_pagenavi(); ?>
	</nav>
<?php endif; ?>

<?php } else { // First time you hit the page, prior to submitting a search query ?>
    
    <article class="listing"><p>Search for a Tax Site above.</p></article>
    
<?php } ?>
</div>

<div id="sidebar">
	<?php get_sidebar( app_template_base() ); ?>
</div>
<?php }