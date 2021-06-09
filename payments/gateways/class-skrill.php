<?php

/**
 *  File Type: Skrill- Monery Booker Gateway
 *
 */
if (!class_exists('FOODBAKERY_SKRILL_GATEWAY')) {

    class FOODBAKERY_SKRILL_GATEWAY extends FOODBAKERY_PAYMENTS {

        
        // Start skrill gateway construct
        
        public function __construct() {
            global $foodbakery_gateway_options;
            $foodbakery_lister_url = '';
            if (isset($foodbakery_gateway_options['foodbakery_skrill_ipn_url'])) {
                $foodbakery_lister_url = $foodbakery_gateway_options['foodbakery_skrill_ipn_url'];
            }



            $foodbakery_gateway_options = get_option('foodbakery_plugin_options');
            $this->gateway_url = "https://www.moneybookers.com/app/payment.pl";
            $this->listner_url = $foodbakery_lister_url;
        }

        
        // Start function for skrill payment gateway setting 
        
        public function settings($foodbakery_gateways_id = '') {
            global $post;

            $foodbakery_rand_id = rand(10000000, 99999999);

            $on_off_option = array("show" => esc_html__("on", "foodbakery"), "hide" => esc_html__("off", "foodbakery"));


            $foodbakery_settings[] = array("name" => esc_html__("Skrill-MoneyBooker Settings", "foodbakery"),
                "id" => "tab-heading-options",
                "std" => esc_html__("Skrill-MoneyBooker Settings", "foodbakery"),
                "type" => "section",
                "id" => "$foodbakery_rand_id",
                "parrent_id" => "$foodbakery_gateways_id",
                "active" => false,
            );



            $foodbakery_settings[] = array("name" => esc_html__("Custom Logo", "foodbakery"),
                "desc" => "",
                "hint_text" => "",
                "id" => "skrill_gateway_logo",
                "std" => wp_foodbakery::plugin_url() . 'payments/images/skrill.png',
                "display" => "none",
                "type" => "upload logo"
            );

            $foodbakery_settings[] = array("name" => esc_html__("Default Status", "foodbakery"),
                "desc" => "",
                "hint_text" => esc_html__("If this switch will be OFF, no payment will be processed via Skrill-MoneyBooker.", "foodbakery"),
                "id" => "skrill_gateway_status",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $foodbakery_settings[] = array("name" => esc_html__("Skrill-MoneryBooker Business Email", "foodbakery"),
                "desc" => "",
                "hint_text" => esc_html__("Add your business Email address here to proceed Skrill-MoneryBooker payments..", "foodbakery"),
                "id" => "skrill_email",
                "std" => "",
                "type" => "text"
            );

            $ipn_url = wp_foodbakery::plugin_url() . 'payments/listner.php';
            $foodbakery_settings[] = array("name" => esc_html__("Skrill-MoneryBooker Ipn Url", "foodbakery"),
                "desc" => '',
                "hint_text" => esc_html__("Here you can add your Skrill-MoneryBooker IPN URL.", "foodbakery"),
                "id" => "skrill_ipn_url",
                "std" => $ipn_url,
                "type" => "text"
            );



            return $foodbakery_settings;
        }
        
         // Start function for skrill payment gateway process request 

        public function foodbakery_proress_request($params = '') {
            global $post, $foodbakery_gateway_options, $foodbakery_form_fields;
            extract($params);

            $foodbakery_current_date = date('Y-m-d H:i:s');
            $output = '';
            $rand_id = $this->foodbakery_get_string(5);
            $business_email = $foodbakery_gateway_options['foodbakery_skrill_email'];

            $currency = foodbakery_get_base_currency();
            $user_ID = get_current_user_id();
			
			$foodbakery_package_title = get_the_title($transaction_package);
			
            $foodbakery_opt_hidden_array = array(
                'id' => '',
                'std' => sanitize_email($business_email),
                'cust_id' => "",
                'cust_name' => "pay_to_email",
                'return' => true,
            );
            $foodbakery_opt_amount_array = array(
                'id' => '',
                'std' => $transaction_amount,
                'cust_id' => "",
                'cust_name' => "amount",
                'return' => true,
            );
            $foodbakery_opt_language_array = array(
                'id' => '',
                'std' => 'EN',
                'cust_id' => "",
                'cust_name' => "language",
                'return' => true,
            );
            $foodbakery_opt_currency_array = array(
                'id' => '',
                'std' => $currency,
                'cust_id' => "",
                'cust_name' => "currency",
                'return' => true,
            );
            $foodbakery_opt_description_array = array(
                'id' => '',
                'std' => 'Membership : ',
                'cust_id' => "",
                'cust_name' => "detail1_description",
                'return' => true,
            );
            $foodbakery_opt_detail1_array = array(
                'id' => '',
                'std' => $foodbakery_package_title,
                'cust_id' => "",
                'cust_name' => "detail1_text",
                'return' => true,
            );
            $foodbakery_opt_detail2_description_array = array(
                'id' => '',
                'std' => 'Ad Title : ',
                'cust_id' => "",
                'cust_name' => "detail2_description",
                'return' => true,
            );
            $foodbakery_opt_detail2_text_array = array(
                'id' => '',
                'std' => sanitize_text_field($foodbakery_package_title),
                'cust_id' => "",
                'cust_name' => "detail2_text",
                'return' => true,
            );
            $foodbakery_opt_detail3_description_array = array(
                'id' => '',
                'std' => "Ad ID : ",
                'cust_id' => "",
                'cust_name' => "detail3_description",
                'return' => true,
            );

            $foodbakery_opt_detail3_text_array = array(
                'id' => '',
                'std' => sanitize_text_field($transaction_id),
                'cust_id' => "",
                'cust_name' => "detail3_text",
                'return' => true,
            );
            $foodbakery_opt_cancel_url_array = array(
                'id' => '',
                'std' => esc_url(get_permalink()),
                'cust_id' => "",
                'cust_name' => "cancel_url",
                'return' => true,
            );

            $foodbakery_opt_status_url_array = array(
                'id' => '',
                'std' => sanitize_text_field($this->listner_url),
                'cust_id' => "",
                'cust_name' => "status_url",
                'return' => true,
            );

            $foodbakery_opt_transaction_id_array = array(
                'id' => '',
                'std' => sanitize_text_field($transaction_id) . '||' . sanitize_text_field($trans_item_id),
                'cust_id' => "",
                'cust_name' => "transaction_id",
                'return' => true,
            );

            $foodbakery_opt_customer_number_array = array(
                'id' => '',
                'std' => $transaction_id,
                'cust_id' => "",
                'cust_name' => "customer_number",
                'return' => true,
            );
			
			$return_url = isset( $transaction_return_url ) ? $transaction_return_url : esc_url( home_url( '/' ) );
            
			$foodbakery_opt_return_url_array = array(
                'id' => '',
                'std' => $return_url,
                'cust_id' => "",
                'cust_name' => "return_url",
                'return' => true,
            );

            $foodbakery_opt_merchant_fields_array = array(
                'id' => '',
                'std' => $transaction_id,
                'cust_id' => "",
                'cust_name' => "merchant_fields",
                'return' => true,
            );
            $output .= '<form name="SkrillForm" id="direcotry-skrill-form" action="' . $this->gateway_url . '" method="post">  
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_hidden_array) . '
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_amount_array) . '
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_language_array) . '
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_currency_array) . '
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_description_array) . '
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_detail1_array) . '
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_detail2_description_array) . '                    
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_detail2_text_array) . '  
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_detail3_description_array) . '  
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_detail3_text_array) . '  
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_cancel_url_array) . '  
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_status_url_array) . '  
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_transaction_id_array) . '  
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_customer_number_array) . '  
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_return_url_array) . '  
                        ' . $foodbakery_form_fields->foodbakery_form_hidden_render($foodbakery_opt_merchant_fields_array) . '  
                        </form>'
						. '<h3>' . __( 'Redirecting to payment gateway website...', 'foodbakery' ) . '</h3>';

            echo force_balance_tags($output);
            echo '<script>
				  	jQuery("#direcotry-skrill-form").submit();
				  </script>';
			if ( isset( $exit ) && $exit != false ) {
				wp_die();
			}
        }

    }

}