<?php

add_action( 'admin_init', 'va_listing_hide_meta' );
add_action( 'admin_init', 'va_init_category_walker' );
add_action( 'wp_terms_checklist_args', 'va_category_checklist', 10, 2 );

/*
 * Override the 'Walker_Category_Checklist' method, 'start_el', to replace checkboxes with radio buttons
 */
function va_init_category_walker() {

	class VA_Category_Walker extends Walker_Category_Checklist {

		private $inline_edit = FALSE;

		function __construct( $post_id, $taxonomy )  {
			$this->inline_edit = (bool) ! $post_id;
		}

		function start_el( &$output, $category, $depth, $args, $id = 0 ) {

			// disable the cats if there's already one category selected and always, on the quick edit panel
			if ( $args['selected_cats'] || $this->inline_edit )
				$args['disabled'] = va_categories_locked();

			extract($args);
			if ( empty($taxonomy) )
				$taxonomy = 'category';

			if ( $taxonomy == 'category' )
				$name = 'post_category';
			else
				$name = 'tax_input['.$taxonomy.']';

			$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';

			$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>";
			$output .= '<label class="selectit">';
			$output .= '<input value="' . $category->term_id . '" type="radio" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"';
			$output .= checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ';
			$output .= esc_html( apply_filters('the_category', $category->name ));
			$output .= '</label>';
		}
	}

}

/*
 * Hook into 'wp_terms_checklist_args' to override the category Walker and display radios buttons instead of checkboxes
 */
function va_category_checklist( $args, $post_id ) {
	global $post;

	if ( is_admin() && VA_LISTING_PTYPE == $post->post_type ) {
		$args['walker'] = new VA_Category_Walker( $post_id, $args['taxonomy'] );
		$args['checked_ontop'] = FALSE;
	}
	return $args;
}

/*
 * Hides a list of metaboxes
 */
function va_listing_hide_meta(){

	$remove_boxes = array( 'commentstatusdiv', 'commentsdiv', 'postexcerpt', 'revisionsdiv', 'postcustom' );
	foreach( $remove_boxes as $id ){
		remove_meta_box( $id, VA_LISTING_PTYPE, 'normal' );
	}

	remove_meta_box( 'authordiv', VA_LISTING_PTYPE, 'normal');
	add_meta_box('authordiv', __( 'Author', APP_TD ), 'post_author_meta_box', VA_LISTING_PTYPE, 'side', 'low'); // uses core Text Domain

}

class VA_Listing_Location_Meta extends APP_Meta_Box {
	
	public function __construct(){

		if ( isset($_GET['post']) ) {
			if( VA_LISTING_PTYPE != get_post( $_GET['post'] )->post_type ) return;
		} else if ( ( !isset($_GET['post_type']) || ( isset($_GET['post_type']) && VA_LISTING_PTYPE != $_GET['post_type'] ) ) && !isset( $_POST['post_type'] ) ) {
			return;
		} else if ( isset( $_POST['post_type'] ) && VA_LISTING_PTYPE != $_POST['post_type'] ) {
			return;
		}
		
		parent::__construct( 'listing-location', __( 'Location', APP_TD ), VA_LISTING_PTYPE, 'normal', 'default' );

	}

	public function admin_enqueue_scripts() {
		appthemes_enqueue_geo_scripts( 'vantage_map_edit' );
	}

	public function after_form( $post ) {

		echo html( 'input', array(
			'type' => 'button',
			'class' => 'button',
			'value' => __( 'Find on Map', APP_TD ),
			'name' => '_blank',
			'id' => 'listing-find-on-map',
		));

		$coord = appthemes_get_coordinates( $post->ID );

		echo html( 'input', array(
			'type' => 'hidden',
			'value' => esc_attr( $coord->lat ),
			'name' => 'lat',
		));

		echo html( 'input', array(
			'type' => 'hidden',
			'value' => esc_attr( $coord->lng ),
			'name' => 'lng',
		));

		echo html( 'div', array(
			'id' => 'listing-map',
		));

	}

	public function form(){

		return array(
			array(
				'title' => __( 'Address', APP_TD ),
				'type' => 'text',
				'name' => 'address',
				'extra' => array (
					'id' => 'listing-address',
				)
			),
		);

	}

	public function before_save( $data, $post_id ) {

		appthemes_set_coordinates( $post_id, $_POST['lat'], $_POST['lng'] );

		return $data;
	}

}

class VA_Listing_Claimable_Meta extends APP_Meta_Box {
	
