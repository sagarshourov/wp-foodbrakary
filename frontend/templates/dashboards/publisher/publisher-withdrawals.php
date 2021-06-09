<?php
/**
 * Publisher Restaurants
 *
 */
if ( ! class_exists('Foodbakery_Publisher_Withdrawals') ) {

	class Foodbakery_Publisher_Withdrawals {

		/**
		 * Start construct Functions
		 */
		public function __construct() {
			add_action('wp_ajax_foodbakery_publisher_withdrawals', array( $this, 'foodbakery_publisher_withdrawals_callback' ));
			add_action('wp_ajax_restaurant_withdrawal_request_send', array( $this, 'restaurant_withdrawal_request_send_callback' ));
		}

		public function foodbakery_publisher_withdrawals_callback() {
			global $current_user, $foodbakery_plugin_options;

			$pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';

			if ( true === Foodbakery_Member_Permissions::check_permissions('withdrawals') ) {
				wp_enqueue_script('foodbakery-restaurant-add');

				$user_id = $current_user->ID;
				$publisher_id = foodbakery_company_id_form_user_id($user_id);

				$over_total_earnings = 0;
				$args = array(
					'post_type' => 'foodbakery-trans',
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'fields' => 'ids',
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key' => 'restaurant_publisher_id',
							'value' => $publisher_id,
							'compare' => '=',
						),
						array(
							'key' => 'foodbakery_transaction_status',
							'value' => 'approved',
							'compare' => '=',
						),
					),
				);
				$over_query = new WP_Query($args);
				$over_query_found = $over_query->posts;
				$over_total_sale = 0;
				$over_total_earnings = 0;
				if ( is_array($over_query_found) ) {
					foreach ( $over_query_found as $m_post ) {
						$trans_order_id = get_post_meta($m_post, 'foodbakery_transaction_order_id', true);

						$total_trans_amount = get_post_meta($trans_order_id, 'services_total_price', true);
						$trans_amount = get_post_meta($trans_order_id, 'order_amount_credited', true);
						if ( $trans_amount == '' ) {
							$trans_amount = $total_trans_amount;
						}
						$over_total_earnings += $trans_amount;
						$over_total_sale += $total_trans_amount;
					}
				}

				$restaurant_withdrawals = get_post_meta($publisher_id, 'total_withdrawals', true);
				$restaurant_cash_subtracts = get_post_meta($publisher_id, 'total_cash_subtracts', true);

                if($restaurant_cash_subtracts == '' || !is_numeric($restaurant_cash_subtracts)){
                    $restaurant_cash_subtracts = 0;
                }

				$restaurant_earnings = $over_total_earnings;
				$restaurant_balance = 0;

				if($restaurant_withdrawals == ''){
                    $restaurant_withdrawals = 0;
                }

				if (is_numeric($restaurant_earnings) && is_numeric($restaurant_withdrawals) && $restaurant_earnings > 0 && $restaurant_earnings > $restaurant_withdrawals ) {
					$restaurant_balance = $restaurant_earnings - $restaurant_withdrawals - $restaurant_cash_subtracts;
				}


				$date_range = isset($_REQUEST['date_range']) ? $_REQUEST['date_range'] : '';

				//
				$args = array(
					'post_type' => 'withdrawals',
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key' => 'foodbakery_withdrawal_user',
							'value' => $publisher_id,
							'compare' => '=',
						),
					),
				);
				if ( $date_range != '' && $date_range != 'undefined' ) {
					$new_date_range = explode(',', $date_range);
					$start_date = isset($new_date_range[0]) ? str_replace('/', '-', $new_date_range[0]) : '';
					$end_date = isset($new_date_range[1]) ? str_replace('/', '-', $new_date_range[1]) : '';
					$args['date_query'] = array(
						'after' => date('Y-m-d', strtotime($start_date)),
						'before' => date('Y-m-d', strtotime($end_date)),
						'inclusive' => true,
					);
				}
				$withdraw_query = new WP_Query($args);

				$total_posts = $withdraw_query->post_count;

				$posts_per_page = $pagi_per_page > 0 ? $pagi_per_page : 10;
				$posts_paged = isset($_REQUEST['page_id_all']) ? $_REQUEST['page_id_all'] : '';

				$args = array(
					'post_type' => 'withdrawals',
					'post_status' => 'publish',
					'posts_per_page' => $posts_per_page,
					'paged' => $posts_paged,
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key' => 'foodbakery_withdrawal_user',
							'value' => $publisher_id,
							'compare' => '=',
						),
					),
				);
				if ( $date_range != '' && $date_range != 'undefined' ) {
					$new_date_range = explode(',', $date_range);
					$start_date = isset($new_date_range[0]) ? str_replace('/', '-', $new_date_range[0]) : '';
					$end_date = isset($new_date_range[1]) ? str_replace('/', '-', $new_date_range[1]) : '';
					$args['date_query'] = array(
						'after' => date('Y-m-d', strtotime($start_date)),
						'before' => date('Y-m-d', strtotime($end_date)),
						'inclusive' => true,
					);
				}
				$withdraw_query = new WP_Query($args);
				?>
				<div class="publisher-withdrawal-form">
					<div class="element-title has-border right-filters-row">
						<h5><?php esc_html_e('Withdrawals', 'foodbakery') ?></h5>

						<div class="right-filters row pull-right">
							<div class="col-lg-6 col-md-6 col-xs-6 text-right">
								<button id="dev-open-withdraw-req-box" class="btn-submit" type="button"><?php esc_html_e('Withdrawal Request', 'foodbakery') ?></button>									
							</div>
							<?php if ( ( ! $withdraw_query->have_posts() && $date_range != '') || $withdraw_query->have_posts() ) { ?>
								<div class="col-lg-6 col-md-6 col-xs-6">
									<div class="input-field">
										<i class="icon-angle-down"></i>
										<input type="text" id="daterange" value="<?php echo ($date_range != 'undefined' ) ? str_replace(',', ' - ', $date_range) : ''; ?>" placeholder="<?php echo esc_html__('Select Date Range', 'foodbakery'); ?>"/>
										<input type="hidden" name="date_range" id="date_range" value="<?php echo ($date_range != 'undefined' ) ? $date_range : ''; ?>" />
										<script type="text/javascript">
											jQuery('#daterange').daterangepicker({
												autoUpdateInput: false,
												opens: 'left',
												locale: {
													format: 'DD/MM/YYYY'
												}
											},
													function (start, end) {
														var date_range = start.format('DD/MM/YYYY') + ',' + end.format('DD/MM/YYYY');
														jQuery('#date_range').val(date_range);
														var actionString = "foodbakery_publisher_withdrawals";
														var pageNum = 1;
														foodbakery_show_loader('.loader-holder');
														var filter_parameters = get_filter_parameters();
														if (typeof (ajaxRequest) != 'undefined') {
															ajaxRequest.abort();
														}
														ajaxRequest = jQuery.ajax({
															type: "POST",
															url: foodbakery_globals.ajax_url,
															data: 'page_id_all=' + pageNum + '&action=' + actionString + filter_parameters,
															success: function (response) {
																foodbakery_hide_loader();
																jQuery('.user-holder').html(response);

															}
														});
													});
											jQuery('#daterange').on('cancel.daterangepicker', function (ev, picker) {
												jQuery('#daterange').val('');
												jQuery('#date_range').val('');
												var actionString = "foodbakery_publisher_withdrawals";
												var pageNum = 1;
												foodbakery_show_loader('.loader-holder');
												var filter_parameters = get_filter_parameters();
												if (typeof (ajaxRequest) != 'undefined') {
													ajaxRequest.abort();
												}
												ajaxRequest = jQuery.ajax({
													type: "POST",
													url: foodbakery_globals.ajax_url,
													data: 'page_id_all=' + pageNum + '&action=' + actionString + filter_parameters,
													success: function (response) {
														foodbakery_hide_loader();
														jQuery('.user-holder').html(response);

													}
												});
											});
										</script>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<div class="row">
						<div class="restaurant-withdraw-box" style="display:none;">
							<?php
							if ( $restaurant_balance > 0 ) {
								?>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="field-holder">
										<label><?php esc_html_e('Amount', 'foodbakery') ?></label>
										<input type="text" id="dev-publisher-withdraw-amount" class="foodbakery-dev-req-field">
									</div>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="field-holder">
										<label><?php esc_html_e('Other Detail', 'foodbakery') ?></label>
										<textarea id="dev-publisher-withdraw-detail"></textarea>
									</div>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="field-holder">
										<button type="button" class="btn-submit" id="dev-send-withdraw-req"><?php esc_html_e('Send Request', 'foodbakery') ?></button>
										<span class="loader-withdraw"></span>
									</div>
								</div>
								<?php
							} else {
								?>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="not-found">
										<i class="icon-error"></i>
										<p><?php esc_html_e('You have not sufficient amount for withdrawal.', 'foodbakery') ?></p>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				</div>
				<?php
				if ( $withdraw_query->have_posts() ) {
					?>
					<div class="responsive-table">
						<ul class="table-generic">
							<li class="order-heading-titles">
								<div><?php esc_html_e('Id', 'foodbakery') ?></div>
								<div><?php esc_html_e('Date', 'foodbakery') ?></div>
								<div><?php esc_html_e('Amount', 'foodbakery') ?></div>
								<div><?php esc_html_e('Status', 'foodbakery') ?></div>
							</li>
							<?php
							while ( $withdraw_query->have_posts() ) : $withdraw_query->the_post();
								$withdrawal_amount = get_post_meta(get_the_ID(), 'withdrawal_amount', true);
								$withdrawal_status = get_post_meta(get_the_ID(), 'foodbakery_withdrawal_status', true);
								$currency_sign = get_post_meta(get_the_ID(), 'foodbakery_currency', true);
								$currency_sign = ( isset($currency_sign) && $currency_sign != '' ) ? $currency_sign : '$';
								$withdrawal_added_to_publisher = get_post_meta(get_the_ID(), 'withdraw_added_to_publisher', true);

								if ( $withdrawal_status == 'approved' ) {
									$withdrawal_status = esc_html__('Approved', 'foodbakery');
								} else if ( $withdrawal_status == 'cancelled' ) {
									$withdrawal_status = esc_html__('Cancelled', 'foodbakery');
								} else {
									$withdrawal_status = esc_html__('Pending', 'foodbakery');
								}
								?>
								<li class="order-heading-titles">
									<div><?php printf(esc_html__('withdrawal-%s', 'foodbakery'), get_the_ID()) ?></div>
									<div><?php echo get_the_date(get_option('date_format')); ?></div>
									<div><?php echo foodbakery_get_currency($withdrawal_amount, true, '', '', false); ?></div>
									<div><?php echo esc_html($withdrawal_status); ?></div>
								</li>
								<?php
							endwhile;
							wp_reset_postdata();
							?>
						</ul>
					</div>
					<?php
					$total_pages = 1;
					if ( $total_posts > 0 && $posts_per_page > 0 && $total_posts > $posts_per_page ) {
						$total_pages = ceil($total_posts / $posts_per_page);

						$foodbakery_dashboard_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard'] : '';
						$foodbakery_dashboard_link = $foodbakery_dashboard_page != '' ? get_permalink($foodbakery_dashboard_page) : '';
						$this_url = $foodbakery_dashboard_link != '' ? add_query_arg(array( 'dashboard' => 'withdrawals' ), $foodbakery_dashboard_link) : '';
						foodbakery_dashboard_pagination($total_pages, $posts_paged, $this_url, 'withdrawals');
					}
				} else {
					?>
					<div class="not-found">
						<i class="icon-error"></i>
						<p>
							<?php esc_html_e('No withdrawls requests found.', 'foodbakery'); ?>
						</p>
					</div>
					<?php
				}
			}
			wp_die();
		}

		public function restaurant_withdrawal_request_send_callback() {
            $current_user = wp_get_current_user();
			$withdraw_amount = isset($_POST['withdraw_amount']) ? $_POST['withdraw_amount'] : '';
			$withdraw_desc = isset($_POST['withdraw_desc']) ? $_POST['withdraw_desc'] : '';

			$user_id = isset($current_user->ID) ? $current_user->ID : 0;
			$publisher_id = foodbakery_company_id_form_user_id($user_id);

			// Total Sales
			$over_total_earnings = 0;
			$args = array(
				'post_type' => 'foodbakery-trans',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'fields' => 'ids',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'restaurant_publisher_id',
						'value' => $publisher_id,
						'compare' => '=',
					),
					array(
						'key' => 'foodbakery_transaction_status',
						'value' => 'approved',
						'compare' => '=',
					),
				),
			);
			$over_query = new WP_Query($args);
			$over_query_found = $over_query->posts;
			$over_total_sale = 0;
			$over_total_earnings = 0;
			if ( is_array($over_query_found) ) {
				foreach ( $over_query_found as $m_post ) {
					$trans_order_id = get_post_meta($m_post, 'foodbakery_transaction_order_id', true);

					$total_trans_amount = get_post_meta($trans_order_id, 'services_total_price', true);
					$trans_amount = get_post_meta($trans_order_id, 'order_amount_credited', true);
					if ( $trans_amount == '' ) {
						$trans_amount = $total_trans_amount;
					}
					$over_total_earnings += $trans_amount;
					$over_total_sale += $total_trans_amount;
				}
			}

			$restaurant_withdrawals = get_post_meta($publisher_id, 'total_withdrawals', true);

			$restaurant_earnings = $over_total_earnings;

			if($restaurant_earnings == '' || !is_numeric($restaurant_earnings)){
                $restaurant_earnings = 0;
            }

			$restaurant_balance = 0;

            $restaurant_earnings = is_numeric($restaurant_earnings) ? $restaurant_earnings : 0;
            $restaurant_withdrawals = is_numeric($restaurant_withdrawals) ? $restaurant_withdrawals : 0;

			if ( $restaurant_earnings > 0 && $restaurant_earnings > $restaurant_withdrawals ) {
				$restaurant_balance = $restaurant_earnings - $restaurant_withdrawals;
			}

			if ( $withdraw_amount > $restaurant_balance ) {
				echo json_encode(array( 'msg' => esc_html__('This amount is greater than your earnings.', 'foodbakery'), 'type' => 'error' ));
				wp_die();
			} else {
				$withdraw_post = array(
					'post_title' => 'withdrawal-' . rand(10000, 99999),
					'post_content' => '',
					'post_status' => 'publish',
					'post_type' => 'withdrawals',
					'post_date' => current_time('Y-m-d H:i:s')
				);
				$withdrawal_id = wp_insert_post($withdraw_post);

				$up_post = array(
					'ID' => $withdrawal_id,
					'post_title' => 'withdrawal-' . $withdrawal_id,
					'post_name' => 'withdrawal-' . $withdrawal_id,
				);
				wp_update_post($up_post);

				update_post_meta($withdrawal_id, 'foodbakery_currency', foodbakery_base_currency_sign());
				update_post_meta($withdrawal_id, 'foodbakery_currency_obj', foodbakery_get_base_currency());
				update_post_meta($withdrawal_id, 'foodbakery_withdrawal_id', $withdrawal_id);
				update_post_meta($withdrawal_id, 'foodbakery_withdrawal_user', $publisher_id);
				update_post_meta($withdrawal_id, 'foodbakery_withdrawal_detail', $withdraw_desc);
				update_post_meta($withdrawal_id, 'withdrawal_amount', $withdraw_amount);
				update_post_meta($withdrawal_id, 'foodbakery_withdrawal_status', 'pending');

				

				echo json_encode(array( 'msg' => esc_html__('Request sent successfully.', 'foodbakery'), 'type' => 'success' ));
				wp_die();
			}
		}

	}

	global $publisher_withdrawals;
	$publisher_withdrawals = new Foodbakery_Publisher_Withdrawals();
}
