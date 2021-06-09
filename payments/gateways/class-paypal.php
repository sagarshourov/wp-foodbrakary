<?php

/**
 *  File Type: Paypal Gateway
 *
 */
if ( ! class_exists('FOODBAKERY_PAYPAL_GATEWAY') ) {

	class FOODBAKERY_PAYPAL_GATEWAY extends FOODBAKERY_PAYMENTS {

		public function __construct() {
			global $foodbakery_gateway_options;

			$foodbakery_gateway_options = get_option('foodbakery_plugin_options');

			$foodbakery_lister_url = '';
			if ( isset($foodbakery_gateway_options['foodbakery_dir_paypal_ipn_url']) ) {
				$foodbakery_lister_url = $foodbakery_gateway_options['foodbakery_dir_paypal_ipn_url'];
			}

			if ( isset($foodbakery_gateway_options['foodbakery_paypal_sandbox']) && $foodbakery_gateway_options['foodbakery_paypal_sandbox'] == 'on' ) {
				$this->gateway_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
			} else {
				$this->gateway_url = "https://www.paypal.com/cgi-bin/webscr";
			}
			$this->listner_url = $foodbakery_lister_url;
		}

		// Start function for paypal setting 

		public function settings($foodbakery_gateways_id = '') {
			global $post;

			$foodbakery_rand_id = rand(10000000, 99999999);

			$on_off_option = array( "show" => esc_html__("on", "foodbakery"), "hide" => esc_html__("off", "foodbakery") );



			$foodbakery_settings[] = array(
				"name" => esc_html__("Paypal Settings", 'foodbakery'),
				"id" => "tab-heading-options",
				"std" => esc_html__("Paypal Settings", "foodbakery"),
				"type" => "section",
				"options" => "",
				"parrent_id" => "$foodbakery_gateways_id",
				"active" => true,
			);



			$foodbakery_settings[] = array( "name" => esc_html__("Custom Logo ", "foodbakery"),
				"desc" => "",
				"hint_text" => "",
				"id" => "paypal_gateway_logo",
				"std" => wp_foodbakery::plugin_url() . 'payments/images/paypal.png',
				"display" => "none",
				"type" => "upload logo"
			);

			$foodbakery_settings[] = array( "name" => esc_html__("Default Status", "foodbakery"),
				"desc" => "",
				"hint_text" => esc_html__("If this switch will be OFF, no payment will be processed via Paypal. ", "foodbakery"),
				"id" => "paypal_gateway_status",
				"std" => "on",
				"type" => "checkbox",
				"options" => $on_off_option
			);

			$foodbakery_settings[] = array( "name" => esc_html__("Paypal Sandbox", "foodbakery"),
				"desc" => "",
				"hint_text" => esc_html__("Control PayPal sandbox Account with this switch. If this switch is set to ON, payments will be  proceed with sandbox account.", "foodbakery"),
				"id" => "paypal_sandbox",
				"std" => "on",
				"type" => "checkbox",
				"options" => $on_off_option
			);

			$foodbakery_settings[] = array( "name" => esc_html__("Paypal Business Email", "foodbakery"),
				"desc" => "",
				"hint_text" => esc_html__("Add your business Email address here to proceed PayPal payments.", "foodbakery"),
				"id" => "paypal_email",
				"std" => "",
				"type" => "text"
			);

			$ipn_url = wp_foodbakery::plugin_url() . 'payments/listner.php';
			$foodbakery_settings[] = array( "name" => esc_html__("Paypal Ipn Url", "foodbakery"),
				"desc" => '',
				"hint_text" => esc_html__("Here you can add your PayPal IPN URL.", "foodbakery"),
				"id" => "dir_paypal_ipn_url",
				"std" => $ipn_url,
				"type" => "text"
			);



			return $foodbakery_settings;
		}

		// Start function for paypal process request  

		public function foodbakery_proress_request($params = '') {
			global $post, $foodbakery_gateway_options, $foodbakery_form_fields;
			extract($params);

			$foodbakery_current_date = date('Y-m-d H:i:s');
			$output = '';
			$rand_id = $this->foodbakery_get_string(5);
			$business_email = $foodbakery_gateway_options['foodbakery_paypal_email'];

			$foodbakery_package_title = get_the_title($transaction_package);
			$currency = foodbakery_get_base_currency();
			
			$return_url = isset( $transaction_return_url ) ? $transaction_return_url : esc_url( home_url( '/' ) );
			
			$foodbakery_opt_hidden1_array = array(
				'id' => '',
				'std' => '_xclick',
				'cust_id' => "",
				'cust_name' => "cmd",
				'return' => true,
			);
			$foodbakery_opt_hidden2_array = array(
				'id' => '',
				'std' => sanitize_email($business_email),
				'cust_id' => "",
				'cust_name' => "business",
				'return' => true,
			);
			$foodbakery_opt_hidden3_array = array(
				'id' => '',
				'std' => $transaction_amount,
				'cust_id' => "",
				'cust_name' => "amount",
				'return' => true,
			);
			$foodbakery_opt_hidden4_array = array(
				'id' => '',
				'std' => $currency,
				'cust_id' => "",
				'cust_name' => "currency_code",
				'return' => true,
			);
			$foodbakery_opt_hidden5_array = array(
				'id' => '',
				'std' => $foodbakery_package_title,
				'cust_id' => "",
				'cust_name' => "item_name",
				'return' => true,
			);
			$foodbakery_opt_hidden6_array = array(
				'id' => '',
				'std' => $trans_item_id,
				'cust_id' => "",
				'cust_name' => "item_number",
				'return' => true,
			);
			$foodbakery_opt_hidden7_array = array(
				'id' => '',
				'std' => '',
				'cust_id' => "",
				'cust_name' => "cancel_return",
				'return' => true,
			);
			$foodbakery_opt_hidden8_array = array(
				'id' => '',
				'std' => '1',
				'cust_id' => "",
				'cust_name' => "no_note",
				'return' => true,
			);
			$foodbakery_opt_hidden9_array = array(
				'id' => '',
				'std' => sanitize_text_field($transaction_id),
				'cust_id' => "",
				'cust_name' => "invoice",
				'return' => true,
			);
			$foodbakery_opt_hidden10_array = array(
				'id' => '',
				'std' => esc_url($this->listner_url),
				'cust_id' => "",
				'cust_name' => "notify_url",
				'return' => true,
			);
			$foodbakery_opt_hidden11_array = array(
				'id' => '',
				'std' => '',
				'cust_id' => "",
				'cust_name' => "lc",
				'return' => true,
			);
			$foodbakery_opt_hidden12_array = array(
				'id' => '',
				'std' => '2',
				'cust_id' => "",
				'cust_name' => "rm",
				'return' => true,
			);
			$foodbakery_opt_hidden13_array = array(
				'id' => '',
				'std' => sanitize_text_field($transaction_id),
				'cust_id' => "",
				'cust_name' => "custom",
				'return' => true,
			);
			$foodbakery_opt_hidden14_array = array(
				'id' => '',
				'std' => $return_url,
				'cust_id' => "",
				'cust_name' => "return",
				'return' => true,
			);

			$output .= '<form name="PayPalForm" id="direcotry-paypal-form" action="' . $this->gateway_url . '" method="post">  
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
                        </form>'
						. '<h3>' . __( 'Redirecting to payment gateway website...', 'foodbakery' ) . '</h3>';


			$data = force_balance_tags($output);
			$data .= '<script>
					  	  jQuery("#direcotry-paypal-form").submit();
					  </script>';
			echo force_balance_tags($data);
		}

		public function foodbakery_gateway_listner() {
			
		}

	}

}