	public function __construct(){
		parent::__construct( 'listing-claimable', __( 'Claimable Listing', APP_TD ), VA_LISTING_PTYPE, 'advanced', 'default' );
	}

	public function form(){

		return array(
			array(
				'title' => __( 'Users can claim this listing', APP_TD ),
				'type' => 'checkbox',
				'name' => 'listing_claimable',
				'desc' => __( 'Yes', APP_TD ),
			),
		);

	}

	public function after_form(){
		echo html( 'p', array(
				'class' => 'howto'
			), __( 'Claimable listings will have a link that allows users to claim them. You can enable moderation on claimed listings in settings.', APP_TD ) );
	}

}

class VA_Listing_Reviews_Status_Meta extends APP_Meta_Box {
	
	public function __construct(){
		parent::__construct( 'listing-reviews', __( 'Reviews Status', APP_TD ), VA_LISTING_PTYPE, 'advanced', 'default' );
	}
	
	public function display( $post ) {
		
		$form_fields = $this->form();
		
		$form_data = array('comment_status' => ( $post->comment_status=='open' ? 'open' : '' ) );

		$form = $this->table( $form_fields, $form_data );
		
		echo $form;
		
	}
	
	public function form(){
		return array(
			array(
				'title' => __( 'Enable Reviews to be submitted on this listing?', APP_TD ),
				'type' => 'checkbox',
				'name' => 'comment_status',
				'desc' => __( 'Yes', APP_TD ),
				'value' => 'open',
			),
		);
	}
	
	function save() {
	
	}
	
}

class VA_Listing_Claim_Moderation extends APP_Meta_Box {

	public function __construct(){

		if( !isset( $_GET['post'] ) || get_post_status( $_GET['post'] ) != 'pending-claimed' )
			return;

		parent::__construct( 'listing-claim-moderation', __( 'Moderation Queue', APP_TD ), VA_LISTING_PTYPE, 'side', 'high' );
		
		add_action( 'admin_init', array($this, 'reject_claim'), 10, 1 );
	}

	function display( $post ){

		echo html( 'p', array(), __( 'Someone wants to claim this listing.', APP_TD ) );

		$claimee = get_userdata( get_post_meta( $post->ID, 'claimee', true ) );
		
		echo html( 'p', array(), sprintf( __( '<strong>New Owner:</strong> %s', APP_TD ), html( 'a', array( 'href'=>va_get_the_author_listings_url($claimee->ID), 'target'=>'_blank' ), $claimee->display_name) ) );
		
		echo html( 'p', array(), html( 'a', array('href'=>'mailto: ' . $claimee->user_email, 'target'=>'_blank' ), sprintf( __( 'Email %s', APP_TD ), $claimee->display_name ) ) );

		echo html( 'input', array(
			'type' => 'submit',
			'class' => 'button-primary',
			'value' => __( 'Accept', APP_TD ),
			'name' => 'publish',
			'style' => 'padding-left: 30px; padding-right: 30px; margin-right: 20px; margin-left: 15px;',
		));

		echo html( 'a', array(
			'class' => 'button',
			'style' => 'padding-left: 30px; padding-right: 30px;',
			'href' => $this->get_edit_post_link($post->ID, 'display', array('reject'=>1) ),
		), __( 'Reject', APP_TD ) );

		echo html( 'p', array(
				'class' => 'howto'
			), __( 'Rejecting will return it to being published on the site.', APP_TD ) );

	}
	
	function get_edit_post_link($post_id, $context, $vars) {
		$link = get_edit_post_link($post_id, $context);
		
		if ( !empty( $vars ) && is_array( $vars ) ) {
			$context_and = 'display' == $context ? '&amp;' : '&';
			foreach($vars as $k=>$v)
				$link .= $context_and . $k . '=' . $v;
		}
		
		return $link;
	}
	
	function reject_claim() {		
		if ( !isset( $_GET['reject'] ) ) return;
		
		if( VA_Listing_Claim::reject_claim() ) {
			wp_redirect( $this->get_edit_post_link( $_GET['post'], 'url', array('rejected'=>1) ) );
		}
	}
	
	function rejected_claim_success_notice() {
		echo scb_admin_notice( __( 'You have rejected the claim, and now this listing has been reset to <a href="#listing-claimable">claimable</a>.', APP_TD ) );
	}

}

class VA_Listing_Contact_Meta extends APP_Meta_Box{
	
	public function __construct(){
		parent::__construct( 'listing-contact', __( 'Contact Information', APP_TD ), VA_LISTING_PTYPE, 'normal', 'default' );
	}

