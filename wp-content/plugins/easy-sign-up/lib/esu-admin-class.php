<?php
// 
//  esu-admin-class.php
//  easy-sign-up
//  
//  Created by Rew Rixom on 2011-03-29.
// 

if (!class_exists("EsuAdmin")) {
	class EsuAdmin
	{
		function EsuAdmin() //__construct()
		{
			add_action( 'admin_init', array( $this,'esu_admin_init' ) );
			add_action( 'admin_menu', array( $this,'esu_add_pages'  ) );
		}
		
		//add TinyMCE button and register esu stylesheet
		function esu_admin_init() 
		{
			// add TinyMCE button
			$this->esu_list_auctions_buttons();
			if(!isset($_GET['page'])) return;
			$page  = $_GET['page'];
			$esu_page = explode("_", $page);
			if( $esu_page[0] == "esu" )
			{
				wp_register_style( 'esuStylesheet', ESU_URL . 'css/stylesheet.css', false, ESU_VERSION,'all' );
				wp_enqueue_style( 'esuStylesheet' );
			}
		}
		
		//add menu to admin page
		function esu_add_pages()
		{
			add_menu_page(
				ESU_NAME." &rsaquo; ".__('Options Page','esu_lang'),
				ESU_NAME, 'administrator', 
				'esu_options_pg', 
				array( $this,'esu_options_pg'  ),
				( ESU_URL.'images/icon.png' ) 
			);
		} //End easy_sign_up_add_pages()
		
		//create the options page
		function esu_options_pg()
		{
			//options saved message
			$this->esu_options_saved_message();
			$this->esu_options_pg_html();
		}
		
		function esu_options_saved_message($value='easy_sign_up_saved')
		{
			//options saved message
			if (isset($_REQUEST['action']) && $value == $_REQUEST['action'] ) echo '<div id="message" class="updated fade"><p><strong>'.__('Settings saved.','esu_lang').'</strong></p></div>';
		}
		//this is the html for the esu options page
		function esu_options_pg_html()
		{ 
			$e_options = esu_options_array(); //$this->esu_options_array();
			// save plugin's options
			
			if ( isset($_REQUEST['action']) &&  'easy_sign_up_saved' == $_REQUEST['action'] ) {
					foreach ($e_options as $value) {
						$temp_val = ( isset($_REQUEST[ $value['id'] ]) ) ? $_REQUEST[ $value['id'] ] : '' ;		
						update_option( $value['id'], $temp_val );
					}

					foreach ($e_options as $value) {
						if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
			}
			?>
				<div class="wrap"> 
					<?php get_screen_icon(); screen_icon();  ?>
					<h2><?php  echo(ESU_NAME);_e(" Options",'esu_lang'); ?></h2>
					<?php /*START THE FORM WRAPPING DIV*/ ?>
					<div class="metabox-holder  esu-css-f-l esu-ui-2-3">
					<div class="postbox left">
					<form method="post" action="">
						<table class="form-table" id="easy_sign_up_form_table">
								<?php 
								foreach ($e_options as $value) {

									switch ( $value['type'] ) {
										case 'text':
										?>
										<tr valign="top"> 
											<th scope="row"><label for="<?php echo $value['id']; ?>"><?php echo($value['name']); ?></label></th>
											<td>
								        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" class="widefat"
													type="<?php echo $value['type']; ?>" 
													value="<?php 
														if ( get_option( $value['id'] ) != "") { 
															echo get_option( $value['id'] ); 
														} else { 
															echo $value['std']; 
														} ?>" maxlength="<?php echo $value['maxlength']; ?>" size="<?php echo $value['size']; ?>" />
												<?php echo($value['desc']); ?>
											</td>
										</tr>
										<?php
										break;

										case 'wp_editor':
										?>
										<tr valign="top"> 
											<th scope="row"><label for="<?php echo $value['id']; ?>"><?php echo($value['name']); ?></label></th>
											<td>
												<?php
													$esu_wysiwyg_options = array(
															'media_buttons' => 0,
															'teeny' 				=> 1
														);
													$esu_wysiwyg_options = apply_filters('esu_wysiwyg_options_filter', $esu_wysiwyg_options );
													$esu_wysiwyg_options['textarea_name'] = $value['id'];
													$esu_wysiwyg_contents = (get_option($value['id'])!= "") ? stripslashes( get_option($value['id']) ) : $value['std'] ;
													wp_editor(
														$esu_wysiwyg_contents,
														$value['id'],
														$esu_wysiwyg_options
														);
												?>
												<br>
												<?php echo($value['desc']); ?>
											</td>
										</tr>
										<?php
										
										break;

										case 'textarea':
										if(isset($_REQUEST['options']))	$ta_options = $value['options'];
										?>
										<tr valign="top"> 
											<th scope="row"><label for="<?php echo $value['id']; ?>"><?php echo($value['name']); ?></label></th>
											<td>
												<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" 
													class="widefat" 
													cols="<?php echo $value['cols']; ?>" 
													rows="<?php echo $value['rows']; ?>"><?php 
												if( get_option($value['id']) != "") {
														echo(stripslashes(get_option($value['id'])));
													}else{
														echo($value['std']);
												}?></textarea>
												<br>
												<?php echo($value['desc']); ?>
											</td>
										</tr>
										<?php
										break;
										case 'checkbox':
										if(isset($_REQUEST['options']))	$ta_options = $value['options'];
										?>
										<tr valign="top"> 
											<th scope="row"><label for="<?php echo $value['id']; ?>"><?php echo($value['name']); ?></label></th>
											<td>
												<?php
													if( get_option($value['id']) == true) {
															$ischecked = 'checked="checked"';
														}else{
															$ischecked = '';
													}
												?>
												<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="checkbox" value="true" <?php echo($ischecked); ?>>
												<?php echo($value['desc']); ?>
											</td>
										</tr>
										<?php
										break;
										case 'nothing':
										$ta_options = $value['options'];
										?>
										</table>
											<?php echo($value['desc']); ?>
										<table class="form-table">
										<?php
										break;

										default:

										break;
									}
								}
								?>
					      <tr valign="top">
							  <th scope="row">&nbsp;</th>
							  <td>
					            <p class="submit">
					                <input type="hidden" name="action" value="easy_sign_up_saved" />
					                <input class="button-primary" name="save" type="submit" value="<?php _e('Save Your Changes','esu_lang'); ?>" />    
					            </p>
					          </td>
						  </tr>
						</table>
					</form>
				</div>
					
					    <?php /* Making Use of Box */; ?>
							<div id="easy-help" class='postbox'>
								<h3 style="cursor: default;"><?php _e('Making Use of','esu_lang') ?> <?php echo ESU_NAME;?></h3>
								<div style="padding:4px">
							    <?php 
										$making_use_of_txt =	__('<a name="easy_help"></a> <ul> 
							  		<li>Use the following short code in your pages and posts:</li>
							  		<li><code>[easy_sign_up<br>
							  		title="Your Title Here"<br> 
							  		<abbr title="Show First Name and Last Name fields">fnln</abbr>="1"<br>
							  		phone="1"<br>
							  		esu_label="A unique identifier for your form"<br> 
							  		esu_class="your-class-here"]</code>
							  		</li>
							  		<li> 
							  		All the attributes are optional if you don\'t want them just add the short code without it, e.g.: <code>[easy_sign_up]</code></li>
							  		</li>
							  		<li>If you would like to include the name person who signed up in the Thank You Email just paste: 
							  		<code>#fullname#</code> into the Thank You Email text field where you\'d like to see it.</li>
							  		</ul>','esu_lang');
				            echo $making_use_of_txt;
				          ?>
								</div>
							</div>
					  <?php /* END OF Making Use of Box */; ?>
					</div> <?php /*END THE FORM WRAPPING DIV*/ ?>

					<div id="esu_poststuff" class="meta-box-sortables ui-sortable esu-css-f-l esu-ui-1-3">
						<?php if (method_exists(	$this,'esu_pro'	 	 				))  { $this->esu_pro(); 	 				} ?>
						<?php if (method_exists(  $this,'esu_plug'        	))  { $this->esu_plug();       		} ?>
						<?php if (method_exists(	$this,'esu_widget_advert' ))  { $this->esu_widget_advert(); } ?>
						<?php if (method_exists(	$this,'esu_about'  				))  { $this->esu_about(); 				} ?>
					</div>
          <br style="float:none;clear:both;">
			</div>
			<!-- END WRAP -->
    <?php
		}

    //admin header html
    function esu_admin_header_html($esu_admin_pg_title='')
    {
    	$screen_icon = get_screen_icon(); 
      $header_html='
        <div class="wrap">
          '.$screen_icon.'
        		<h2>'.ESU_NAME.$esu_admin_pg_title.'</h2>
        		<!-- START THE CONTENT WRAPPING DIV -->
        		<div class="metabox-holder">
      ';
      return $header_html;
    }
    
    //admin footer html
		function esu_admin_footer_html()
    {
      $footer_html='
        		</div> 
        		<!-- END THE CONTENT WRAPPING DIV -->
        </div>
        <!-- END WRAP -->
      ';
      return $footer_html;
	  }
	  
		//Admin UI widgets/panels
		function esu_plug()
		{ 
			?>
			<!-- UpGrade -->
			<div class="postbox esu-postbox" id="esu_like_plug">
			  <h3 class="hndle"><span><?php _e('Like this Plugin? - Spread the Word!','esu_lang'); ?></span></h3> 
			  <div class="inside">
			  	<p><?php _e('This plugin has cost me many hours of work, if you use it, please:','esu_lang'); ?></p>
			    <ol>
			      <li>
			      	<a href="http://wordpress.org/support/view/plugin-reviews/easy-sign-up">
			      		<?php echo __('Rate the plugin <span title="Five Stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span> on WordPress.org','esu_lang'); ?>
			      	</a>
			      </li>
			      <li>
			      	<a href="http://www.beforesite.com/wordpress-plugins/easy-sign-up/"><?php _e('Blog about it &amp; link to the plugin page','esu_lang'); ?></a>
			      </li>
			      <li>
			      	<a href="http://www.beforesite.com/wordpress-plugins/easy-sign-up-extras/"><?php _e('Upgrade your plugin','esu_lang'); ?></a>
			      </li>
			      <li>
  			    	<strong><a href="http://www.greenvilleweb.us/services/?ref=plugin_services" 
  						title="<?php _e("Need WordPress Design? Themes and Plugins",'esu_lang'); ?>"><?php _e("You can hire me.",'esu_lang'); ?></a></strong>
  					</li>
			    </ol> 
			  </div> 
			</div> 	
			<!-- END DONATE -->
		<?php 
		}

		function esu_about($css_class='postbox  esu-postbox')
		{ 
			?>	
			<!-- ABOUT -->
			<?php $plugname = "Easy Sign Up"; ?>
			<div class="<?php echo $css_class; ?>"> 
			  <h3 class="hndle"><span><?php _e("Do you need help?",'esu_lang'); ?></span></h3> 
			  <div class="inside"> 
			    <ul class="esu-css-p-10"> 
			    	<li>
			    	  <?php _e("If you need support or want to suggest improvements to the plugin please visit the ",'esu_lang'); ?>
			    	  <a href="http://www.beforesite.com/support"><?php _e("plugin's support forum",'esu_lang'); ?></a>
						</li>
			    </ul>
			  </div>
			</div> 
			<!-- END ABOUT -->
		<?php 
		} 

		function esu_widget_advert() // has news feed
		{ 
			?>
			<!-- GreenWebPlug -->
			<div id="easy-feed" class="postbox esu-postbox"> 
			  <h3 class="hndle">
			    <?php _e('Latest News','esu_lang') ?>
				</h3> 
			  <div class="inside"> 
			    <?php 
            $args = array('id'=>'esu_latest_news');
						esu_feeds("http://feeds.feedburner.com/EasySignUpPluginNews",$args);
          ?>
			  </div>
			</div> 
			<!-- END GreenWebPlug -->	
		<?php 
		}

		function esu_pro()
		{
			?>
			<!-- Pro -->
			<div class="postbox esu-postbox" id="easy-pro">
			  <h3 class="hndle"><span><?php echo ESU_NAME;?> <?php _e('Upgrade Options','esu_lang'); ?></span></h3> 
			  <div class="inside">
				<p><?php echo ESU_NAME;?> <?php _e('offers the following add-ons:','esu_lang'); ?></p>
          <?php 
            $args = array(
              'id'=>'esu_extras_feed',
              'ele_class'=>'easy-extras-rss',
              'feed_items'=>5,
              'show_sub_link'=>true,
              'show_content'=>true
            );
            $url = "http://feeds.feedburner.com/EasySignUpExtras";
            esu_feeds($url,$args); 
          ?>
			  </div>
			</div> 	
			<!-- END Pro -->
		<?php 
		}
		// End right col widgets

		//TinyMCE

		/**
		* Create Our Initialization Function
		* for the WP editor
		*/

		function esu_list_auctions_buttons()
		{
			if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
			     return;
			}
			if ( get_user_option('rich_editing') == 'true' && !isset($_REQUEST['page']) ) {
			     	add_filter( 'mce_external_plugins', array($this,'esu_add_TinyMCE_plugin') ); // calls the fun to add a button to the wp-editor
			     	add_filter( 'mce_buttons', array($this,'esu_register_button') );
			}
		}

		/**
		* Register Button for the wysiwyg editor 
		* in the admin add or edit auction area
		* used by esu_list_auctions_buttons()
		*/

		function esu_register_button( $buttons )
		{
		 	array_push( $buttons, "esubutton" );
		 	return $buttons;
		}

		/**
		* Register TinyMCE Plugin
		* used by esu_list_auctions_buttons()
		* Don't move to the load js class 
		*/
		function esu_add_TinyMCE_plugin( $plugin_array )
		{
		  $plugin_array['esubutton'] = ESU_URL . 'js/esu-button.js'; 
		  return $plugin_array;
		}



	} //End Class EasyAdmin

} //End if Class EasyAdmin

// eof