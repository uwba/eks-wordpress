<div id="main">
	<div class="section-head">
	      <h1><?php _e( 'Pricing Options', APP_TD ); ?></h1>
	</div>
	<form id="create-listing" method="POST" action="<?php echo va_get_listing_purchase_url( $listing->ID ); ?>">
		<fieldset>
		    <div class="pricing-options">
		    		<div class="plan">
		    			<div class="content">
		    				<div class="title">
		    					<?php echo $plan['title'][0]; ?>
		    				</div>
		    				<div class="description">
	    	    				<?php echo $plan['description'][0]; ?>
	    	    			</div>
	    	    			<div class="featured-options">
	    	    			<?php if( !empty($plan['disable_featured'][0])) { ?>
	    	    				<div class="option-header">
	    	    					<?php _e( 'Featured Listings are not available for this price option.', APP_TD ); ?>
	    	    				</div>		    	    				
	    	    			<?php } else { ?>
	    	    			
	    	    				<div class="option-header">
	    	    					<?php _e( 'Please choose a feature option (none, one or multiple):', APP_TD ); ?>
	    	    				</div>
	    	    				<?php foreach ( array( VA_ITEM_FEATURED_HOME, VA_ITEM_FEATURED_CAT ) as $addon ) : ?>
									<div class="featured-option"><label>
										<?php _va_show_featured_addon( $addon, $listing->ID ); ?>
									</label></div>
								<?php endforeach; ?>
	    	    			
	    	    			<?php } ?>
	    	    			</div>
	    	    		</div>
	    	    		<div class="price-box">
	    	    			<div class="price">
	    	    				<?php echo APP_Currencies::get_price($plan['price'][0]); ?>
	    	    			</div>
	    	    			<div class="duration">
	    	    				<?php printf( _n( 'for <br /> %s day', 'for <br /> %s days', $plan['duration'][0], APP_TD ), $plan['duration'][0] ); ?>
	    	    			</div>
		    				<div class="radio-button">
		    					<label>
		    						<input disabled="disabled" checked="checked" type="radio" />
		    						<?php _e( 'You chose this option', APP_TD ); ?>
		    					</label>
		    				</div>
		    			</div>
		    		</div>
		    </div>
		</fieldset>
		<fieldset>
			<input type="hidden" name="action" value="purchase-listing">
			<input type="hidden" name="ID" value="<?php echo $listing->ID; ?>">
			<div classess="form-field"><input type="submit" value="<?php _e( 'Continue', APP_TD ) ?>" /></div>
		</fieldset>
	</form>
</div>