	public function form(){

		return array(
			array(
				'title' => __( 'Phone Number', APP_TD ),
				'type' => 'text',
				'name' => 'phone',
			),
			array(
				'title' => __( 'Website', APP_TD ),
				'type' => 'text',
				'name' => 'website',
			),
			array(
				'title' => __( 'Twitter', APP_TD ),
				'type' => 'text',
				'name' => 'twitter',
			),
			array(
				'title' => __( 'Facebook', APP_TD ),
				'type' => 'text',
				'name' => 'facebook',
			),
		);

	}

}

class VA_Listing_Pricing_Meta extends APP_Meta_Box{
	
	public function __construct(){
		parent::__construct( 'listing-pricing', __( 'Pricing Information', APP_TD ), VA_LISTING_PTYPE, 'normal', 'low' );
	}

	public function admin_enqueue_scripts(){
		if( is_admin() ){
			wp_enqueue_style( 'jquery-ui-datepicker', get_template_directory_uri() . '/styles/jqueryui/jquery-ui.css' );
			wp_enqueue_script('jquery-ui-datepicker');
		}
	}

	public function before_display( $form_data, $post ){
		
		$form_data['_blank_listing_start_date'] = $post->post_date;

		if( isset ( $form_data['featured-home_start_date'] ) )
			$form_data['featured-home_start_date'] = date( 'n/j/Y', strtotime($form_data['featured-home_start_date']));

		if( isset ( $form_data['featured-cat_start_date'] ) )
			$form_data['featured-cat_start_date'] = date( 'n/j/Y', strtotime($form_data['featured-cat_start_date']));

		return $form_data;

	}

	public function before_form(){

		?>
		<script type="text/javascript">
			jQuery(function($){
				createExpireHandler( undefined, $("#listing_duration"), $("#_blank_listing_start_date"), $("#_blank_expire_listing"), $ );
				$("#_blank_listing_start_date").parent().parent().parent().hide();

				createExpireHandler( $("#featured-home"), $("#featured-home_duration"), $("#featured-home_start_date"), $("#_blank_expire_featured-home"), $ );
				$( "#featured-home_start_date" ).datepicker();

				createExpireHandler( $("#featured-cat"), $("#featured-cat_duration"), $("#featured-cat_start_date"), $("#_blank_expire_featured-cat"), $ );
				$( "#featured-cat_start_date" ).datepicker();
			});

			function createExpireHandler( enableBox, durationBox, startDateBox, textBox, $ ){

				$(enableBox).change(function(){
					if( $(this).attr("checked") == "checked" && $(startDateBox).val() == "" ){
						$(startDateBox).val( dateToString( new Date ) );
					}
				})

				var checker = function(){
					var string = "";
					if( enableBox === undefined ){
						string = get_expiration_time();
					}
					else if( $(enableBox).attr('checked') !== undefined ){
						string = get_expiration_time();
					}
					update(string);
				}

				var get_expiration_time = function(){
					
					var startDate = $(startDateBox).val();
					if( startDate == "" ){
						startDate = new Date();
					}

					var duration = $(durationBox).val();
					if ( duration == "" ){
						return "";
					}
					
					return getDateString( parseInt( duration, 10 ), startDate );
				}

				var getDateString = function ( duration, start_date){
					if( isNaN(duration) )
						return "";

					if( duration === 0 )
						return "<?php _e( 'Never', APP_TD ); ?>";

					var expireTime = new Date( new Date( start_date ).getTime() + ( ( ( ( duration * 24 ) * 60 ) * 60 ) * 1000 ) );
					return dateToString( expireTime );
				}

				var update = function( string ){
					if( string  != $(textBox).val() ){
						$(textBox).val( string );
					}
				}

				var dateToString = function( date ){
					return ( date.getMonth() + 1 )+ "/" + date.getDate() + "/" + date.getFullYear();
				}

				setInterval( checker, 10 );
			}
		</script>
		<p><?php _e( 'These settings allow you to override the defaults that have been applied to the listings based on the plan the owner chose. They will apply until the listing expires.', APP_TD ); ?></p>
		<?php

	}

