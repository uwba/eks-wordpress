<?php

	global $va_options;

	$listing = get_queried_object();

	if( _va_needs_publish( $listing ) || _va_is_claimable( $listing->ID ) ){

		$plans = new WP_Query( array( 
			'post_type' => APPTHEMES_PRICE_PLAN_PTYPE, 
			'nopaging' => 1,
			'tax_query' => array(
				array(
					'taxonomy' => VA_LISTING_CATEGORY,
					'field' => 'id',
					'terms' => get_the_listing_category( $listing->ID )->term_id
				)
			)
		));

		$plans_data = array();
		foreach( $plans->posts as $key => $plan){
			$plans_data[$key] = get_post_custom( $plan->ID );
			$plans_data[$key]['post_data'] = $plan;
		} 

		appthemes_load_template( 'purchase-listing-new.php', array(
			'listing' => $listing,
			'plans' => $plans_data
		));

	}else{

		$plan = _va_get_last_plan_info( $listing->ID );
		appthemes_load_template( 'purchase-listing-existing.php', array(
			'listing' => $listing,
			'plan' => $plan
		));

	}

?>

<style type="text/css">
	.plan{
		border: 1px solid #CCC;
		margin: 5px;
		padding: 3px;
		font-size: 13px;
		position: relative;
	}
	
	.plan:after {
		content: "";
		position: absolute;
		top: 106px;
		right: -10px;		
		border-top: 10px solid #000;
	    border-right: 10px solid transparent;
	}

	.plan .content{
		background-color: #EEEEEF;
		padding: 8px;
		min-height: 95px;
	}

	.plan .title{
		font-size: 20px;
		font-weight: bold;
	}

	.plan .description{
		font-style: italic;
		margin-bottom: 10px;
		width: 40em;
	}

	.plan .option-header{
		font-weight: bold;
		margin-bottom: 2px;
	}

	.plan .price-box{
		position: absolute;
		top: 10px;
		right: -10px;
		background-color: white;
		padding: 10px;
		padding-right: 0px;
		border: 1px solid #CCC;
		border-bottom-left-radius: 5px;
		border-top-left-radius: 5px;
	}

	.plan .price-box .price{
		color: #0066CC;
		font-size: 40px;
		float: left;
		margin-right: 5px;
	}
	.plan .price-box .duration{
		margin-top: 4px;
		font-size: 15px;
		float: left;
	}
	.plan .price-box .radio-button{
		background-color: #CCC;
		clear: both;
		padding: 5px;
		padding-right: 20px;
		font-weight: bold;
		border-bottom-left-radius: 5px;
		border-top-left-radius: 5px;
	}

	.plan .price-box .radio-button label{
		font-style: normal;
	}

</style>