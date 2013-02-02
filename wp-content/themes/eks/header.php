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
				<?php if(is_post_type_archive('listing') || is_search()){
					if ( true || !is_page_template( 'create-listing.php' ) ) : ?>
				<form method="get" action="<?php bloginfo( 'url' ); ?>">
					<div id="main-search">
						<div class="search-for">
							<label for="search-text">
								<span class="search-title"><?php _e( 'Search For ', APP_TD ); ?></span><span class="search-help"><?php _e( '(e.g. Alameda, Contra Costa, San Francisco)', APP_TD ); ?></span>
							</label>
							<div class="input-cont h39">
								<div class="left h39"></div>
								<div class="mid h39">
									<input type="text" name="ls" id="search-text" class="text" value="<?php va_show_search_query_var( 'ls' ); ?>" />
								</div>
								<div class="right h39"></div>
							</div>
						</div>

						<div class="search-location">
							<label for="search-location">
								<span class="search-title"><?php _e( 'Near ', APP_TD ); ?></span><span class="search-help"><?php _e( '(Zip code or keyword)', APP_TD ); ?></span>
							</label>
							<div class="input-cont h39">
								<div class="left h39"></div>
								<div class="mid h39">
									<input type="text" name="location" id="search-location" class="text" value="<?php va_show_search_query_var( 'location' ); ?>" />
								</div>
								<div class="right h39"></div>
							</div>
						</div>

						<div class="search-button">
							<!-- <input type="image" src="<?php echo get_bloginfo('template_directory'); ?>/images/search.png" value="<?php _e( 'Search', APP_TD ); ?>" /> -->
							<button type="submit" id="search-submit" class="rounded-small"><?php _e( 'Search', APP_TD ); ?></button>
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
				</form>
				<?php endif;  ?>
                <?php } ?>
			</div>
		</div>
	</div>
</div>

<div id="breadcrumbs" class="container">
	<div class="row">
		<?php breadcrumb_trail( array(
			'separator' => '&raquo;',
			'before' => '',
			'show_home' => '<img src="' . get_template_directory_uri() . '/images/breadcrumb-home.png" />',
		) ); ?>
	</div>
</div>