	public function form(){

		$output = array(
			 array(
				'title' => __( 'Listing Duration', APP_TD ),
				'type' => 'text',
				'name' => 'listing_duration',
				'extra' => array(
					'size' => '3'
				),
			),
			array(
				'title' => __( 'Listing Start Date', APP_TD ),
				'type' => 'text',
				'name' => '_blank_listing_start_date',
			),
			array(
				'title' => __( 'Expires on', APP_TD ),
				'type' => 'text',
				'name' => '_blank',
				'extra' => array(
					'disabled' => 'disabled',
					'style' => 'background-color: #EEEEEF;',
					'id' => '_blank_expire_listing'
				)
			)
		);

		foreach( array( VA_ITEM_FEATURED_HOME, VA_ITEM_FEATURED_CAT ) as $addon ){

			$enabled = array(
				'title' => APP_Item_Registry::get_title( $addon ),
				'type' => 'checkbox',
				'name' => $addon,
				'desc' => __( 'Yes', APP_TD ),
				"extra" => array(
					"id" => $addon,
				)
			);

			$duration = array(
				'title' => __( 'Duration', APP_TD ),
				'desc' => __( '(0 = Infinite)', APP_TD ),
				'type' => 'text',
				'name' => $addon . '_duration',
				'extra' => array(
					'size' => '3'
				),
			);

			$start = array(
				'title' => __( 'Start Date', APP_TD ),
				'type' => 'text',
				'name' => $addon . '_start_date',
			);

			$expires = array(
				'title' => __( 'Expires on', APP_TD ),
				'type' => 'text',
				'name' => '_blank',
				'extra' => array(
					'disabled' => 'disabled',
					'style' => 'background-color: #EEEEEF;',
					'id' => '_blank_expire_' . $addon,
				)
			);

			$output = array_merge( $output, array( $enabled, $duration, $start, $expires ));

		}

		return $output;

	}

	function before_save( $data, $post_id ){
		global $va_options;

		unset( $data['_blank_listing_start_date'] );
		unset( $data['_blank'] );

		foreach( array( VA_ITEM_FEATURED_HOME, VA_ITEM_FEATURED_CAT ) as $addon ){

			if( $data[$addon.'_start_date'] ){
				$data[$addon.'_start_date'] = date('Y-m-d H:i:s', strtotime( $data[$addon.'_start_date'] ) );
			}

			if( $data[$addon] ){

				if( $data[$addon.'_duration'] !== '0' && empty( $data[$addon.'_duration'] ) ){
					$data[$addon.'_duration'] = $va_options->addons[$addon]['duration'];
				}

				if( empty( $data[$addon.'_start_date'] ) ){
					$data[$addon.'_start_date'] = current_time( 'mysql' );
				}

			}
		}

		return $data;

	}

}

class VA_Listing_Publish_Moderation extends APP_Meta_Box {
	
	public function __construct(){

		if( !isset( $_GET['post'] ) || get_post_status( $_GET['post'] ) != 'pending' )
			return;

		parent::__construct( 'listing-publish-moderation', __( 'Moderation Queue', APP_TD ), VA_LISTING_PTYPE, 'side', 'high' );
	}

	function display( $post ){

		echo html( 'p', array(), __( 'You must approve this listing before it can be published.', APP_TD ) );

		echo html( 'input', array(
			'type' => 'submit',
			'class' => 'button-primary',
			'value' => __( 'Accept', APP_TD ),
			'name' => 'publish',
			'style' => 'padding-left: 30px; padding-right: 30px; margin-right: 20px; margin-left: 15px;',
		));

		echo html( 'a', array(
			'class' => 'button',
			'style' => 'padding-left: 30px; padding-right: 30px;',
			'href' => get_delete_post_link($post->ID),
		), __( 'Reject', APP_TD ) );

		echo html( 'p', array(
				'class' => 'howto'
			), __( 'Rejecting a listing sends it to the trash.', APP_TD ) );

	}

}

class VA_Listing_Gallery_Meta extends APP_Meta_Box {

	private $operation_attach = 'ATTACH';
	private $operation_unattach = 'UNATTACH';

	public function __construct() {
		parent::__construct( 'gallerydiv', __( 'Gallery', APP_TD ), VA_LISTING_PTYPE, 'side', 'low' );

		add_action( 'wp_ajax_va_update_listing_attachment', array( $this, 'ajax_update_listing_attachment' ) );
		add_action( 'wp_ajax_nopriv_va_update_listing_attachment', array ( $this, 'ajax_update_listing_attachment' ) );
		add_action( 'add_attachment', array ( $this, 'update_attachment_meta' ) );

		// add a low priority to make sure the admin scripts are already enqeued before localizing
		add_action('admin_enqueue_scripts', array($this, 'localize'), 9999);
	}

