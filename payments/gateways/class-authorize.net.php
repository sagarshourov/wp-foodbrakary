<?php

/**
 *  File Type: Authorize.net Gateway

 */
if (!class_exists('FOODBAKERY_AUTHORIZEDOTNET_GATEWAY')) {

    class FOODBAKERY_AUTHORIZEDOTNET_GATEWAY extends FOODBAKERY_PAYMENTS {

        // Call a construct for objects 
        public function __construct() {
            // Do Something
            global $foodbakery_gateway_options;
            $foodbakery_gateway_options = get_option('foodbakery_plugin_options');
            $foodbakery_lister_url = '';
            if (isset($foodbakery_gateway_options['foodbakery_authorizenet_ipn_url'])) {
                $foodbakery_lister_url = $foodbakery_gateway_options['foodbakery_authorizenet_ipn_url'];
            }
            if (isset($foodbakery_gateway_options['foodbakery_authorizenet_sandbox']) && $foodbakery_gateway_options['foodbakery_authorizenet_sandbox'] == 'on') {
                $this->gateway_url = "https://test.authorize.net/gateway/transact.dll";
            } else {
                $this->gateway_url = "https://secure.authorize.net/gateway/transact.dll";
            }
            $this->listner_url = $foodbakery_lister_url;
        }

        // Start function for Authorize.net payment gateway
        
        public function settings($foodbakery_gateways_id = '') {
            global $post;

            $foodbakery_rand_id = rand(10000000, 99999999);

            $on_off_option = array("show" => esc_html__("on", "foodbakery"), "hide" => esc_html__("off", "foodbakery"));




            $foodbakery_settings[] = array(
                "name" => esc_html__("Authorize.net Settings", 'foodbakery'),
                "id" => "tab-heading-options",
                "std" => esc_html__("Authorize.net Settings", "foodbakery"),
                "type" => "section",
                "options" => "",
                "parrent_id" => "$foodbakery_gateways_id",
                "active" => true,
            );




            $foodbakery_settings[] = array("name" => esc_html__("Custom Logo", "foodbakery"),
                "desc" => "",
                "hint_text" => "",
                "id" => "authorizedotnet_gateway_logo",
                "std" => wp_foodbakery::plugin_url() . 'payments/images/athorizedotnet_.png',
                "display" => "none",
                "type" => "upload logo"
            );

            $foodbakery_settings[] = array("name" => esc_html__("Default Status", "foodbakery"),
                "desc" => "",
                "hint_text" => esc_html__("If this switch will be OFF, no payment will be processed via Authorize.net.", "foodbakery"),
                "id" => "authorizedotnet_gateway_status",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $foodbakery_settings[] = array("name" => esc_html__("Authorize.net Sandbox", "foodbakery"),
                "desc" => "",
                "hint_text" => esc_html__("Control Authorize.net sandbox Account with this switch. If this switch is set to ON, payments will be  proceed with sandbox account.", "foodbakery"),
                "id" => "authorizenet_sandbox",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $foodbakery_settings[] = array("name" => esc_html__("Login Id", "foodbakery"),
                "desc" => "",
                "hint_text" => esc_html__("Add your Authorize.net login ID here. You will get it while signing up on Authorize.net.", "foodbakery"),
                "id" => "authorizenet_login",
                "std" => "",
                "type" => "text"
            );

            $foodbakery_settings[] = array("name" => esc_html__("Transaction Key", "foodbakery"),
                "desc" => "",
                "hint_text" => esc_html__("Add your Authorize.net Transaction Key here. You will get this key while signing up on Authorize.net", "foodbakery"),
                "id" => "authorizenet_transaction_key",
                "std" => "",
                "type" => "text"
            );

            $ipn_url = wp_foodbakery::plugin_url() . 'payments/listner.php';
            $foodbakery_settings[] = array("name" => esc_html__("Authorize.net Ipn Url", "foodbakery"),
                "desc" => '',
                "hint_text" => esc_html__("Here you can add your Authorize.net IPN URL.", "foodbakery"),
                "id" => "dir_authorizenet_ipn_url",
                "std" => $ipn_url,
                "type" => "text"
            );



            return $foodbakery_settings;
        }
            // Start function for process request Authorize.net payment gateway
        public function foodbakery_proress_request($params = '') {
            global $post, $foodbakery_gateway_options, $foodbakery_form_fields;
          
            extract($params);
            $foodbakery_current_date = date('Y-m-d H:i:s');
            $output = '';
            $rand_id = $this->foodbakery_get_string(5);
            $foodbakery_login = '';
            if (isset($foodbakery_gateway_options['foodbakery_authorizenet_login'])) {
                $foodbakery_login = $foodbakery_gateway_options['foodbakery_authorizenet_login'];
            }
            $transaction_key = '';
            if (isset($foodbakery_gateway_options['foodbakery_authorizenet_transaction_key'])) {
                $transaction_key = $foodbakery_gateway_options['foodbakery_authorizenet_transaction_key'];
            }
            if (isset($package)) {
                $package = $foodbakery_gateway_options['foodbakery_packages_options'][$foodbakery_trans_pkg];
            }

            $timeStamp = time();
            $sequence = rand(1, 1000);

            if (phpversion() >= '5.1.2') {
                $fingerprint = hash_hmac("md5", $foodbakery_login . "^" . $sequence . "^" . $timeStamp . "^" . $transaction_amount . "^", $transaction_key);
            } else {
                $fingerprint = bin2hex(mhash(MHASH_MD5, $foodbakery_login . "^" . $sequence . "^" . $timeStamp . "^" . $transaction_amount . "^", $transaction_key));
            }
			
			$foodbakery_package_title = get_the_title($transaction_package);

            $currency = foodbakery_get_base_currency();
            $user_ID = get_current_user_id();

            $foodbakery_opt_hidden1_array = array(
                'id' => '',
                'std' => $foodbakery_login,
                'cust_id' => "",
                'cust_name' => "x_login",
                'return' => true,
            );
            $foodbakery_opt_hidden2_array = array(
                'id' => '',
                'std' => 'AUTH_CAPTURE',
                'cust_id' => "",
                'cust_name' => "x_type",
                'return' => true,
            );
            $foodbakery_opt_hidden3_array = array(
                'id' => '',
                'std' => $transaction_amount,
                'cust_id' => "",
                'cust_name' => "x_amount",
                'return' => true,
            );
           
            $foodbakery_opt_hidden4_array = array(
                'id' => '',
                'std' => $sequence,
                'cust_id' => "",
                'cust_name' => "x_fp_sequence",
                'return' => true,
            );
            $foodbakery_opt_hidden5_array = array(
                'id' => '',
                'std' => $timeStamp,
                'cust_id' => "",
                'cust_name' => "x_fp_timestamp",
                'return' => true,
            );
            $foodbakery_opt_hidden6_array = array(
                'id' => '',
                'std' => $fingerprint,
                'cust_id' => "",
                'cust_name' => "x_fp_hash",
                'return' => true,
            );
            $foodbakery_opt_hidden7_array = array(
                'id' => '',
                'std' => 'PAYMENT_FORM',
                'cust_id' => "",
                'cust_name' => "x_show_form",
                'return' => true,
            );
            $foodbakery_opt_hidden8_array = array(
                'id' => '',
                'std' => 'ORDER-' . sanitize_text_field($transaction_id),
                'cust_id' => "",
                'cust_name' => "x_invoice_num",
                'return' => true,
            );
            $foodbakery_opt_hidden9_array = array(
                'id' => '',
                'std' => sanitize_text_field($transaction_id),
                'cust_id' => "",
                'cust_name' => "x_po_num",
                'return' => true,
            );
            $foodbakery_opt_hidden10_array = array(
                'id' => '',
                'std' => sanitize_text_field($trans_item_id),
                'cust_id' => "",
                'cust_name' => "x_cust_id",
                'return' => true,
            );
            $foodbakery_opt_hidden11_array = array(
                'id' => '',
                'std' => sanitize_text_field($foodbakery_package_title),
                'cust_id' => "",
                'cust_name' => "x_description",
                'return' => true,
            );
			$return_url = isset( $transaction_return_url ) ? $transaction_return_url : esc_url( home_url( '/' ) );
			$foodbakery_opt_hidden18_array = array(
                'id' => '',
                'std' => $return_url,
                'cust_id' => "",
                'cust_name' => "x_receipt_link_url",
                'return' => true,
            );
            $foodbakery_opt_hidden12_array = array(
                'id' => '',
                'std' => esc_url( home_url( '/' ) ),
                'cust_id' => "",
                'cust_name' => "x_cancel_url",
                'return' => true,
            );
            $foodbakery_opt_hidden13_array = array(
                'id' => '',
                'std' => esc_html__('Cancel Order', 'foodbakery'),
                'cust_id' => "",
                'cust_name' => "x_cancel_url_text",
                'return' => true,
            );
            $foodbakery_opt_hidden14_array = array(
                'id' => '',
                'std' => 'TRUE',
                'cust_id' => "",
                'cust_name' => "x_relay_response",
                'return' => true,
            );
            $foodbakery_opt_hidden15_array = array(
                'id' => '',
                'std' => sanitize_text_field($this->listner_url),
                'cust_id' => "",
                'cust_name' => "x_relay_url",
                'return' => true,
            );
            $foodbakery_opt_hidden16_array = array(
                'id' => '',
                'std' => 'false',
                'cust_id' => "",
                'cust_name' => "x_test_request",
                'return' => true,
            );
             $foodbakery_opt_hidden17_array = array(
				'id' => '',
				'std' => $currency,
				'cust_id' => "",
				'cust_name' => "currency_code",
				'return' => true,
			);
            $output .= '<form name="AuthorizeForm" id="direcotry-authorize-form" action="' . $this->gateway_url . '" method="post">  
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden1_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden2_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden3_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden4_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden5_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden6_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden7_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden8_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden9_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden10_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden11_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden12_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden13_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden14_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden15_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden16_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden17_array) . '
				' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden18_array) . '
				</form>'
				. '<h3>' . __( 'Redirecting to payment gateway website...', 'foodbakery' ) . '</h3>';
            echo force_balance_tags($output);
            echo '<script>
				    	jQuery("#direcotry-authorize-form").submit();
				      </script>';
			if ( isset( $exit ) && $exit != false ) {
				wp_die();
			}
        }

    }

}