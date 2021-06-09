<?php
/**
 * Publisher Earnings
 *
 */
if ( ! class_exists('Foodbakery_Publisher_Earnings') ) {

	class Foodbakery_Publisher_Earnings {

		/**
		 * Start construct Functions
		 */
		public function __construct() {
			add_action('wp_ajax_foodbakery_publisher_earnings', array( $this, 'foodbakery_publisher_earnings_callback' ));
		}

		public function foodbakery_publisher_earnings_callback() {
			global $current_user, $foodbakery_plugin_options;

			$pagi_per_page = isset($foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination']) ? $foodbakery_plugin_options['foodbakery_publisher_dashboard_pagination'] : '';

			if ( true === Foodbakery_Member_Permissions::check_permissions('earnings') ) {
				wp_enqueue_script('foodbakery-restaurant-add');
				$date_range = isset( $_POST['date_range'] ) ? $_POST['date_range'] : '';
				if ( $date_range != '' ) {
					$new_date_range = explode( ',', $date_range );
					$start_date = isset( $new_date_range[0] ) ? str_replace( '/', '-', $new_date_range[0] ) : '';
					$end_date = isset( $new_date_range[1] ) ? str_replace( '/', '-', $new_date_range[1] ) : '';
					$start_date = strtotime( $start_date );
					$end_date = strtotime( $end_date );
				} else {
					$start_date = strtotime( '-1 month' );
					$end_date = time();
				}

				$date_range = date( 'Y/m/d', $start_date ) . ',' . date( 'Y/m/d', $end_date );

				$user_id = $current_user->ID;
				$publisher_id = foodbakery_company_id_form_user_id($user_id);

				if ( empty( $_REQUEST['is_inner_ajax_request'] ) ) :
					$publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);

					// Total Sales
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

					$total_withdrawls = get_post_meta($publisher_id, 'total_withdrawals', true);

					$restaurant_cash_subtracts = get_post_meta($publisher_id, 'total_cash_subtracts', true);

                    $over_total_earnings = is_numeric($over_total_earnings) ? $over_total_earnings : 0;
                    $total_withdrawls = is_numeric($total_withdrawls) ? $total_withdrawls : 0;
                    $restaurant_cash_subtracts = is_numeric($restaurant_cash_subtracts) ? $restaurant_cash_subtracts : 0;

					$over_balance = 0;
					if ($over_total_earnings > 0 && $over_total_earnings > $total_withdrawls ) {
						$over_balance = $over_total_earnings - $total_withdrawls - $restaurant_cash_subtracts;
					}
					?>
<?php
								$days_between = ceil( abs( $end_date - $start_date ) / 86400 );
								$total_earnings = 0;
                                $earning_of_month = 0;
                                $over_total_sale_value = 0;
								for ( $i = 0; $i < $days_between + 1; $i++ ) {

									$start_day = $start_date + ( $i * 86400 );
									$args = array(
										'post_type' => 'foodbakery-trans',
										'post_status' => 'publish',

										'date_query' => array(
											array(
												'year' => date( 'Y', $start_day ),
												'month' => date( 'm', $start_day ),
												'day' => date( 'j', $start_day ),
											),
										),
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
					$monthly_query = new WP_Query($args);
					$monthly_query_found = $monthly_query->posts;
                    $total_earnings_monthly = 0;
					$monthly_earning_total = 0;


					/* start withdrwal of current month*/
                    $args_withdrawl = array(
                        'post_type' => 'withdrawals',
                        'post_status' => 'publish',
                        'year' => date('Y'),
                        'monthnum' => date('m'),
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key' => 'foodbakery_withdrawal_user',
                                'value' => $publisher_id,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'foodbakery_withdrawal_status',
                                'value' => 'approved',
                                'compare' => '=',
                            ),
                        ),
                    );
                    $withdrawl_query = new WP_Query($args_withdrawl);
                    $withdrawl_query_found = $withdrawl_query->posts;

                    $widtdrawl_amount_of_current_month = 0;
                    if ( is_array($withdrawl_query_found) ) {
                        foreach ( $withdrawl_query_found as $withdraw_amount_spec_month ) {
                          $widtdrawl_amount_of_current_month += $withdraw_amount_spec_month->withdrawal_amount;
                        }
                    }
                   /* End withdrwal of current month*/



					if ( is_array($monthly_query_found) ) {
						foreach ( $monthly_query_found as $m_post ) {
							$trans_order_id = get_post_meta($m_post->ID, 'foodbakery_transaction_order_id', true);

							$total_trans_amount = get_post_meta($trans_order_id, 'services_total_price', true);

							$trans_amount = get_post_meta($trans_order_id, 'order_amount_credited', true);


                            /* Earning of this month */
                            $services_total_price = get_post_meta($trans_order_id, 'services_total_price', true);

                            $order_amount_charged       = get_post_meta( $trans_order_id, 'order_amount_charged', true );
                            if(!is_numeric($services_total_price)){
                                $services_total_price = 0;
                            }
                            if(!is_numeric($order_amount_charged)){
                                $order_amount_charged = 0;
                            }
                           $order_amount_credited =  $services_total_price - $order_amount_charged;

                            if( $order_amount_credited != '' ){
                                $order_amount_credited  =  $order_amount_credited;
                            } else {
                                $order_amount_credited       = get_post_meta( $trans_order_id, 'services_total_price', true );
                            }
                            $earning_of_month += $order_amount_credited;

                            /* end Earning of this month */


							if ( $trans_amount == '' ) {
								 $trans_amount = 0;
							}

							$monthly_earning_total += $trans_amount;
                            $over_total_sale_value += $total_trans_amount;
							$over_balance = $monthly_earning_total - $total_withdrawls;
						}
					}


                    /* withdrawl amount */
                    if(!is_numeric($total_withdrawls)){
                        $total_withdrawls = 0;
                    }
                    $remaining_balance_after_withdraw =  $earning_of_month - $widtdrawl_amount_of_current_month;

					$currency_sign = foodbakery_base_currency_sign();
					$total_earnings_monthly += $monthly_earning_total;
                    if(!is_numeric($total_earnings_monthly)){
                        $total_earnings_monthly = 0;
                    }
					$over_balance = $total_earnings_monthly - $total_withdrawls;
								}
                                       ?>
            <ul class="earning-calculation">
			<li><?php esc_html_e('Earnings of this month is:', 'foodbakery'); ?> <strong> <?php echo  foodbakery_get_currency($earning_of_month, true, '', '', false); ?></strong></li>
			<li><?php esc_html_e('Total Withdrawal of current month:', 'foodbakery'); ?> <strong> <?php echo foodbakery_get_currency($widtdrawl_amount_of_current_month, true, '', '', false); ?></strong></li>
                             <li><?php esc_html_e('Remaining balance (After withdrawal of current month):', 'foodbakery'); ?> <strong> <?php echo foodbakery_get_currency($remaining_balance_after_withdraw, true, '', '', false); ?></strong></li>
			<li><?php esc_html_e('Total sale price of this Month:', 'foodbakery'); ?> <strong> <?php echo foodbakery_get_currency($over_total_sale_value, true, '', '', false); ?></strong></li>
			<li><?php esc_html_e('Your Total Withdrawals:', 'foodbakery'); ?><a href="javascript:void(0);" class="user_dashboard_ajax" data-id="foodbakery_publisher_withdrawals" data-queryvar="dashboard=withdrawals"><span>(<?php esc_html_e('View withdrawal history', 'foodbakery'); ?>)</span></a><strong> <?php echo currency_symbol_possitions($total_withdrawls, $currency_sign); ?></strong></li>
		     </ul>

					<div class="element-title right-filters-row">
						<h5><?php esc_html_e('Earnings', 'foodbakery') ?></h5>
						<div class="right-filters row">
							<div class="col-lg-6 col-md-6 col-xs-6 pull-right">
								<div class="input-field">
									<i class="icon-angle-down"></i>
									<input type="text" id="daterange" value="<?php echo esc_html($date_range); ?>" placeholder="<?php echo esc_html__('Select Date Range', 'foodbakery'); ?>"/>
									<input type="hidden" name="date_range" id="date_range" value="<?php echo esc_html($date_range); ?>" />
									<script type="text/javascript">
										jQuery('#daterange').daterangepicker({
											startDate: '<?php echo date( 'd/m/Y', $start_date ); ?>',
											endDate: '<?php echo date( 'd/m/Y', $end_date ); ?>',
											autoUpdateInput: false,
											opens: 'left',
											locale: {
												format: 'DD/MM/YYYY'
											}
										},
										function (start, end) {
											var date_range = start.format('DD/MM/YYYY') + ',' + end.format('DD/MM/YYYY');
											jQuery('#date_range').val( date_range );
											var actionString = "foodbakery_publisher_earnings";
											var pageNum = 1;
											foodbakery_show_loader('.loader-holder');
											var filter_parameters = '&is_inner_ajax_request=1&date_range=' + date_range;
											if (typeof (ajaxRequest) != 'undefined') {
												ajaxRequest.abort();
											}
											ajaxRequest = jQuery.ajax({
												type: "POST",
												url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
												data: 'action=' + actionString + filter_parameters,
												success: function (response) {
												foodbakery_hide_loader();
												jQuery('.earnings-container-inner').html(response);

												}
											});
										});
										jQuery('#daterange').on('cancel.daterangepicker', function (ev, picker) {
											jQuery('#daterange').val('');
											jQuery('#date_range').val('');
											var actionString = "foodbakery_publisher_earnings";
											var pageNum = 1;
											foodbakery_show_loader('.loader-holder');
											var filter_parameters = '&is_inner_ajax_request=1';
											if (typeof (ajaxRequest) != 'undefined') {
												ajaxRequest.abort();
											}
											ajaxRequest = jQuery.ajax({
												type: "POST",
												url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
												data: 'action=' + actionString + filter_parameters,
												success: function (response) {
													foodbakery_hide_loader();
													jQuery('.earnings-container-inner').html(response);
												}
											});
										});
									</script>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<div class="earnings-container-inner">
					<div class="tab-content">

						<div id="menu2" class="responsive-table tab-pane fade in active">
							<ul class="table-generic">
								<li class="order-heading-titles">
									<div><?php esc_html_e('Date', 'foodbakery') ?></div>
									<div><?php esc_html_e('Total Sales', 'foodbakery') ?></div>
									<div><?php esc_html_e('Total Earnings', 'foodbakery') ?></div>
								</li>
								<?php
								$days_between = ceil( abs( $end_date - $start_date ) / 86400 );
								$total_earnings = 0;
                                $total_earnings_ = 0;
								for ( $i = 0; $i < $days_between + 1; $i++ ) {

									$start_day = $start_date + ( $i * 86400 );
									$args = array(
										'post_type' => 'foodbakery-trans',
										'post_status' => 'publish',

										'date_query' => array(
											array(
												'year' => date( 'Y', $start_day ),
												'month' => date( 'm', $start_day ),
												'day' => date( 'j', $start_day ),
											),
										),
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

									$monthly_query = new WP_Query($args);
									$monthly_query_found = $monthly_query->posts;
									$total_earnings_amount = 0;
									$total_sales_amount = 0;
                                    $earning_of_month = 0;
									if ( is_array( $monthly_query_found ) ) {
										foreach ( $monthly_query_found as $m_post ) {
											$trans_order_id = get_post_meta( $m_post->ID, 'foodbakery_transaction_order_id', true );
											$currency_sign = get_post_meta( $trans_order_id, 'foodbakery_currency', true );
											$total_trans_amount = get_post_meta( $trans_order_id, 'services_total_price', true );
											$total_sales_amount += $total_trans_amount;

											$trans_amount = get_post_meta( $trans_order_id, 'order_amount_credited', true );
											if ( $trans_amount == '' ) {
												$trans_amount = $total_trans_amount;
											}
											$total_earnings_amount += $trans_amount;


                                            /* Earning of this month */
                                            $services_total_price = get_post_meta($trans_order_id, 'services_total_price', true);
                                            $order_amount_charged       = get_post_meta( $trans_order_id, 'order_amount_charged', true );
                                            if(!is_numeric($services_total_price)){
                                                $services_total_price = 0;
                                            }
                                            if(!is_numeric($order_amount_charged)){
                                                $order_amount_charged = 0;
                                            }
                                            $order_amount_credited =  $services_total_price - $order_amount_charged;

                                            if( $order_amount_credited != '' ){
                                                $order_amount_credited  =  $order_amount_credited;
                                            } else {
                                                $order_amount_credited       = get_post_meta( $trans_order_id, 'services_total_price', true );
                                            }
                                            $earning_of_month += $order_amount_credited;
                                            /* End Earning of this month */

										}
									}
									$total_earnings += $total_earnings_amount;

									$total_earnings_ += $earning_of_month;
									$currency_sign = foodbakery_base_currency_sign();
									$month_date_show = date_i18n( get_option( 'date_format' ), $start_day );
									?>
									<li class="order-heading-titles">
										<div><?php echo esc_html( $month_date_show ) ?></div>
										<div><?php echo foodbakery_get_currency( $total_sales_amount, true, '', '', false ); ?></div>
										<div><?php echo foodbakery_get_currency( $earning_of_month, true, '', '', false ); ?></div>
									</li>
									<?php
								}
								wp_reset_postdata();
								?>
								<li class="order-heading-titles">
									<div>&nbsp;</div>
									<div><b><?php echo __( 'Total Earnings:', 'foodbakery' ); ?></b></div>
									<div><b><?php echo foodbakery_get_currency( $total_earnings_, true, '', '', false ); ?></b></div>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<?php
			}
			die();
		}
	}

	global $publisher_earnings;
	$publisher_earnings = new Foodbakery_Publisher_Earnings();
}
