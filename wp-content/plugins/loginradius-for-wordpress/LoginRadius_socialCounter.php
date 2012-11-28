<?php 



  	add_action('wp_enqueue_scripts', 'loginradius_counter_output');



		if( $LoginRadius_settings['LoginRadius_counterEnable'] == "1" ){

	

			if($LoginRadius_settings['LoginRadius_counterhome'] || $LoginRadius_settings['LoginRadius_counterpost'] || $LoginRadius_settings['LoginRadius_counterpage'] || $LoginRadius_settings['LoginRadius_counterexcerpt'] || $LoginRadius_settings['LoginRadius_counterarchive'] || $LoginRadius_settings['LoginRadius_counterfeed'])

		

			{

	

				function loginRadiusCounterContent($content) 

		

				{

	

				global $LoginRadius_settings;

	

				

				if (strpos($LoginRadius_settings['LoginRadius_counterCode'], 'isHorizontal = true') !== false) {

					$append = "<div style='margin:0'><b>".ucfirst($LoginRadius_settings['LoginRadius_counter_title'])."</b></div><br/><div class='lrcounter_simplebox'></div>";
				}else{
					$append = "<div class='lrcounter_simplebox'></div>";
				}
	

					

	

				if( ( $LoginRadius_settings['LoginRadius_counterhome'] && is_front_page() ) || ( $LoginRadius_settings['LoginRadius_counterpost'] && is_single() ) || ( $LoginRadius_settings['LoginRadius_counterpage'] && is_page() ) || ( $LoginRadius_settings['LoginRadius_counterexcerpt'] && has_excerpt() ) || ( $LoginRadius_settings['LoginRadius_counterarchive'] && is_archive() ) || ( $LoginRadius_settings['LoginRadius_counterfeed'] && is_feed() ) )

	

				{	

	

				

	

					if($LoginRadius_settings['LoginRadius_countertop'] && $LoginRadius_settings['LoginRadius_counterbottom'])

	

					{

	

						$content = $append.'<br/>'.$content.'<br/>'.$append;

	

					}

	

					else

	

					{

	

						if($LoginRadius_settings['LoginRadius_countertop'])

	

						{

	

							$content = $append.$content;

	

						}

	

						elseif($LoginRadius_settings['LoginRadius_counterbottom'])

	

						{

	

							$content = $content.$append;

	

						}

	

					}

	

				}

	

			  return $content;

	

			}

	

			add_filter('the_content', 'loginRadiusCounterContent');

	

		}



	}



