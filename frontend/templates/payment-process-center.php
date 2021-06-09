<?php
if (!function_exists('foodbakery_is_package_order')) {

    /**
     * checking package order
     * @return boolean
     */
    function foodbakery_is_package_order($id = '') {
        $package_order = get_post($id);
        if (isset($package_order->post_type) && $package_order->post_type == 'package-orders') {
            return true;
        }
        return false;
    }

}

if (!function_exists('foodbakery_payment_summary_fields')) {

    /**
     * Payment Summary fields
     * @return html
     */
    function foodbakery_payment_summary_fields() {

        global $current_user;

        $user_info = get_user_info_array();
        $author_id = $current_user->ID;
        $foodbakery_user_beel = get_the_author_meta('foodbakery_user_beel', $author_id);
        $foodbakery_user_floor = get_the_author_meta('foodbakery_user_floor', $author_id);

        $html = '
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="payment-summary-fields">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="field-holder">
							<label>' . esc_html__('First Name', 'foodbakery') . '</label>
							<input type="text" readonly class="foodbakery-dev-req-field" name="trans_first_name" placeholder="' . esc_html__('First Name', 'foodbakery') . '" value="' . $user_info['first_name'] . '">
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="field-holder">
							<label>' . esc_html__('Last Name', 'foodbakery') . '</label>
							<input type="text"  readonly  class="foodbakery-dev-req-field" name="trans_last_name" placeholder="' . esc_html__('Last Name', 'foodbakery') . '" value="' . $user_info['last_name'] . '">
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="field-holder">
							<label>' . esc_html__('Email', 'foodbakery') . '</label>
							<input type="text"  readonly  class="foodbakery-dev-req-field foodbakery-email-field" name="trans_email" placeholder="' . esc_html__('Email', 'foodbakery') . '" value="' . $user_info['email'] . '">
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="field-holder">
							<label>' . esc_html__('Phone Number', 'foodbakery') . '</label>
							<input type="text"  readonly  class="foodbakery-dev-req-field foodbakery-number-field" name="trans_phone_number" placeholder="' . esc_html__('Phone Number', 'foodbakery') . '" value="' . $user_info['phone_number'] . '">
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="field-holder">
							<label>' . esc_html__('Address', 'foodbakery') . '</label>
							<textarea  readonly  class="foodbakery-dev-req-field" name="trans_address">' . $user_info['address'] . '</textarea>
						</div>
					</div>
                     <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>' . esc_html__('Κουδούνι', 'foodbakery') . '</label>
                            <input readonly type="text" class="foodbakery-dev-req-field" name="trans_beel" placeholder="' . esc_html__('Κουδούνι', 'foodbakery') . '" value="' . $foodbakery_user_beel . '">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>' . esc_html__('Όροφος', 'foodbakery') . '</label>
                            <input  readonly type="text" class="foodbakery-dev-req-field" name="trans_floor" placeholder="' . esc_html__('Όροφος', 'foodbakery') . '" value="' . $foodbakery_user_floor . '">
                        </div>
                    </div>
				</div>
			</div>
		</div>';

        return apply_filters('foodbakery_payment_summary_fields', $html);
        // usage :: add_filter('foodbakery_payment_summary_fields', 'my_callback_function', 10, 1);
    }

}