	public function get_post_id() {
		global $post;

		$calling_post_id = 0;
		if ( isset( $_GET['post_id'] ) )
			$calling_post_id = absint( $_GET['post_id'] );
		elseif ( isset( $_POST ) && count( $_POST ) && isset($post->post_parent)) // for async-upload where $_GET['post_id'] isn't set
			$calling_post_id = $post->post_parent;

		return $calling_post_id;
	}

	public function localize() {
			wp_localize_script( 'va-admin-listing-edit', 'VA_admin_i18n', array(
				'ajaxurl' 		 => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' 	 => wp_create_nonce( "listing-{$this->get_post_id()}" ),
				'saving'	 	 => __('Saving...', APP_TD),
				'attached' 		 => __('Image attached', APP_TD),
				'attach_text'	 => __('Attach to Listing', APP_TD),
				'unattach_text'	 => __('Unattach from Listing', APP_TD),
				'error' 		 => __('Error', APP_TD),
				'done' 			 => __('Done', APP_TD),
				'attach'	 	 => $this->operation_attach,
				'unattach'	 	 => $this->operation_unattach,
			) );
	}

	public function display() {
		global $post;

		$this->localize();

		$featured_id = get_post_thumbnail_id( $post->ID );

		$gallery = va_get_listing_attachments( $post->ID, -1, VA_ATTACHMENT_GALLERY, 'ids' );
		$gallery = array_unique( array_merge( $gallery, array( $featured_id ) ) );

		foreach ( $gallery as $image_id ) {
			echo $this->wrap_gallery_image( $image_id, $featured = (bool) ( $image_id == $featured_id ) );
		}

		$upload_iframe_src = esc_url( get_upload_iframe_src('image', $post->ID, 'gallery' ) );
		$link = html( 'a', array (
								'title' => __( 'Gallery Manager', APP_TD ),
								'href'  => '%1$s',
								'id'	=> 'set-post-thumbnail',
								'class' => 'thickbox'
					  ), '%2$s');

		$manage_images = html( 'p', array ( 'class' => 'hide-if-no-js' ), $link );
		echo sprintf( $manage_images, $upload_iframe_src, esc_html__( 'Gallery Manager' ) );

		$info = html( 'p' , array( 'class' => 'listing-gallery-info' ), '%1$s' );
		echo sprintf ( $info, sprintf( __( 'Only the first %1$s %2$s will be visible on the listing' ) , VA_MAX_IMAGES, __ngettext( 'image', 'images', VA_MAX_IMAGES, APP_TD ) ) );
	}

	public function ajax_update_listing_attachment() {

		$post_ID = intval( $_POST['post_id'] );
		if ( isset( $_POST['featured'] ) )	$featured = intval( $_POST['featured'] );
		else $featured = 0;
		if ( !current_user_can( 'edit_post', $post_ID ) )
			wp_die( -1 );
		$thumbnail_id = intval( $_POST['thumbnail_id'] );

		check_ajax_referer( "listing-$post_ID" );

		if ( ! isset( $_POST['operation'] ) || ( $this->operation_attach != $_POST['operation'] && $this->operation_unattach != $_POST['operation'] ) )
			wp_die( 0 );

		$operation = $_POST['operation'];

		if ( $this->operation_attach == $operation ) {
			$gallery = va_get_listing_attachments( $post_ID );
			$post_parent = $post_ID;
			$menu_order = count( $gallery ) + 1;
		} else {
			$post_parent = 0;
			$menu_order = 0;
		}

		// update the attachment
		$updated_post = array();
		$updated_post['ID'] = $thumbnail_id;
		$updated_post['post_parent'] = $post_parent;
		$updated_post['menu_order'] = $menu_order;

		// update the post into the database
		if ( wp_update_post( $updated_post ) ) {
			wp_die( ( $this->operation_attach == $operation ?  $this->wrap_gallery_image( $thumbnail_id, $featured ) : $thumbnail_id ) );
		}
		wp_die( 0 );
	}

	public function wrap_gallery_image( $image_id, $featured = false ) {
		$image  = wp_get_attachment_image( $image_id, 'thumbnail' );
		$image .= html( 'input', array(
									'name'  => 'listing-thumb',
									'type'  => 'hidden',
									'value' => $image_id,
									'class' => ( $featured ? 'featured' : ''),
					   ) );

		$image = html( 'span', array( 'id' => 'listing-thumb-' . $image_id ), $image );

		return $image;
	}

	public function update_attachment_meta( $post_id ) {
		update_post_meta( $post_id, '_va_attachment_type', VA_ATTACHMENT_GALLERY );
	}

}
