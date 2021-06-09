<?php

ob_start();

if (!class_exists('Payment_Processing')) {

    class Payment_Processing {

        public function __construct() {
            global $rcv_parameters;
            $rcv_parameters = array();
            $Payment_Processing = '';
            add_action('woocommerce_order_status_cancelled', array($this, 'custom_order_status_cancelled'));
            add_action('woocommerce_thankyou', array($this, 'custom_thankyou_page'));
            add_action('woocommerce_checkout_order_processed', array($this, 'action_woocommerce_new_order'), 1000);
            add_filter('woocommerce_checkout_fields', array($this, 'custom_override_checkout_fields'));
            add_filter('woocommerce_order_status_pending_to_processing', array($this, 'custom_payment_complete'));
            add_action('woocommerce_payment_complete', array($this, 'custom_payment_complete'));
            add_action('woocommerce_order_status_on-hold', array($this, 'custom_payment_complete'));
            add_action('woocommerce_order_status_processing', array($this, 'custom_payment_complete'));
            add_action('woocommerce_coupons_enabled', array($this, 'custom_hide_coupon_field'));
            add_filter('woocommerce_payment_complete_order_status', array($this, 'custom_payment_complete_order_status'), 10, 2);
            add_filter('woocommerce_billing_fields', array($this, 'woocommerce_billing_fields_callback'), 10, 1);
            add_filter('woocommerce_shipping_fields', array($this, 'woocommerce_shipping_fields_callback'), 10, 1);
            //add_filter('woocommerce_currency', array($this, 'woocommerce_currency_callback'), 10, 1);
            add_action('woocommerce_order_items_meta_display', array($this, 'woocommerce_order_items_meta_display_callback'), 10, 2);
            add_filter('woocommerce_cart_calculate_fees', array($this, 'woocommerce_cart_calculate_fees_callback'), 10, 1);
        }

        public function processing_payment($payment_args) {
            global $wpdb, $rcv_parameters, $woocommerce;
            $rcv_parameters = $payment_args;

            extract($payment_args);

            if (isset($transaction_return_url) && $transaction_return_url != '') {
                $return_url = $transaction_return_url;
            }
            if (!isset($return_url) || $return_url == '') {
                $$return_url = site_url();
            }

            $wpdb->query("DELETE " . $wpdb->prefix . "posts
			FROM " . $wpdb->prefix . "posts
			INNER JOIN " . $wpdb->prefix . "postmeta ON " . $wpdb->prefix . "postmeta.post_id = " . $wpdb->prefix . "posts.ID
			WHERE (" . $wpdb->prefix . "postmeta.meta_key = 'referance_ID' AND " . $wpdb->prefix . "postmeta.meta_value = '" . $package_id . "')");

            $post = array(
                'post_author' => 1,
                'post_content' => '',
                'post_status' => "publish",
                'post_title' => $package_name,
                'post_parent' => '',
                'post_type' => "product",
            );

            //Create post
            $post_id = wp_insert_post($post);

            update_post_meta($post_id, '_visibility', 'visible');
            update_post_meta($post_id, '_stock_status', 'instock');
            update_post_meta($post_id, '_regular_price', $price);
            update_post_meta($post_id, 'referance_ID', $package_id);
            update_post_meta($post_id, '_price', $price);
            update_post_meta($post_id, 'rcv_parameters', $payment_args);
            update_post_meta($post_id, '_virtual', 'yes');
            update_post_meta($post_id, 'return_url', $return_url);
            update_post_meta($post_id, '_visibility', 'hidden');

            $woocommerce->cart->empty_cart();
            $woocommerce->cart->add_to_cart($post_id, 1);
            $checkout_url = wc_get_checkout_url();

            echo "<script>window.top.location.href='$checkout_url';</script>";
            if (isset($exit) && $exit == true) {
                exit;
            }
        }

        public function custom_order_status_cancelled($order_id) {
            global $foodbakery_plugin_options;
            $rcv_parameters = get_post_meta($order_id, '_rcv_parameters', true);
            $transaction_id = $rcv_parameters['custom_var']['foodbakery_transaction_id'];
            //$this->update_woocommerce_details_order( $order_id, $transaction_id );
            if (isset($rcv_parameters) && !empty($rcv_parameters)) {
                $_REQUEST['order_id'] = $order_id;
                $_REQUEST['payment_status'] = 'Cancelled';
                $_REQUEST['payment_source'] = 'wooC';
                $redirect_url = add_query_arg($_REQUEST, $foodbakery_plugin_options['foodbakery_dir_paypal_ipn_url']);

                $order = new WC_Order($order_id);
                foreach ($order->get_items() as $item) {
                    wp_delete_post($item['product_id']);
                }
                wp_delete_post($order_id);
                $return_url = get_post_meta($order_id, 'return_url', true);
                if (!isset($return_url) || $return_url == '') {
                    $return_url = site_url();
                }
                wp_redirect($return_url);
            }
        }

        public function custom_thankyou_page($order_id) {
            global $foodbakery_plugin_options;
            $rcv_parameters = get_post_meta($order_id, '_rcv_parameters', true);
            $transaction_id = $rcv_parameters['custom_var']['foodbakery_transaction_id'];
            //$this->update_woocommerce_details_order( $order_id, $transaction_id );
            update_post_meta($transaction_id, 'foodbakery_order_status', 'processing');
            
            
            
            $order_menu_list = get_post_meta($transaction_id, 'menu_items_list', true);
            
            
            

      

            if (isset($rcv_parameters) && !empty($rcv_parameters)) {
                $return_url = isset($rcv_parameters['redirect_url']) ? $rcv_parameters['redirect_url'] : '';
                $order = new WC_Order($order_id);
                $payment_method = get_post_meta($order_id, '_payment_method', true);
                $order_status_array = array(
                    'payment_method' => $payment_method,
                    'order_id' => $order_id,
                    'foodbakery_order_id' => $rcv_parameters['custom_var']['foodbakery_order_id'],
                    'status_code' => 200,
                    'status_message' => esc_html__('Thank you. Your order has been received.', 'foodbakery'),
                );
                $return_url = get_post_meta($order_id, 'return_url', true);
                if (!isset($return_url) || $return_url == '') {
                    $return_url = site_url();
                }
                update_option('custom_order_status_array', $order_status_array);
                wp_redirect($return_url);
            }
        }

        public function action_woocommerce_new_order($order_id) {
            global $woocommerce;
            $order = new WC_Order($order_id);
            foreach ($order->get_items() as $item) {
                $product_id = $item['product_id'];
            }
            $rcv_parameters = get_post_meta($item['product_id'], 'rcv_parameters', true);

            $return_url = get_post_meta($item['product_id'], 'return_url', true);

            $transaction_id = $rcv_parameters['custom_var']['foodbakery_transaction_id'];
            if (isset($rcv_parameters) && !empty($rcv_parameters)) {
                update_post_meta($order_id, '_rcv_parameters', $rcv_parameters);
            }
            if (isset($return_url) && !empty($return_url)) {
                update_post_meta($order_id, 'return_url', $return_url);
            }
            $current_user = wp_get_current_user();

            $this->update_woocommerce_details_order($order_id, $transaction_id);
            $this->foodbakery_commision_charge($order_id, $transaction_id);

            update_post_meta($transaction_id, 'woocommerce_order_id', $order_id);
            update_post_meta($transaction_id, 'foodbakery_transaction_pay_method', get_post_meta($order_id, '_payment_method', true));
            update_post_meta($transaction_id, 'foodbakery_currency', foodbakery_base_currency_sign());
            update_post_meta($transaction_id, 'foodbakery_currency_obj', foodbakery_get_base_currency());
            $user_id = get_current_user_id();
        }

        public function custom_override_checkout_fields($fields) {
            global $woocommerce;
            $items = $woocommerce->cart->get_cart();
            $product_id = 0;
            foreach ($items as $item) {
                $product_id = $item['product_id'];
            }

            $rcv_parameters = get_post_meta($product_id, 'rcv_parameters');


            if (isset($rcv_parameters) && !empty($rcv_parameters)) {
                $fields = array();
            }
            return $fields;
        }

        public function custom_payment_complete($order_id) {

            $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
            $_REQUEST['order_id'] = $order_id;
            $_REQUEST['payment_status'] = 'approved';
            $_REQUEST['payment_source'] = 'FOODBAKERY_WOOCOMMERCE_GATEWAY';
            $redirect_url = $foodbakery_plugin_options['foodbakery_dir_paypal_ipn_url'];
            $redirect_url = add_query_arg($_REQUEST, $redirect_url);
            wp_remote_get($redirect_url);
        }

        public function custom_payment_complete_order_status($order_status, $order_id) {
            if ($order_status == 'processing') {
                $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
                $_REQUEST['order_id'] = $order_id;
                $_REQUEST['payment_status'] = 'approved';
                $_REQUEST['payment_source'] = 'FOODBAKERY_WOOCOMMERCE_GATEWAY';
                $redirect_url = $foodbakery_plugin_options['foodbakery_dir_paypal_ipn_url'];
                $redirect_url = add_query_arg($_REQUEST, $redirect_url);
                wp_remote_get($redirect_url);
            }
            return 'completed';
        }

        public function custom_hide_coupon_field($enabled) {
            if (is_checkout()) {
                $enabled = false;
            }
            return $enabled;
        }

        public function custom_order_status_display() {
            global $woocommerce;
            $return_data = get_option('custom_order_status_array');
            delete_option('custom_order_status_array');

            return $return_data;
        }

        public function remove_raw_data($order_id) {
            if (isset($order_id) && $order_id != '') {
                $order = new WC_Order($order_id);
                foreach ($order->get_items() as $item) {
                    wp_delete_post($item['product_id']);
                }
                //wp_delete_post($order_id);
            }
        }

        public function woocommerce_billing_fields_callback($address_fields) {
            $address_fields['billing_phone']['required'] = false;
            $address_fields['billing_country']['required'] = false;
            $address_fields['billing_first_name']['required'] = false;
            $address_fields['billing_last_name']['required'] = false;
            $address_fields['billing_email']['required'] = false;
            $address_fields['billing_address_1']['required'] = false;
            $address_fields['billing_city']['required'] = false;
            $address_fields['billing_postcode']['required'] = false;
            return $address_fields;
        }

        public function woocommerce_shipping_fields_callback($address_fields) {
            $address_fields['order_comments']['required'] = false;
            return $address_fields;
        }

        public function woocommerce_order_items_meta_display_callback($output, $orderObj) {
            return $output;
        }

        public function woocommerce_currency_callback($currency) {
            return foodbakery_get_base_currency();
        }

        public function update_woocommerce_details_order($order_id, $transaction_id) {
            $order = wc_get_order($order_id);
            $order_all_data = $order->get_order_item_totals();
            $order_data_new = array();
            $i = 0;
            foreach ($order_all_data as $order_key => $order_data) {
                $order_data_new[$order_key]['label'] = strip_tags($order_data['label']);
                $order_data_new[$order_key]['value'] = strip_tags($order_data['value']);
                $i++;
            }
            $total_amount = $this->get_only_number($order_data_new['order_total']['value']);

            unset($order_data_new['cart_subtotal']);
            unset($order_data_new['payment_method']);
            unset($order_data_new['order_total']);

            $transaction_order_id = get_post_meta($transaction_id, 'foodbakery_transaction_order_id', true);
            update_post_meta($transaction_id, 'foodbakery_transaction_amount', $total_amount);
            update_post_meta($transaction_order_id, 'foodbakery_transaction_amount', $total_amount);
            update_post_meta($transaction_order_id, 'services_total_price', $total_amount);
            update_post_meta($transaction_id, 'foodbakery_wooc_order_data', $order_data_new);
            update_post_meta($transaction_order_id, 'foodbakery_wooc_order_data', $order_data_new);

            
        }

        public function get_only_number($string) {
            $string = html_entity_decode($string);
            preg_match_all('/\d.+/', $string, $matches);
            if (isset($matches[0][0])) {
                return $matches[0][0];
            }
        }

        public function foodbakery_commision_charge($order_id, $transaction_id) {

            $transaction_order_id = get_post_meta($transaction_id, 'foodbakery_transaction_order_id', true);
            $restaurant_id = get_post_meta($transaction_order_id, 'foodbakery_restaurant_id', true);

            $total_price = get_post_meta($transaction_order_id, 'services_total_price', true);

            do_action('foodbakery_restaurant_order_commission_frontend', $transaction_order_id, $restaurant_id, $total_price);
        }

        public function woocommerce_cart_calculate_fees_callback($wooccm_custom_user_charge_man) {
            global $woocommerce, $foodbakery_plugin_options;

            $items = $woocommerce->cart->get_cart();

            foreach ($items as $item) {
                $product_id = $item['product_id'];
            }
            $rcv_parameters = get_post_meta($product_id, 'rcv_parameters', true);
            $transaction_id = isset($rcv_parameters['custom_var']['foodbakery_transaction_id']) ? $rcv_parameters['custom_var']['foodbakery_transaction_id'] : '';
            $transaction_order_id = get_post_meta($transaction_id, 'foodbakery_transaction_order_id', true);
            $foodbakery_restaurant_id = get_post_meta($transaction_order_id, 'foodbakery_restaurant_id', true);
            $woocommerce_fee_data = get_post_meta($foodbakery_restaurant_id, 'woocommerce_fee_data', true);

            if (!empty($woocommerce_fee_data)) {
                foreach ($woocommerce_fee_data as $fee_data) {
                    if (isset($fee_data['value']) && $fee_data['value'] > 0) {
                        $woocommerce->cart->add_fee($fee_data['label'], $fee_data['value']);
                    }
                }
            }
            return $wooccm_custom_user_charge_man;
        }

    }

    global $Payment_Processing;
    $Payment_Processing = new Payment_Processing();
}