if (!function_exists('foodbakery_payment_gateways')) {

    /**
     * Load Payment Gateways
     * @return markup
     */
    function foodbakery_payment_gateways($trans_fields = array()) {

        global $foodbakery_plugin_options, $gateways, $foodbakery_form_fields, $current_user;

        $foodbakery_vat_switch = isset($foodbakery_plugin_options['foodbakery_vat_switch']) ? $foodbakery_plugin_options['foodbakery_vat_switch'] : '';
        $foodbakery_payment_vat = isset($foodbakery_plugin_options['foodbakery_payment_vat']) ? $foodbakery_plugin_options['foodbakery_payment_vat'] : '';

        $html = '';
        $payments_settings = new FOODBAKERY_PAYMENTS();

        wp_enqueue_script('foodbakery-restaurant-add');

        // Payment Process
        // when this form submit
        $buy_order_action = foodbakery_get_input('foodbakery_buy_order_flag', 0);
        $get_action = foodbakery_get_input('action', '');
        $get_trans_id = foodbakery_get_input('trans_id', 0);

        $order_type = get_post_meta($get_trans_id, 'foodbakery_order_type', true);
        $restaurant_id = get_post_meta($get_trans_id, 'foodbakery_restaurant_id', true);
        $order_menu_list = get_post_meta($get_trans_id, 'menu_items_list', true);

        $transaction_detail_bank = 'false';
        if ($buy_order_action == '1') {
            if ($get_action == 'restaurant-package' && foodbakery_is_package_order($get_trans_id)) {

                $trans_user_id = get_post_meta($get_trans_id, 'foodbakery_transaction_user', true);
                $foodbakery_trans_pkg = get_post_meta($get_trans_id, 'foodbakery_transaction_package', true);
                $foodbakery_trans_amount = get_post_meta($get_trans_id, 'foodbakery_transaction_amount', true);

                $foodbakery_trans_pay_method = foodbakery_get_input('foodbakery_restaurant_gateway', '', 'STRING');

                $foodbakery_trans_array = array(
                    'transaction_id' => $get_trans_id, // order id
                    'transaction_user' => $trans_user_id,
                    'transaction_package' => $foodbakery_trans_pkg,
                    'transaction_amount' => $foodbakery_trans_amount,
                    'transaction_order_type' => 'package-order',
                    'transaction_pay_method' => $foodbakery_trans_pay_method,
                    'transaction_return_url' => isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? get_permalink($foodbakery_plugin_options['foodbakery_publisher_dashboard']) . '?response=order-completed' : site_url(),
                );
                $transaction_detail = foodbakery_payment_process($foodbakery_trans_array);

                if ($transaction_detail) {
                    echo force_balance_tags($transaction_detail);
                    $transaction_detail_bank = 'true';
                }
            }

            // Order transaction
            if ($get_action == 'reservation-order') {
                $foodbakery_order_service = '';
                $trans_order_user_company_id = get_post_meta($get_trans_id, 'foodbakery_order_user', true);
                $foodbakery_service_title = get_post_meta($get_trans_id, 'service_title', true);

                if (is_array($foodbakery_service_title) && !empty($foodbakery_service_title)) {
                    $foodbakery_order_service = implode(' ', $foodbakery_service_title);
                }

                $foodbakery_trans_amount = get_post_meta($get_trans_id, 'services_total_price', true);
                $foodbakery_trans_pay_method = foodbakery_get_input('foodbakery_restaurant_gateway', '', 'STRING');

                $foodbakery_trans_array = array(
                    'transaction_id' => $get_trans_id, // order id
                    'transaction_user' => $trans_order_user_company_id,
                    'transaction_package' => $foodbakery_order_service,
                    'transaction_amount' => $foodbakery_trans_amount,
                    'transaction_order_type' => 'reservation-order',
                    'restaurant_id' => $restaurant_id,
                    'transaction_pay_method' => $foodbakery_trans_pay_method,
                    'transaction_return_url' => isset($foodbakery_plugin_options['foodbakery_publisher_dashboard']) ? get_permalink($foodbakery_plugin_options['foodbakery_publisher_dashboard']) . '?response=order-completed' : site_url(),
                );

                $transaction_detail = foodbakery_payment_process($foodbakery_trans_array);

                if ($transaction_detail) {
                    echo force_balance_tags($transaction_detail);
                }
            }
            if (!empty($transaction_detail)) {
                return;
            }
        }

        $user_id = $current_user->ID;
        $publisher_id = foodbakery_company_id_form_user_id($user_id);
        $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);

        // Payment Gateways
        $payment_gw_list = '';
        $foodbakery_gateway_options = $foodbakery_plugin_options;
        $gw_counter = 1;
        $rand_id = rand(10000000, 99999999);
        if (is_array($gateways) && sizeof($gateways) > 0) {

            $payment_gw_list .= '
			<div class="section-content col-lg-8 col-md-8 col-sm-12 col-xs-12">';

            $view_menu_order = true;
            if (!is_user_logged_in() && $get_action == 'reservation-order') {
                $view_menu_order = false;
            }
            if (is_user_logged_in() && $publisher_type != 'buyer' && $get_action == 'reservation-order') {
                ob_start();
                ?>
                <div class="restricted-message">
                    <div class="media-holder">
                        <figure>
                            <img src="<?php echo wp_foodbakery::plugin_url() . 'assets/frontend/images/access-restricted-icon-img.png'; ?>" alt="<?php esc_html_e('Access Restricted', 'foodbakery'); ?>">
                        </figure>
                    </div>
                    <div class="text-holder">
                        <strong><?php esc_html_e('Access Restricted', 'foodbakery'); ?></strong>
                        <span><?php esc_html_e('Only customers can order to this, you cannot order to your own restaurant.', 'foodbakery'); ?></span>
                    </div>
                </div>
                <?php
                $payment_gw_list .= ob_get_clean();
            } else if (!is_user_logged_in() && $get_action == 'reservation-order') {
                ob_start();
                echo do_shortcode('[foodbakery_register register_role="foodbakery_publisher" register_type="buyer"]');
                $payment_gw_list .= ob_get_clean();
            } else if ($view_menu_order) {
                if ($transaction_detail_bank != 'true') {
                    $payment_gw_list .= '
					<div class="reservation-form packages-form">
					<form class="foodbakery-dev-payment-form" data-id="' . $rand_id . '" method="post">
					<div class="row">' . foodbakery_payment_summary_fields();
                    if (isset($_REQUEST['payment_mode']) != 'cash') {
                        if (!isset($foodbakery_gateway_options['foodbakery_use_woocommerce_gateway']) || $foodbakery_gateway_options['foodbakery_use_woocommerce_gateway'] != 'on') {
                            $payment_gw_list .= '
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
							<div class="element-title">
								<h4>' . esc_html__('Payment Methods', 'foodbakery') . '</h4>
								<span class="element-slogan">(' . esc_html__('Click one of the options below', 'foodbakery') . ')</span>
							</div>
						</div>';
                        }
                    }
                    $payment_gw_list .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="field-holder">
					<div class="payment-holder">
					<div class="payment-section">
					<ul class="payment-list row">';

                    $cash_trans = false;
                    if (isset($_GET['payment_mode']) && $_GET['payment_mode'] == 'cash') {
                        $cash_trans = true;
                    }

                    if (isset($foodbakery_gateway_options['foodbakery_use_woocommerce_gateway']) && $foodbakery_gateway_options['foodbakery_use_woocommerce_gateway'] == 'on' && false === $cash_trans) {


                        $payment_gw_list .= '
						<li class="col-lg-3 col-md-3 col-sm-3 col-xs-12 hidden">
							<div class="payment-box">
								<input type="radio" id="FOODBAKERY_WOOCOMMERCE_GATEWAY" checked="checked" name="foodbakery_restaurant_gateway" value="FOODBAKERY_WOOCOMMERCE_GATEWAY">
								<label for="FOODBAKERY_WOOCOMMERCE_GATEWAY"></label>
								<span>' . sprintf(esc_html__('Pay with %s', 'foodbakery'), "Woocommerce") . '</span>
							</div>
						</li>';
                        $gw_counter++;
                    } else {

                        if (false === $cash_trans) {
                            foreach ($gateways as $key => $value) {
                                //if ( $key == 'FOODBAKERY_PRE_BANK_TRANSFER' && $get_action == 'reservation-order' ) {
                                //continue;
                                //}
                                $status = $foodbakery_gateway_options[strtolower($key) . '_status'];
                                if (isset($status) && $status == 'on') {
                                    $rand_counter = rand(1000000, 9999999);
                                    $logo = '';
                                    if (isset($foodbakery_gateway_options[strtolower($key) . '_logo'])) {
                                        $logo = $foodbakery_gateway_options[strtolower($key) . '_logo'];
                                    }
                                    if (isset($logo) && $logo != '') {
                                        if ($logo > 0) {
                                            $logo = wp_get_attachment_url($logo);
                                            if ($logo == '') {
                                                if ($key == 'FOODBAKERY_PAYPAL_GATEWAY') {
                                                    $logo = wp_foodbakery::plugin_url() . 'payments/images/paypal.png';
                                                } else if ($key == 'FOODBAKERY_AUTHORIZEDOTNET_GATEWAY') {
                                                    $logo = wp_foodbakery::plugin_url() . 'payments/images/athorizedotnet_.png';
                                                } else if ($key == 'FOODBAKERY_PRE_BANK_TRANSFER') {
                                                    $logo = wp_foodbakery::plugin_url() . 'payments/images/bank.png';
                                                } else {
                                                    $logo = wp_foodbakery::plugin_url() . 'payments/images/skrill.png';
                                                }
                                            }
                                        } else {
                                            $logo = $logo;
                                        }
                                        $gateway_name = isset($gateways[$key]) ? $gateways[$key] : '';
                                        $payment_gw_list .= '
										<li class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
											<div class="payment-box">
												<input type="radio" id="' . strtolower($key) . '_' . $rand_counter . '"' . ($gw_counter == 1 ? ' checked="checked"' : '') . ' name="foodbakery_restaurant_gateway" value="' . $key . '">
												<label for="' . strtolower($key) . '_' . $rand_counter . '"><img alt="" src="' . esc_url($logo) . '"></label>
												<span>' . sprintf(esc_html__('Pay with %s', 'foodbakery'), $gateway_name) . '</span>
											</div>
										</li>';
                                    }
                                    $gw_counter++;
                                }
                            }
                        } else {
                            $payment_gw_list .= '
							<li style="display: none;"><input type="hidden" name="foodbakery_restaurant_gateway" value="cash"></li>';
                        }
                    }
                    $payment_gw_list .= '</ul></div>';
                    if (isset($trans_fields['back_button']) && $trans_fields['back_button'] == true) {
                        $payment_gw_list .= '<input class="previous back-bg-color" type="submit" value="' . esc_html__('Back', 'foodbakery') . '" id="btn-back-payment-information">&nbsp;';
                        $payment_gw_list .= '<button id="register-restaurant-order" class="submit bgcolor btn-submit" type="submit">' . esc_html__('Submit Order', 'foodbakery') . '</button>';
                    } else {
                        $payment_gw_list .= '<div class="edit_btn_profile"><a class="btn btn_success" href="' . site_url() . '/user-dashboard/?dashboard=account' . '">' . esc_html__('Επεξεργασία Πληροφοριών', 'foodbakery') . '</a></div>';
                        $payment_gw_list .= '<button id="register-restaurant-order" class="submit btn-submit" type="submit">' . esc_html__('Submit Order', 'foodbakery') . '</button>';
                    }
                    $payment_gw_list .= '
					<input type="hidden" name="foodbakery_buy_order_flag" value="1">
					<input type="hidden" name="trans_id" value="' . $get_trans_id . '">
					</div>
					</div>
					</div>
					</div>
					</form>
					</div>';
                }
            } else {
                $payment_gw_list .= '
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<p>' . esc_html__('You have to login first to place order.', 'foodbakery') . '</p>
					</div>
				</div>';
            }

            $payment_gw_list .= '
			</div>';

            $payment_gw_list .= '
			<div class="section-sidebar col-lg-4 col-md-4 col-sm-12 col-xs-12">';

            if (isset($trans_fields['order_type']) && $trans_fields['order_type'] == 'restaurant-package') {

                $foodbakery_trans_pkg = get_post_meta($get_trans_id, 'foodbakery_transaction_package', true);
                $package_detail = get_post_meta($foodbakery_trans_pkg, 'foodbakery_package_data', true);

                $foodbakery_trans_amount = get_post_meta($get_trans_id, 'foodbakery_transaction_amount', true);
                $payment_gw_list .= '<div class="user-order-holder">
					<div class="user-order">
						<h6><i class="icon-shopping-basket"></i>' . esc_html__('Your Order ', 'foodbakery') . ' </h6><ul class="categories-order">';

                $payment_gw_list .= '<a>' . get_the_title($foodbakery_trans_pkg) . '</a>
							<span class="category-price">' . foodbakery_get_currency($foodbakery_trans_amount, true, '', '', true) . '</span>';

                $payment_gw_list .= '</li></ul>';
                $payment_gw_list .= '<div class="price-area restaurant-package-data"><ul>';
                $data = ( isset($package_detail['phone_number']['value']) && $package_detail['phone_number']['value'] == 'on' ) ? '<i class="icon-check2"></i>' : '<i class="icon-close2"></i>';
                $payment_gw_list .= '<li>' . __('Listed Phone Number', 'foodbakery') . '<span class="price">' . $data . '</span></li>';

                $data = ( isset($package_detail['website_link']['value']) && $package_detail['website_link']['value'] == 'on' ) ? '<i class="icon-check2"></i>' : '<i class="icon-close2"></i>';
                $payment_gw_list .= '<li>' . __('Website Link ', 'foodbakery') . '<span class="price">' . $data . '</span></li>';

                $data = ( isset($package_detail['number_of_featured_restaurants']['value']) && $package_detail['number_of_featured_restaurants']['value'] == 'on' ) ? '<i class="icon-check2"></i>' : '<i class="icon-close2"></i>';
                $payment_gw_list .= '<li>' . __('Featured Restaurants ', 'foodbakery') . '<span class="price">' . $data . '</span></li>';

                $data = ( isset($package_detail['number_of_top_cat_restaurants']['value']) && $package_detail['number_of_top_cat_restaurants']['value'] == 'on' ) ? '<i class="icon-check2"></i>' : '<i class="icon-close2"></i>';
                $payment_gw_list .= '<li>' . __('Top Categories Restaurants', 'foodbakery') . '<span class="price">' . $data . '</span></li>';

                $data = ( isset($package_detail['social_impressions_reach']['value']) && $package_detail['social_impressions_reach']['value'] == 'on' ) ? '<i class="icon-check2"></i>' : '<i class="icon-close2"></i>';
                $payment_gw_list .= '<li>' . __('Social Impressions Reach', 'foodbakery') . '<span class="price">' . $data . '</span></li>';

                $data = ( isset($package_detail['reviews']['value']) && $package_detail['reviews']['value'] == 'on' ) ? '<i class="icon-check2"></i>' : '<i class="icon-close2"></i>';
                $payment_gw_list .= '<li>' . __('Reviews Allowed', 'foodbakery') . '<span class="price">' . $data . '</span></li>';

                $data = ( isset($package_detail['respond_to_reviews']['value']) && $package_detail['respond_to_reviews']['value'] == 'on' ) ? '<i class="icon-check2"></i>' : '<i class="icon-close2"></i>';
                $payment_gw_list .= '<li>' . __('Can respond to reviews', 'foodbakery') . '<span class="price">' . $data . '</span></li>';

                $payment_gw_list .= '</div></ul>';

                $payment_gw_list .= '</div></div>';
            }


            $menu_order_fee = get_post_meta($get_trans_id, 'menu_order_fee', true);
            $menu_order_fee = foodbakery_get_currency($menu_order_fee, false, '', '', true);
            $menu_order_fee_type = get_post_meta($get_trans_id, 'menu_order_fee_type', true);

            if ($order_type == 'order' && is_array($order_menu_list)) {
                $order_m_total = 0;

                $payment_gw_list .= '
				<div class="user-order-holder">
					<div class="user-order">
						<h6><i class="icon-shopping-basket"></i>' . esc_html__('Your Order ', 'foodbakery') . ' </h6>
						<ul class="categories-order">';
                foreach ($order_menu_list as $_menu_list) {
                    $title_item = isset($_menu_list['title']) ? $_menu_list['title'] : '';
                    $price_item = isset($_menu_list['price']) ? $_menu_list['price'] : '';
                    $extras_item = isset($_menu_list['extras']) ? $_menu_list['extras'] : '';

                    $extras_notes = isset($_menu_list['notes']) ? '<li>' . $_menu_list['notes'] . '</li>' : '';

                    //$order_m_total += floatval($price_item);


                    $sa_category_html = 0;
                    $temp_ex_html = '';

                    $payment_gw_list .= '
						<li>
							<a>' . $title_item . '</a>';

                    if (is_array($extras_item) && sizeof($extras_item) > 0) {
                        $temp_ex_html .= '<ul>';
                        //  $sa_category_html +=floatval($price_item);
                        foreach ($extras_item as $extra_item) {
                            $heading_extra_item = isset($extra_item['heading']) ? $extra_item['heading'] : '';
                            $title_extra_item = isset($extra_item['title']) ? $extra_item['title'] : '';
                            $price_extra_item = isset($extra_item['price']) ? $extra_item['price'] : '';
                             $quantity_extra_item = isset($extra_item['quantity']) ?  $extra_item['quantity'] : '';
                            if ($title_extra_item != '' || $price_extra_item > 0) {
                                $temp_ex_html .= '<li>' . $title_extra_item . ' x '.$quantity_extra_item.' : <span class="category-price">' . foodbakery_get_currency($price_extra_item, true, '', '', true) . '</span></li>';
                            }
                                  $quantity_extra_item = (int) $quantity_extra_item;
                                  
                            $order_m_total += floatval($price_extra_item*$quantity_extra_item);
                            $sa_category_html += floatval($price_extra_item*$quantity_extra_item);
                        }

                        $temp_ex_html .= $extras_notes;

                        $temp_ex_html .= '</ul>';
                    } else {
                        $sa_category_html += floatval($price_item);
                    }


                    $payment_gw_list .= '<span class="category-price">' . foodbakery_get_currency($sa_category_html, true, '', '', true) . '</span>';
                    $payment_gw_list .= $temp_ex_html;

                    $payment_gw_list .= '
						</li>';
                }







                $payment_gw_list .= '
						</ul>';

                if ($order_m_total > 0) {
                    $payment_gw_list .= '
						<div class="price-area">
							<ul>
								<li>' . esc_html__('Subtotal', 'foodbakery') . ' <span class="price">' . foodbakery_get_currency($order_m_total, true, '', '', true) . '</span></li>';

                    //$payment_gw_list .= apply_filters('foodbakery_add_delivery_countytax_list_payment', $order_m_total, $get_trans_id);
                    //$flag_delivery_tax = apply_filters('foodbakery_check_delivery_tax', false);
                    $transaction_id = isset($_REQUEST['trans_id']) ? $_REQUEST['trans_id'] : '';

                    if (isset($flag_delivery_tax)) {
                        $menu_order_fee = $menu_t_price = apply_filters('foodbakery_calculation_of_delivery_taxes', $order_m_total, $transaction_id);
                    } else {
                        $order_m_total = foodbakery_get_currency($order_m_total, false, '', '', true);
                        if ($menu_order_fee_type == 'delivery') {
                            $payment_gw_list .= '<li>' . esc_html__('Delivery fee', 'foodbakery') . ' <span class="price">' . foodbakery_get_currency($menu_order_fee, true, '', '', false) . '</span></li>';
                        } else if ($menu_order_fee_type == 'pickup') {
                            $payment_gw_list .= '<li>' . esc_html__('Pickup fee', 'foodbakery') . ' <span class="price">' . foodbakery_get_currency($menu_order_fee, true, '', '', false) . '</span></li>';
                        }
                        if ($foodbakery_vat_switch == 'on' && $foodbakery_payment_vat > 0) {
                            $payment_gw_list .= '<li>' . sprintf(esc_html__('VAT (%s&#37;)', 'foodbakery'), $foodbakery_payment_vat) . ' <span class="price">' . restaurant_menu_price_calc('defined', $order_m_total, $menu_order_fee, true, true, false, '', true) . '</span></li>';
                        }
                    }
                    $payment_gw_list .= '
							</ul>
							<p class="total-price">' . esc_html__('Total', 'foodbakery') . ' <span class="price">' . restaurant_menu_price_calc('defined', $order_m_total, $menu_order_fee, true, false, false, $transaction_id, true) . '</span></p>
						</div>';
                }
                $payment_gw_list .= '
					</div> 
				</div>';
            } else if ($publisher_type == 'buyer') {
                $get_added_menus = '';
                if (isset($_COOKIE['add_menu_items_temp'])) {
                    $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                }
                ob_start();
                $foodbakery_restaurant_id = $get_trans_id;
                $selected_fee_type = isset($get_added_menus[$foodbakery_restaurant_id . '_fee_type']) ? $get_added_menus[$foodbakery_restaurant_id . '_fee_type'] : '';
                if (isset($get_added_menus[$foodbakery_restaurant_id]) && is_array($get_added_menus[$foodbakery_restaurant_id]) && sizeof($get_added_menus[$foodbakery_restaurant_id]) > 0) {
                    ?>
                    <div class="user-order-holder">
                        <div class="user-order">
                            <h6><i class="icon-shopping-basket"></i> <?php esc_html_e('Your Order', 'foodbakery') ?> </h6>
                            <ul class="categories-order ">
                                <?php
                                $order_m_total = 0;
                                $restaurant_menu_list = get_post_meta($foodbakery_restaurant_id, 'foodbakery_menu_items', true);
                                foreach ($get_added_menus[$foodbakery_restaurant_id] as $menu_ord_item) {

                                    if (isset($menu_ord_item['menu_id']) && isset($menu_ord_item['price'])) {

                                        $rand_numb = rand(10000000, 99999999);

                                        $this_menu_cat_id = isset($menu_ord_item['menu_cat_id']) ? $menu_ord_item['menu_cat_id'] : '';
                                        $this_item_id = $menu_ord_item['menu_id'];
                                        $this_item_price = isset($restaurant_menu_list[$this_item_id]['menu_item_price']) ? $restaurant_menu_list[$this_item_id]['menu_item_price'] : '';
                                        $this_item_extras = isset($menu_ord_item['extras']) ? $menu_ord_item['extras'] : '';

                                        // $order_m_total += floatval($this_item_price);
                                        $this_item_title = isset($restaurant_menu_list[$this_item_id]['menu_item_title']) ? $restaurant_menu_list[$this_item_id]['menu_item_title'] : '';

                                        $menu_extra_li = '';
                                        $sa_catgory_price = 0;
                                        if (is_array($this_item_extras) && sizeof($this_item_extras) > 0) {
                                            $extra_m_counter = 0;
                                            $menu_extra_li .= '<ul>';
                                            foreach ($this_item_extras as $this_item_extra_at) {
                                                $this_item_heading = isset($restaurant_menu_list[$this_item_id]['menu_item_extra']['heading'][$extra_m_counter]) ? $restaurant_menu_list[$this_item_id]['menu_item_extra']['heading'][$extra_m_counter] : '';
                                                $item_extra_at_title = isset($this_item_extra_at['title']) ? $this_item_extra_at['title'] : '';
                                                $item_extra_at_price = isset($this_item_extra_at['price']) ? $this_item_extra_at['price'] : '';
                                                if ($item_extra_at_title != '' || $item_extra_at_price > 0) {
                                                    $menu_extra_li .= '<li>' . $this_item_heading . ' - ' . $item_extra_at_title . ' : ' . foodbakery_get_currency($item_extra_at_price, true) . '</li>';
                                                }

                                                $order_m_total += floatval($item_extra_at_price);
                                                $sa_catgory_price += floatval($item_extra_at_price);
                                                $extra_m_counter++;
                                            }
                                            $menu_extra_li .= '</ul>';
                                        }
                                        ?>
                                        <li id="menu-added-<?php echo absint($rand_numb) ?>" data-pr="<?php echo foodbakery_get_currency($order_m_total, false, '', '', false); ?>">
                                            <a><?php echo esc_html($this_item_title) ?></a>
                                            <span class="category-price"><?php echo foodbakery_get_currency($order_m_total, true); ?></span>
                                            <?php echo force_balance_tags($menu_extra_li) ?>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                            <?php
                            if ($order_m_total > 0) {

                                $foodbakery_delivery_fee = get_post_meta($foodbakery_restaurant_id, 'foodbakery_delivery_fee', true);
                                $foodbakery_pickup_fee = get_post_meta($foodbakery_restaurant_id, 'foodbakery_pickup_fee', true);
                                ?>

                                <div class="price-area">
                                    <ul>
                                        <li><?php esc_html_e('Subtotal', 'foodbakery') ?> <span class="price"><?php echo foodbakery_get_currency($order_m_total, true) ?></span></li>

                                        <?php
                                        $show_fee_type = '';
                                        if ($selected_fee_type == 'delivery' && $foodbakery_delivery_fee > 0 && $foodbakery_pickup_fee > 0) {
                                            $show_fee_type = 'delivery';
                                        } else if ($selected_fee_type == 'pickup' && $foodbakery_delivery_fee > 0 && $foodbakery_pickup_fee > 0) {
                                            $show_fee_type = 'pickup';
                                        } else {
                                            if ($foodbakery_delivery_fee > 0) {
                                                $show_fee_type = 'delivery';
                                            } else if ($foodbakery_pickup_fee > 0) {
                                                $show_fee_type = 'pickup';
                                            }
                                        }

                                        if ($show_fee_type == 'delivery') {
                                            ?>
                                            <li class="restaurant-fee-con"><span class="fee-title"><?php esc_html_e('Delivery fee', 'foodbakery') ?></span> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-charges" data-fee="<?php echo foodbakery_get_currency($foodbakery_delivery_fee, false); ?>">' . foodbakery_get_currency($foodbakery_delivery_fee, false, false, '', '', true) . '</em>', foodbakery_get_currency_sign()); ?></span></li>
                                            <?php
                                        } else if ($show_fee_type == 'pickup') {
                                            ?>
                                            <li class="restaurant-fee-con"><span class="fee-title"><?php esc_html_e('Pickup fee', 'foodbakery') ?></span> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-charges" data-fee="<?php echo foodbakery_get_currency($foodbakery_pickup_fee, false) ?>">' . foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', true) . '</em>', foodbakery_get_currency_sign()); ?></span></li>
                                            <?php
                                        }

                                        if ($foodbakery_vat_switch == 'on' && $foodbakery_payment_vat > 0) {
                                            ?>
                                            <li><?php printf(esc_html__('VAT (%s&#37;)', 'foodbakery'), $foodbakery_payment_vat) ?> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-vtax">' . restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, false, true, true) . '</em>', foodbakery_get_currency_sign()); ?></span></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                    <p class="total-price"><?php esc_html_e('Total', 'foodbakery') ?> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-grtotal">' . restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, true, false, true) . '</em>', foodbakery_get_currency_sign()); ?></span></p>
                                </div>
                                <?php
                            }
                            ?>

                        </div>
                    </div>
                    <?php
                }
                $payment_gw_list .= ob_get_clean();
            }
            $payment_gw_list .= '
			</div>';
        }

        if ($payment_gw_list) {

            $html .= '
			<div class="row">
				' . $payment_gw_list . '
			</div>';
        }
        echo force_balance_tags($html);
    }

    add_action('foodbakery_payment_gateways', 'foodbakery_payment_gateways', 10, 1);
}
if (!function_exists('sa_quantity_process')) {

    function sa_quantity_process($data) {

        // print_r($data);
        // $restaurant_menu_list = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);

        $restaurant_id = $data[0]['extras'][0]['restaurant_id'];

        $restaurant_menu_list = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);
        foreach ($data as $key => $value) {


            foreach ($value['extras'] as $key2 => $value2) {



                $menu_item_id = $value2['menu_item_id'];
                $extra_id = $value2['extra_id'];
                $extra_quantity = (int) $value2['quantity'];

                $position_id = (int) $value2['position_id'];

                $old_quantity = $restaurant_menu_list[$menu_item_id]['menu_item_extra'][$position_id]['quantity'][$extra_id];

                if ($old_quantity == '') {
                    
                } else {

                    $old_quantity = (int) $old_quantity;

                    $new_quentity = $old_quantity - $extra_quantity;


                    if ($new_quentity > -1) {
                        $restaurant_menu_list[$menu_item_id]['menu_item_extra'][$position_id]['quantity'][$extra_id] = $new_quentity;
                    }
                }
            }




        }
        update_post_meta($restaurant_id, 'foodbakery_menu_items', $restaurant_menu_list);


    }

}
if (!function_exists('foodbakery_payment_process')) {

    /**
     * Payment Process
     * @return id
     */
    function foodbakery_payment_process($foodbakery_transaction_fields = array()) {
        global $current_user, $foodbakery_plugin_options;
        extract($foodbakery_transaction_fields);
        $user_id = $current_user->ID;
        $foodbakery_vat_switch = isset($foodbakery_plugin_options['foodbakery_vat_switch']) ? $foodbakery_plugin_options['foodbakery_vat_switch'] : '';
        $foodbakery_payment_vat = isset($foodbakery_plugin_options['foodbakery_payment_vat']) ? $foodbakery_plugin_options['foodbakery_payment_vat'] : '';
        $publisher_id = foodbakery_company_id_form_user_id($user_id);

        $transaction_detail = '';
        if (isset($transaction_amount) && isset($transaction_pay_method) && $transaction_amount > 0 && $transaction_pay_method != '') {
            $foodbakery_transaction_fields['foodbakery_order_id'] = isset($transaction_id) ? $transaction_id : 0;
            // update_post_meta($transaction_id, 'foodbakery_order_status', 'processing');
            if (isset($_GET['menu_id']) && $_GET['menu_id'] != '') {

                $restaurant_menu_id = $_GET['menu_id'];

                // update restaurant id in order post
                update_post_meta($transaction_id, 'order_item_id', $restaurant_menu_id);
                //

                $get_added_menus = get_transient('add_menu_items_' . $publisher_id);
                // resetting menu list
                if (isset($get_added_menus[$restaurant_menu_id])) {
                    unset($get_added_menus[$restaurant_menu_id]);
                }
                if (isset($get_added_menus[$restaurant_menu_id . '_fee_type'])) {
                    unset($get_added_menus[$restaurant_menu_id . '_fee_type']);
                }

                set_transient('add_menu_items_' . $publisher_id, $get_added_menus, 60 * 60 * 24 * 30);
                //
            }

            // Add Transaction
            $foodbakery_trans_id = rand(10000000, 99999999);
            $transaction_post = array(
                'post_title' => '#' . $foodbakery_trans_id,
                'post_status' => 'publish',
                'post_type' => 'foodbakery-trans',
                'post_date' => current_time('Y-m-d H:i:s')
            );
            //insert the transaction
            $trans_id = wp_insert_post($transaction_post);

            if ($trans_id) {

                update_post_meta($trans_id, 'foodbakery_currency', foodbakery_base_currency_sign());
                update_post_meta($trans_id, 'foodbakery_currency_obj', foodbakery_get_base_currency());
                $get_trans_first_name = foodbakery_get_input('trans_first_name', '');
                $get_trans_last_name = foodbakery_get_input('trans_last_name', '');
                $get_trans_email = foodbakery_get_input('trans_email', NULL, 'STRING');
                $get_trans_phone_number = foodbakery_get_input('trans_phone_number', '');
                $get_trans_address = foodbakery_get_input('trans_address', NULL, 'STRING');

                $trans_meta_arr = array(
                    'transaction_id' => $trans_id,
                    'transaction_order_id' => $transaction_id,
                    'transaction_amount' => $transaction_amount,
                    'transaction_user' => $transaction_user,
                    'transaction_order_type' => $transaction_order_type,
                    'transaction_pay_method' => $transaction_pay_method,
                    'trans_first_name' => $get_trans_first_name,
                    'trans_last_name' => $get_trans_last_name,
                    'trans_email' => $get_trans_email,
                    'trans_phone_number' => $get_trans_phone_number,
                    'trans_address' => $get_trans_address,
                );

                // updating all fields of transaction
                foreach ($trans_meta_arr as $trans_key => $trans_val) {
                    update_post_meta($trans_id, "foodbakery_{$trans_key}", $trans_val);
                }

                if ($transaction_order_type == 'reservation-order') {
                    $trans_order_id = get_post_meta($trans_id, "foodbakery_transaction_order_id", true);
                    $restaurant_publisher_id = get_post_meta($trans_order_id, "foodbakery_publisher_id", true);
                    update_post_meta($trans_id, "restaurant_publisher_id", $restaurant_publisher_id);

                    $trans_charges_amount = get_post_meta($trans_order_id, 'order_amount_charged', true);

                    if ($trans_charges_amount != '') {
                        $foodbakery_trans_fee_id = rand(10000000, 99999999);
                        $transaction_fee_post = array(
                            'post_title' => '#' . $foodbakery_trans_fee_id,
                            'post_status' => 'publish',
                            'post_type' => 'foodbakery-trans',
                            'post_date' => current_time('Y-m-d H:i:s')
                        );
                        $trans_fee_id = wp_insert_post($transaction_fee_post);

                        $trans_fee_meta_arr = array(
                            'transaction_id' => $trans_fee_id,
                            'transaction_parent_id' => $trans_id,
                            'transaction_order_id' => $trans_order_id,
                            'order_amount_charged' => $trans_charges_amount,
                            'transaction_user' => $restaurant_publisher_id,
                            'transaction_order_type' => 'reservation-order',
                            'transaction_order_charge_type' => 'order-charges',
                            'transaction_pay_method' => $transaction_pay_method,
                            'trans_first_name' => $get_trans_first_name,
                            'trans_last_name' => $get_trans_last_name,
                            'trans_email' => $get_trans_email,
                            'trans_phone_number' => $get_trans_phone_number,
                            'trans_address' => $get_trans_address,
                        );
                        // updating all fields of transaction
                        foreach ($trans_fee_meta_arr as $trans_key => $trans_val) {
                            update_post_meta($trans_fee_id, "foodbakery_{$trans_key}", $trans_val);
                        }
                    }
                }

                $foodbakery_transaction_fields['transaction_id'] = $trans_id;

                // passing item id if any
                $trans_item_id = get_post_meta($transaction_id, 'order_item_id', true);
                $foodbakery_transaction_fields['trans_item_id'] = $trans_item_id;

                // Gateways Process
                if ($transaction_pay_method == 'FOODBAKERY_PAYPAL_GATEWAY' && !empty($foodbakery_transaction_fields)) {
                    $paypal_gateway = new FOODBAKERY_PAYPAL_GATEWAY();
                    $paypal_gateway->foodbakery_proress_request($foodbakery_transaction_fields);

                    update_post_meta($transaction_id, 'foodbakery_order_status', 'processing');
                } else if ($transaction_pay_method == 'FOODBAKERY_AUTHORIZEDOTNET_GATEWAY' && !empty($foodbakery_transaction_fields)) {
                    $authorizedotnet = new FOODBAKERY_AUTHORIZEDOTNET_GATEWAY();
                    $authorizedotnet->foodbakery_proress_request($foodbakery_transaction_fields);

                    update_post_meta($transaction_id, 'foodbakery_order_status', 'processing');
                } else if ($transaction_pay_method == 'FOODBAKERY_SKRILL_GATEWAY' && !empty($foodbakery_transaction_fields)) {
                    $skrill = new FOODBAKERY_SKRILL_GATEWAY();
                    $skrill->foodbakery_proress_request($foodbakery_transaction_fields);

                    update_post_meta($transaction_id, 'foodbakery_order_status', 'processing');
                } else if ($transaction_pay_method == 'FOODBAKERY_PRE_BANK_TRANSFER' && !empty($foodbakery_transaction_fields)) {
                    $banktransfer = new FOODBAKERY_PRE_BANK_TRANSFER();
                    $transaction_detail = $banktransfer->foodbakery_proress_request($foodbakery_transaction_fields);

                    update_post_meta($transaction_id, 'foodbakery_order_status', 'processing');
                } else if ($transaction_pay_method == 'cash' && !empty($foodbakery_transaction_fields)) {

                    $get_added_menus = get_transient('add_menu_items_' . $publisher_id);
                    if (isset($get_added_menus[$restaurant_menu_id])) {
                        unset($get_added_menus[$restaurant_menu_id]);
                    }
                    if (isset($get_added_menus[$restaurant_menu_id . '_fee_type'])) {
                        unset($get_added_menus[$restaurant_menu_id . '_fee_type']);
                    }
                    set_transient('add_menu_items_' . $publisher_id, $get_added_menus, 60 * 60 * 24 * 30);

                    // mail
                    do_action('foodbakery_sent_order_email', $transaction_id);
                    do_action('foodbakery_received_order_email', $transaction_id);

                    $transaction_detail = '
					<div class="activation-tab-message">
						<div class="text-holder">
							<strong>' . esc_html__('Thank You', 'foodbakery') . '</strong>
							<span>' . esc_html__('Your order have been placed . You will pay cash on delivery.', 'foodbakery') . '</span>
						</div>
					</div>';

                    $order_menu_list = get_post_meta($transaction_id, 'menu_items_list', true);

                    //sa_quantity_process($order_menu_list);

                    // print_r($order_menu_list); //sa_edit_order
                    //$restaurant_menu_list = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);
                    // print_r($restaurant_menu_list); 



                    update_post_meta($transaction_id, 'foodbakery_order_status', 'processing');
                } else if ($transaction_pay_method == 'FOODBAKERY_WOOCOMMERCE_GATEWAY' && !empty($foodbakery_transaction_fields)) {

                    //   update_post_meta($transaction_id, 'foodbakery_order_status', 'processing');
                    /*
                     * If payment gateway is woocommerce
                     */
                    global $Payment_Processing;
                    update_post_meta($trans_id, 'foodbakery_order_with', 'woocommerce');

                    $foodbakery_transaction_amount = $foodbakery_transaction_fields['transaction_amount'];
                    /* if ( $foodbakery_vat_switch == 'on' && $foodbakery_payment_vat > 0 ) {
                      $foodbakery_transaction_vat = ($foodbakery_transaction_fields['transaction_amount'] / 100) * $foodbakery_payment_vat;
                      $foodbakery_transaction_amount  = $foodbakery_transaction_amount + $foodbakery_transaction_vat;
                      } */
                    $package_title = get_the_title($foodbakery_transaction_fields['transaction_package']);
                    if ($transaction_order_type == 'reservation-order') {
                        $package_title = get_the_title($restaurant_id);
                    }

                    $payment_args = array(
                        'package_id' => $foodbakery_transaction_fields['transaction_id'],
                        'package_name' => $package_title,
                        'price' => $foodbakery_transaction_amount,
                        'custom_var' => array(
                            'foodbakery_transaction_id' => $trans_id,
                            'restaurant_id' => $trans_item_id,
                            'foodbakery_restaurant_id' => $foodbakery_transaction_fields['transaction_id'],
                            'foodbakery_order_id' => $foodbakery_transaction_fields['foodbakery_order_id'],
                        ),
                    );
                    if (isset($foodbakery_transaction_fields['transaction_return_url'])) {
                        $payment_args['return_url'] = $foodbakery_transaction_fields['transaction_return_url'];
                    }
                    $payment_args['exit'] = true;
                    if (isset($foodbakery_transaction_fields['exit'])) {
                        $payment_args['exit'] = $foodbakery_transaction_fields['exit'];
                    }
                    $Payment_Processing->processing_payment($payment_args);
                }
            }
            //
        }
        return apply_filters('foodbakery_payment_process', $transaction_detail, $foodbakery_transaction_fields);
        // usage :: add_filter('foodbakery_payment_process', 'my_callback_function', 10, 2);
    }

}
