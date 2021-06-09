<?php

/**
 * File Type: Memberships Post Type Metas
 */
if ( ! class_exists('orders_inquiries_post_type_meta') ) {

	class orders_inquiries_post_type_meta {

		/**
		 * Start Contructer Function
		 */
		public function __construct() {
			add_action('add_meta_boxes', array( &$this, 'orders_inquiries_add_meta_boxes_callback' ));
		}

		/**
		 * Add meta boxes Callback Function
		 */
		public function orders_inquiries_add_meta_boxes_callback() {
			add_meta_box('foodbakery_meta_orders_inquiries', esc_html(foodbakery_plugin_text_srt('foodbakery_restaurant_orders_inquiries_options')), array( $this, 'foodbakery_meta_orders_inquiries' ), 'orders_inquiries', 'normal', 'high');
		}

		public function foodbakery_meta_orders_inquiries() {
			global $post, $orders_inquiries;

			$foodbakery_users_list = array();
			$foodbakery_users = get_users('orderby=nicename');
			if ( $foodbakery_users ) {
				foreach ( $foodbakery_users as $user ) {
					$foodbakery_users_list[$user->ID] = $user->display_name;
				}
			}

			$order_type = get_post_meta($post->ID, 'foodbakery_order_type', true);
			$order_menu_list = get_post_meta($post->ID, 'menu_items_list', true);

			$menu_order_fee = get_post_meta($post->ID, 'menu_order_fee', true);
			$menu_order_fee_type = get_post_meta($post->ID, 'menu_order_fee_type', true);

			$foodbakery_publishers_list = array();
			$args = array( 'posts_per_page' => '-1', 'post_type' => 'publishers', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC' );
			$cust_query = get_posts($args);
			if ( is_array($cust_query) && sizeof($cust_query) > 0 ) {
				foreach ( $cust_query as $package_post ) {
					if ( isset($package_post->ID) ) {
						$package_id = $package_post->ID;
						$package_title = $package_post->post_title;
						$foodbakery_publishers_list[$package_id] = $package_title;
					}
				}
			}

			$orders_meta = array();

			$orders_meta['order_id'] = array(
				'name' => 'order_id',
				'type' => 'hidden_label',
				'title' => esc_html__('Order Id', 'foodbakery'),
				'description' => '',
			);

			$orders_meta['order_user'] = array(
				'name' => 'order_user',
				'type' => 'select',
				'classes' => 'chosen-select',
				'title' => esc_html__('Order User', 'foodbakery'),
				'options' => $foodbakery_publishers_list,
				'description' => '',
			);
			
			$orders_meta['publisher_id'] = array(
				'name' => 'publisher_id',
				'type' => 'select',
				'classes' => 'chosen-select',
				'title' => esc_html__('Service User', 'foodbakery'),
				'options' => $foodbakery_publishers_list,
				'description' => '',
			);

			$orders_meta['services_total_price'] = array(
				'name' => 'services_total_price',
				'type' => 'text',
				'title' => esc_html__('Total Price', 'foodbakery'),
				'description' => '',
				'active' => 'in-active',
			);

			if ( $order_type == 'order' && is_array($order_menu_list) ) {

				$orders_meta['order_amount_charged'] = array(
					'name' => 'order_amount_charged',
					'type' => 'text',
					'title' => esc_html__('Commission Charged', 'foodbakery'),
					'description' => '',
					'active' => 'in-active',
				);
			}

			$html = '
			<div class="page-wrap">
				<div class="option-sec" style="margin-bottom:0;">
					<div class="opt-conts">
						<div class="foodbakery-review-wrap">';
			foreach ( $orders_meta as $key => $params ) {
				$html .= $this->foodbakery_create_orders_fields($key, $params);
			}
			$html .= '</div>
					</div>
				</div>
				<div class="clear"></div>
			</div>';
			echo force_balance_tags($html);
		}

		public function foodbakery_create_orders_fields($key, $param = array()) {
			global $post, $foodbakery_html_fields, $foodbakery_form_fields, $foodbakery_plugin_options;
			$foodbakery_currency_sign = get_post_meta($post->ID, 'foodbakery_currency', true);
			$foodbakery_value = $param['title'];
			$html = '';
			switch ( $param['type'] ) {
				case 'text' :
					// prepare
					$foodbakery_value = get_post_meta($post->ID, $key, true);

					if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
						if ( $key == 'foodbakery_order_date' ) {
							$foodbakery_value = date_i18n('d-m-Y', $foodbakery_value);
						} else {
							$foodbakery_value = $foodbakery_value;
						}
					} else {
						$foodbakery_value = isset($param['std']) ? $param['std'] : '';
					}

					if ( $key == 'services_total_price' || $key == 'order_amount_charged' ) {
						$foodbakery_value = $foodbakery_value;
						$param['title'] .= ' (' . $foodbakery_currency_sign . ')';
					}

					$foodbakery_opt_array = array(
						'name' => $param['title'],
						'desc' => '',
						'hint_text' => '',
						'field_params' => array(
							'std' => $foodbakery_value,
							'cust_id' => $key,
							'cust_name' => $key,
							'classes' => 'foodbakery-form-text foodbakery-input',
							'force_std' => true,
							'return' => true,
							'active' => $param['active'],
						),
					);
					$output = '';
					$output .= $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
					$output .= '<span class="foodbakery-form-desc">' . $param['description'] . '</span>' . "\n";


					$html .= $output;
					break;
				case 'checkbox' :
					// prepare
					$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $key, true);

					$foodbakery_opt_array = array(
						'name' => $param['title'],
						'desc' => '',
						'hint_text' => '',
						'field_params' => array(
							'std' => $foodbakery_value,
							'id' => $key,
							'classes' => 'foodbakery-form-text foodbakery-input',
							'force_std' => true,
							'return' => true,
						),
					);
					$output = '';
					$output .= $foodbakery_html_fields->foodbakery_checkbox_field($foodbakery_opt_array);

					$html .= $output;
					break;
				case 'textarea' :
					// prepare
					$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $key, true);
					if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
						$foodbakery_value = $foodbakery_value;
					} else {
						$foodbakery_value = '';
					}

					$foodbakery_opt_array = array(
						'name' => $param['title'],
						'desc' => '',
						'hint_text' => '',
						'field_params' => array(
							'std' => '',
							'id' => $key,
							'return' => true,
						),
					);

					$output = $foodbakery_html_fields->foodbakery_textarea_field($foodbakery_opt_array);
					$html .= $output;
					break;
				case 'select' :
					$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $key, true);
					if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
						$foodbakery_value = $foodbakery_value;
					} else {
						$foodbakery_value = '';
					}
					$foodbakery_classes = '';
					if ( isset($param['classes']) && $param['classes'] != "" ) {
						$foodbakery_classes = $param['classes'];
					}
					$foodbakery_opt_array = array(
						'name' => $param['title'],
						'desc' => '',
						'hint_text' => '',
						'field_params' => array(
							'std' => '',
							'id' => $key,
							'classes' => $foodbakery_classes,
							'options' => $param['options'],
							'return' => true,
						),
					);

					$output = $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
					// append
					$html .= $output;
					break;
				case 'hidden_label' :
					// prepare
					$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $key, true);

					if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
						$foodbakery_value = $foodbakery_value;
					} else {
						$foodbakery_value = '';
					}

					$foodbakery_opt_array = array(
						'name' => $param['title'],
						'hint_text' => '',
					);
					$output = $foodbakery_html_fields->foodbakery_opening_field($foodbakery_opt_array);

					$output .= '<span>#' . $foodbakery_value . '</span>';

					$output .= $foodbakery_form_fields->foodbakery_form_hidden_render(
							array(
								'name' => '',
								'id' => $key,
								'return' => true,
								'classes' => '',
								'std' => $foodbakery_value,
								'description' => '',
								'hint' => ''
							)
					);

					$foodbakery_opt_array = array(
						'desc' => '',
					);
					$output .= $foodbakery_html_fields->foodbakery_closing_field($foodbakery_opt_array);
					$html .= $output;
					break;
				default :
					break;
			}
			return $html;
		}

	}

	// Initialize Object
	$orders_inquiries_meta_object = new orders_inquiries_post_type_meta();
}