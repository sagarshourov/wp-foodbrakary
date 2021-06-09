<?php

/**
 * File Type: Opening Hours
 */
if ( ! class_exists('foodbakery_page_elements') ) {

	class foodbakery_page_elements {

		/**
		 * Start construct Functions
		 */
		public function __construct() {
			add_filter('foodbakery_page_elements_admin_fields', array( $this, 'foodbakery_page_elements_admin_fields_callback' ), 11, 2);
		}

		public function foodbakery_page_elements_admin_fields_callback($post_id, $restaurant_type_slug) {
			global $foodbakery_html_fields, $post;

			$post_id = ( isset($post_id) && $post_id != '' ) ? $post_id : $post->ID;
			$restaurant_type_post = get_posts(array( 'posts_per_page' => '1', 'post_type' => 'restaurant-type', 'name' => "$restaurant_type_slug", 'post_status' => 'publish' ));
			$restaurant_type_id = isset($restaurant_type_post[0]->ID) ? $restaurant_type_post[0]->ID : 0;
			$foodbakery_full_data = get_post_meta($restaurant_type_id, 'foodbakery_full_data', true);
			$html = '';

			$foodbakery_services_data = get_post_meta($post_id, 'foodbakery_services', true);

			$html .= $foodbakery_html_fields->foodbakery_heading_render(
					array(
						'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_page_elements'),
						'cust_name' => 'page_elements',
						'classes' => '',
						'std' => '',
						'description' => '',
						'hint' => '',
						'echo' => false,
					)
			);

			$foodbakery_opt_array = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_page_inquire_form'),
				'desc' => '',
				'hint_text' => '',
				'echo' => false,
				'field_params' => array(
					'std' => '',
					'id' => 'inquiry_form',
					'return' => true,
				),
			);
			$html .= $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

			$foodbakery_opt_array = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_page_financing_calculator'),
				'desc' => '',
				'hint_text' => '',
				'echo' => false,
				'field_params' => array(
					'std' => '',
					'id' => 'financing_calculator',
					'return' => true,
				),
			);
			$html .= $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);
			$foodbakery_opt_array = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_page_similar_posts'),
				'desc' => '',
				'hint_text' => '',
				'echo' => false,
				'field_params' => array(
					'std' => '',
					'id' => 'similar_posts',
					'return' => true,
				),
			);
			$html .= $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

			$foodbakery_opt_array = array(
				'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_page_featured_restaurant_image'),
				'desc' => '',
				'hint_text' => '',
				'echo' => false,
				'field_params' => array(
					'std' => '',
					'id' => 'featured_restaurant_image',
					'return' => true,
				),
			);
			$html .= $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

			if ( isset($foodbakery_full_data['foodbakery_claim_restaurant_element']) && $foodbakery_full_data['foodbakery_claim_restaurant_element'] == 'on' ) {

				$foodbakery_opt_array = array(
					'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_page_claim_restaurant'),
					'desc' => '',
					'hint_text' => '',
					'echo' => false,
					'field_params' => array(
						'std' => '',
						'id' => 'claim_restaurant',
						'return' => true,
					),
				);
				$html .= $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);
			}

			if ( isset($foodbakery_full_data['foodbakery_social_share_element']) && $foodbakery_full_data['foodbakery_social_share_element'] == 'on' ) {

				$foodbakery_opt_array = array(
					'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_page_social_share'),
					'desc' => '',
					'hint_text' => '',
					'echo' => false,
					'field_params' => array(
						'std' => '',
						'id' => 'social_share',
						'return' => true,
					),
				);
				$html .= $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);
			}

			if ( isset($foodbakery_full_data['foodbakery_user_reviews']) && $foodbakery_full_data['foodbakery_user_reviews'] == 'on' ) {

				$foodbakery_opt_array = array(
					'name' => foodbakery_plugin_text_srt('foodbakery_restaurant_page_review_ratings'),
					'desc' => '',
					'hint_text' => '',
					'echo' => false,
					'field_params' => array(
						'std' => '',
						'id' => 'reivew_ratings',
						'return' => true,
					),
				);
				$html .= $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);
			}

			return $html;
		}

	}

	global $foodbakery_page_elements;
	$foodbakery_page_elements = new foodbakery_page_elements();
}