<form id="add-review-form" action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post">
	<input type="hidden" name="comment_post_ID" value="<?php echo get_the_ID(); ?>" />
	<?php wp_comment_form_unfiltered_html_nonce(); ?>
	<input type="hidden" name="comment_type" value="review" />

	<table>
		<tr>
			<td class="first_col"><label><?php _e( 'Rating', APP_TD ); ?> <span class="label-helper">(<?php _e( 'required', APP_TD ); ?>)</span></label></td>
			<td class="second_col">
				<div id="review-rating"></div>
			</td>
		</tr>
		<tr>
			<td>
				<label for="review_body"><?php _e( 'Review', APP_TD ); ?> <span class="label-helper">(<?php _e( 'required', APP_TD ); ?>)</span></label>
			</td>
			<td>
				<textarea name="comment" id="review_body" class="required"></textarea>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class="submit"><input type="submit" value="<?php esc_attr_e( 'Submit Review', APP_TD ); ?>" /></td>
		</tr>
	</table>
</form>
