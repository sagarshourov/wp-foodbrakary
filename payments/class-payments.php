<?php

global $gateways;
/**
 *  File Type: Payemnts Base Class
 *
 */
if ( ! class_exists('FOODBAKERY_PAYMENTS') ) {

	class FOODBAKERY_PAYMENTS {

		public $gateways;

		public function __construct() {
			global $gateways;
			$gateways['FOODBAKERY_PAYPAL_GATEWAY'] = 'Paypal';
			$gateways['FOODBAKERY_AUTHORIZEDOTNET_GATEWAY'] = 'Authorize.net';
			$gateways['FOODBAKERY_PRE_BANK_TRANSFER'] = 'Pre Bank Transfer';
			$gateways['FOODBAKERY_SKRILL_GATEWAY'] = 'Skrill-MoneyBooker';
		}

		// Start function currency general setting 

		public function foodbakery_general_settings() {
			global $foodbakery_settings, $foodbakery_plugin_options;
                        $base_currency = isset( $foodbakery_plugin_options['foodbakery_base_currency'] ) ? $foodbakery_plugin_options['foodbakery_base_currency'] : 'USD';
			$currencies = array();
			$foodbakery_currencuies = foodbakery_get_currencies();
			if ( is_array($foodbakery_currencuies) ) {
				foreach ( $foodbakery_currencuies as $key => $value ) {
					$currencies[$key] = $value['name'] . '-' . $value['code'];
				}
			}
                        $foodbakery_settings[] = array( "name" => esc_html__("Base Currency", "foodbakery"),
				"desc" => "",
				"hint_text" => esc_html__("All the transactions will be placed in this currency.", "foodbakery"),
				"id" => "base_currency",
				"std" => "USD",
				'classes' => 'dropdown chosen-select-no-single base-currency-change',
				"type" => "select_values",
				"options" => $currencies
			);

            $foodbakery_settings[] = array(
                "name" => esc_html__("Currency Alignment", "foodbakery"),
                "desc" => "",
                "id" => "currency_alignment",
                "std" => "Left",
                'classes' => 'dropdown chosen-select-no-single',
                "type" => "select",
                "custom" => true,
                "options" => array('Left' => 'Left', 'Right' => 'Right'),
            );
			
                        /*$all_currencies     = foodbakery_all_currencies_array( $base_currency );
                        $foodbakery_settings[] = array( "name" => esc_html__("Select Currency", "foodbakery"),
				"desc" => "",
				"hint_text" => esc_html__("Overall website currency to show the prices. The payments will be processed in Base Currency.", "foodbakery"),
				"id" => "currency_id",
				"std" => "USD",
				'classes' => 'dropdown chosen-select-no-single ',
				"type" => "select_values",
				"options" => $all_currencies
			);*/
			return $foodbakery_settings;
		}

		// Start function get string length

		public function foodbakery_get_string($length = 3) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			for ( $i = 0; $i < $length; $i ++ ) {
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}
			return $randomString;
		}

		// Start function for add transaction 

		public function foodbakery_add_transaction($fields = array()) {
			global $foodbakery_plugin_options;
			define("DEBUG", 1);
			define("USE_SANDBOX", 1);
			define("LOG_FILE", "./ipn.log");
			include_once('../../../../wp-load.php');
			if ( is_array($fields) ) {
				foreach ( $fields as $key => $value ) {
					update_post_meta((int) $fields['foodbakery_transaction_id'], "$key", $value);
				}
			}
			return true;
		}

	}

}
