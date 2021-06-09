<?php

/**
 *  File Type: Pre Bank Transfer
 *
 */
if ( ! class_exists('FOODBAKERY_PRE_BANK_TRANSFER') ) {

	class FOODBAKERY_PRE_BANK_TRANSFER {

		public function __construct() {
			global $foodbakery_gateway_options;
			$foodbakery_gateway_options = get_option('foodbakery_plugin_options');
		}

		// Start function for Bank Transfer setting 

		public function settings($foodbakery_gateways_id = '') {
			global $post;

			$foodbakery_rand_id = rand(10000000, 99999999);

			$on_off_option = array( "show" => esc_html__("on", "foodbakery"), "hide" => esc_html__("off", "foodbakery") );

			$foodbakery_settings[] = array( "name" => esc_html__("Bank Transfer Settings", "foodbakery"),
				"id" => "tab-heading-options",
				"std" => esc_html__("Bank Transfer Settings", "foodbakery"),
				"type" => "section",
				"parrent_id" => "$foodbakery_gateways_id",
				"active" => false,
			);



			$foodbakery_settings[] = array( "name" => esc_html__("Custom Logo", "foodbakery"),
				"desc" => "",
				"hint_text" => "",
				"id" => "pre_bank_transfer_logo",
				"std" => wp_foodbakery::plugin_url() . 'payments/images/bank.png',
				"display" => "none",
				"type" => "upload logo"
			);

			$foodbakery_settings[] = array( "name" => esc_html__("Default Status", "foodbakery"),
				"desc" => "",
				"hint_text" => esc_html__("If this switch will be OFF, no payment will be processed via Bank Transfer.", "foodbakery"),
				"id" => "pre_bank_transfer_status",
				"std" => "on",
				"type" => "checkbox",
				"options" => $on_off_option
			);
			$foodbakery_settings[] = array( "name" => esc_html__("Bank Information", "foodbakery"),
				"desc" => "",
				"hint_text" => esc_html__("Add information of your bank (Bank Name).", "foodbakery"),
				"id" => "bank_information",
				"std" => "",
				"type" => "text"
			);
			$foodbakery_settings[] = array( "name" => esc_html__("Account Number", "foodbakery"),
				"desc" => "",
				"hint_text" => esc_html__("Add your bank account Number where you want receive payment.", "foodbakery"),
				"id" => "bank_account_id",
				"std" => "",
				"type" => "text"
			);
			$foodbakery_settings[] = array( "name" => esc_html__("Other Information", "foodbakery"),
				"desc" => "",
				"hint_text" => esc_html__("In this text box, you can add any help text whatever you want to show on front end for assistance of users regarding bank payment.", "foodbakery"),
				"id" => "other_information",
				"std" => "",
				"type" => "textarea"
			);



			return $foodbakery_settings;
		}

		// Start function for process request.
		public function foodbakery_proress_request($params = '') {
			global $post, $foodbakery_plugin_options, $foodbakery_gateway_options, $current_user;

			extract($params);

			$foodbakery_totl_amount = 0;
			$foodbakery_detail = '';
			$foodbakery_currency_sign = foodbakery_get_currency_sign();
			if ( isset($transaction_package) && $transaction_package <> '' ) {
				$transaction_package_title = $transaction_package <> '' ? get_the_title($transaction_package) : '';
				$transaction_package_price = $transaction_package <> '' ? get_post_meta($transaction_package, 'foodbakery_package_price', true) : '';
				$foodbakery_detail .= '<li>' . esc_html__('Membership : ', 'foodbakery') . $transaction_package_title . ' - ' . $foodbakery_currency_sign . $transaction_package_price . '</li>';
				$foodbakery_totl_amount += FOODBAKERY_FUNCTIONS()->num_format($transaction_package_price);

				$foodbakery_totl_amount = FOODBAKERY_FUNCTIONS()->num_format($foodbakery_totl_amount);

				$foodbakery_detail .= '<li>' . esc_html__('Charges: ', 'foodbakery') . $foodbakery_currency_sign . $foodbakery_totl_amount . '</li>';
			}

			if ( isset($vat_amount) && $vat_amount > 0 && $foodbakery_totl_amount > 0) {

				$foodbakery_totl_amount += $vat_amount;
				$foodbakery_totl_amount = FOODBAKERY_FUNCTIONS()->num_format($foodbakery_totl_amount);

				$foodbakery_detail .= '<li>' . esc_html__('VAT :', 'foodbakery') . ' ' . $foodbakery_currency_sign . $vat_amount . '</li>';
				$foodbakery_detail .= '<li>' . esc_html__('Total Charges: ', 'foodbakery') . $foodbakery_currency_sign . $foodbakery_totl_amount . '</li>';
			}

			$foodbakery_bank_transfer = '<div class="foodbakery-bank-transfer">';
			$foodbakery_bank_transfer .= '<h2>' . esc_html__('Order detail', 'foodbakery') . '</h2>';

			$foodbakery_bank_transfer .= '<ul class="list-group">';
			$foodbakery_bank_transfer .= '<li class="list-group-item">';
			$foodbakery_bank_transfer .= '<span class="badge">#' . (isset($trans_rand_id) ? $trans_rand_id : $transaction_id) . '</span>';
			$foodbakery_bank_transfer .= esc_html__('Order ID', 'foodbakery');
			$foodbakery_bank_transfer .= '</li>';
			$foodbakery_bank_transfer .= $foodbakery_detail;
			$foodbakery_bank_transfer .= '</ul>';
			
			
			$foodbakery_bank_transfer .= '<h2>' . esc_html__('Bank detail', 'foodbakery') . '</h2>';
			$foodbakery_bank_transfer .= '<p>' . esc_html__('Please transfer amount To this account, After payment Received we will process your Order', 'foodbakery') . '</p>';
			$foodbakery_bank_transfer .= '<ul class="list-group">';

			if ( isset($foodbakery_gateway_options['foodbakery_bank_information']) && $foodbakery_gateway_options['foodbakery_bank_information'] != '' ) {
				$foodbakery_bank_transfer .= '<li class="list-group-item">';
				$foodbakery_bank_transfer .= '<span class="badge">' . $foodbakery_gateway_options['foodbakery_bank_information'] . '</span>';
				$foodbakery_bank_transfer .= esc_html__('Bank Information', 'foodbakery');
				$foodbakery_bank_transfer .= '</li>';
			}

			if ( isset($foodbakery_gateway_options['foodbakery_bank_account_id']) && $foodbakery_gateway_options['foodbakery_bank_account_id'] != '' ) {
				$foodbakery_bank_transfer .= '<li class="list-group-item">';
				$foodbakery_bank_transfer .= '<span class="badge">' . $foodbakery_gateway_options['foodbakery_bank_account_id'] . '</span>';
				$foodbakery_bank_transfer .= esc_html__('Account No', 'foodbakery');
				$foodbakery_bank_transfer .= '</li>';
			}

			if ( isset($foodbakery_gateway_options['foodbakery_other_information']) && $foodbakery_gateway_options['foodbakery_other_information'] != '' ) {
				$foodbakery_bank_transfer .= '<li class="list-group-item">';
				$foodbakery_bank_transfer .= '<span>' . $foodbakery_gateway_options['foodbakery_other_information'] . '</span>';
				$foodbakery_bank_transfer .= '</li>';
			}

			$foodbakery_bank_transfer .= '</ul>';
			$foodbakery_bank_transfer .= '</div>';

			return force_balance_tags($foodbakery_bank_transfer);
		}

	}

}