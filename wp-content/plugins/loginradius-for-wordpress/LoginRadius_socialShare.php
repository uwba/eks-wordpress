<?php 



  	add_action('wp_enqueue_scripts', 'loginradius_share_output');



		if( $LoginRadius_settings['LoginRadius_shareEnable'] == "1" ){

	

			if($LoginRadius_settings['LoginRadius_sharehome'] || $LoginRadius_settings['LoginRadius_sharepost'] || $LoginRadius_settings['LoginRadius_sharepage'] || $LoginRadius_settings['LoginRadius_shareexcerpt'] || $LoginRadius_settings['LoginRadius_sharearchive'] || $LoginRadius_settings['LoginRadius_sharefeed'])

		

			{

	

				function loginRadiusShareContent($content) 

		

				{

	

				global $LoginRadius_settings;

	

				

	
				if (strpos($LoginRadius_settings['LoginRadius_shareCode'], 'horizontal') !== false) {
					$append = "<div style='margin:0'><b>".ucfirst($LoginRadius_settings['LoginRadius_share_title'])."</b></div><div class='lrsharecontainer'></div>";
				}else{
					$append = "<div class='lrsharecontainer'></div>";
				}
	
	

				if( ( $LoginRadius_settings['LoginRadius_sharehome'] && is_front_page() ) || ( $LoginRadius_settings['LoginRadius_sharepost'] && is_single() ) || ( $LoginRadius_settings['LoginRadius_sharepage'] && is_page() ) || ( $LoginRadius_settings['LoginRadius_shareexcerpt'] && has_excerpt() ) || ( $LoginRadius_settings['LoginRadius_sharearchive'] && is_archive() ) || ( $LoginRadius_settings['LoginRadius_sharefeed'] && is_feed() ) )
				{	

					if($LoginRadius_settings['LoginRadius_sharetop'] && $LoginRadius_settings['LoginRadius_sharebottom'])
					{
						$content = $append.'<br/>'.$content.'<br/>'.$append;
					}
					else{

	

						if($LoginRadius_settings['LoginRadius_sharetop'])

	

						{

	

							$content = $append.$content;

	

						}

	

						elseif($LoginRadius_settings['LoginRadius_sharebottom'])

	

						{

	

							$content = $content.$append;

	

						}

	

					}

	

				}

	

			  return $content;

	

			}

	

			add_filter('the_content', 'loginRadiusShareContent');

	

		}



	}



