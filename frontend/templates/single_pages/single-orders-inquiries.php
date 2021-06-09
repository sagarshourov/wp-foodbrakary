<?php
/**
 * The template for displaying single order/inquiry
 *
 */
get_header();
global $foodbakery_order_detail;
?>
<?php
while ( have_posts() ): the_post();
	$order_id = get_the_ID();
	$foodbakery_restaurant_id = get_post_meta( $order_id, 'foodbakery_restaurant_id', true );
	$order_type = get_post_meta( $order_id, 'foodbakery_order_type', true );
	?>
	<div class="menu-order-detail" id="print-order-det-<?php echo esc_html($order_id); ?>">
		<h2><?php _e( 'Order Detail', 'foodbakery' ) ?></h2>
		<div class="order-detail-inner">
			<div class="description-holder">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="list-detail-options has-checkbox">
							<h3>
								<?php echo get_the_title( $foodbakery_restaurant_id ); ?>
							</h3>
							<ul class="order-detail-options">
								<li class="order-number">
									<strong><?php _e( 'Order ID:', 'foodbakery' ) ?></strong>
									<span><?php echo esc_html( $order_id ); ?></span>
								</li>
								<?php $foodbakery_order_detail->order_pick_delivery_time( $order_id ); ?>
								<li class="created-date">
									<strong><?php _e( 'Created:', 'foodbakery' ) ?></strong>
									<span><?php echo get_the_time( get_option( 'date_format' ), $order_id ); ?></span>
								</li>
								<li class="order-type">
									<strong><?php _e( 'Type:', 'foodbakery' ) ?></strong>
									<span><?php echo esc_html( $order_type ); ?></span>
								</li>
								<li class="order-type">
									<strong><?php _e( 'Payment Status:', 'foodbakery' ) ?></strong>
									<span><?php echo esc_html( $foodbakery_order_detail->order_payment_status( $order_id ), 'foodbakery' ); ?></span>
								</li>

							</ul>
						</div>
					</div>
					<?php
					// Order buyer info.
					$foodbakery_order_detail->order_buyer_info( $order_id );
					// Order menu list.
					$foodbakery_order_detail->order_menu_list( $order_id );
					// Order price.
					$foodbakery_order_detail->order_price( $order_id );
					?>
				</div>
			</div>
		</div>
	</div>

<?php endwhile;
?>
<?php get_footer(); ?>
