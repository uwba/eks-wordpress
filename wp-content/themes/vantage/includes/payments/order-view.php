<?php
class APP_Order_Summary extends APP_View {

	function condition() {
		return is_singular( APPTHEMES_ORDER_PTYPE );
	}

	function notices() {

		$queried_order = get_queried_object();
		$order = appthemes_get_order( $queried_order->ID );

		$currentuser = wp_get_current_user();
		if ( $order->get_author() != $currentuser->ID ) {
			appthemes_display_notice( 'error', __( 'The given order ID belongs to someone else.', APP_TD ) );
			return;
		}

		// Check if the order is free, if so complete
		if ( $order->get_total() == 0 ) {
			$order->complete();
		}

		// Check if we are canceling
		if( isset( $_GET['cancel'] ) ){
			$order->clear_gateway();
		}

		// Check if POST data for the gateway has been submited
		$gateway = $order->get_gateway();
		if ( !empty( $gateway ) || !empty( $_POST['payment_gateway'] ) ) {

			if ( !empty( $_POST['payment_gateway'] ) ) {
				$order->set_gateway( $_POST['payment_gateway'] );
				$gateway = $_POST['payment_gateway'];
			}

			appthemes_process_gateway( $gateway, $order );

		}
		else {
		
			$items = $order->get_items();
?>
			<div class="section-head">
              <h1><?php _e( 'Order Summary', APP_TD ); ?></h1>
            </div>
            <div class="order-summary">
            	<?php
            		$table = new APP_Order_Summary_Table( $order );
            		$table->show();
            	?>
				<?php if( $order->get_total() > 0 ): ?>
					<form action="<?php echo $order->get_return_url(); ?>" method="POST">
						<p><?php _e( 'Please select a method for processing your payment:', APP_TD ); ?></p>
						<?php appthemes_list_gateway_dropdown(); ?>
						<input type="submit">
					</form>
				<?php else: ?>
					<p><?php _e( 'Your order has been completed.', APP_TD ); ?></p>
					<input type="submit" value="<?php esc_attr_e( 'Continue to Listing', APP_TD ); ?>" onClick="location.href='<?php echo get_permalink( $items[0]['post']->ID ); ?>';return false;">
				<?php endif; ?>
            </div>
<?php
		}
	}
}

class APP_Order_Summary_Table extends APP_Table{

	protected $order, $currency;

	public function __construct( $order ){

		$this->order = $order;
		$this->currency = $order->get_currency();

	}

	public function show( $attributes = array() ){
		echo $this->table( $this->order->get_items(), $attributes );
	}

	protected function footer( $items ){

		$cells = array(
			__( 'Total', APP_TD ),
			APP_Currencies::get_price( $this->order->get_total() )
		);

		return html( 'tr', array(), $this->cells( $cells, 'td' ) );

	}

	protected function row( $item ){

		$cells = array(
			APP_Item_Registry::get_title( $item['type'] ),
			APP_Currencies::get_price( $item['price'], $this->currency )
		);

		return html( 'tr', array(), $this->cells( $cells ) );

	}

}