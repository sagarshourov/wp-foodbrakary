<?php

/**
 * Start Function  how to Create Transations Fields
 */
if ( ! function_exists('foodbakery_create_package_orders_fields') ) {

	function foodbakery_create_package_orders_fields($key, $param = array()) {
		global $post, $foodbakery_html_fields, $foodbakery_form_fields, $foodbakery_plugin_options;
		$foodbakery_gateway_options = get_option('foodbakery_plugin_options');
		$foodbakery_currency_sign = foodbakery_get_currency_sign();
		$foodbakery_value = $param['title'];
		$html = '';
		switch ( $param['type'] ) {
			case 'text' :
				// prepare
				$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $key, true);

				if ( isset($foodbakery_value) && $foodbakery_value != '' ) {
					if ( $key == 'transaction_expiry_date' ) {
						$foodbakery_value = date_i18n('d-m-Y', $foodbakery_value);
					} else {
						$foodbakery_value = $foodbakery_value;
					}
				} else {
					$foodbakery_value = '';
				}

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
			case 'trans_dynamic' :
				$foodbakery_trans_dynamic = get_post_meta($post->ID, "foodbakery_transaction_dynamic", true);
				
				if(is_array($foodbakery_trans_dynamic) && sizeof($foodbakery_trans_dynamic) > 0) {
					$foodbakery_opt_array = array(
						'name' => $param['title'],
						'hint_text' => '',
					);
					$output = $foodbakery_html_fields->foodbakery_opening_field($foodbakery_opt_array);
					
					foreach($foodbakery_trans_dynamic as $trans_dynamic){
						if(isset($trans_dynamic['field_type']) && isset($trans_dynamic['field_label']) && isset($trans_dynamic['field_value'])) {
							$d_type = $trans_dynamic['field_type'];
							$d_label = $trans_dynamic['field_label'];
							$d_value = $trans_dynamic['field_value'];
							if ($d_type == 'single-choice') {
								$d_value = $d_value == 'on' ? __('Yes', 'foodbakery') : __('No', 'foodbakery');
							}
							
							$output .= '<div class="col-md-3"><strong>'.$d_label.'</strong></div><div class="col-md-8">'.$d_value.'</div><br><hr>' . "\n";
						}
					}
	
					$foodbakery_opt_array = array(
						'desc' => '',
					);
					$output .= $foodbakery_html_fields->foodbakery_closing_field($foodbakery_opt_array);
					
					$html .= $output;
				}
				
				break;
			case 'extra_features' :
				// prepare
				$foodbakery_restaurant_ids = get_post_meta($post->ID, "foodbakery_restaurant_ids", true);
				$foodbakery_featured_ids = get_post_meta($post->ID, "foodbakery_featured_ids", true);
				$foodbakery_top_cat_ids = get_post_meta($post->ID, "foodbakery_top_cat_ids", true);
				
				$output = '';
				
				$output .= '<div class="form-elements">';
				
				$foodbakery_post_data = '<div class="col-md-12">';
				if ( is_array($foodbakery_restaurant_ids) && sizeof($foodbakery_restaurant_ids) ) {
					
					$restaurant_counter = 1;
					foreach ( $foodbakery_restaurant_ids as $id ) {
						$foodbakery_permalink = get_the_title($id) ? ' target="_blank" href="' . get_edit_post_link($id) . '"' : '';
						$foodbakery_title = get_the_title($id) ? get_the_title($id) : __('Removed', 'foodbakery');
						$foodbakery_post = '<ul>';
						$foodbakery_post .= '<li><strong></strong>' . __('Restaurant Id', 'foodbakery') . ' : #' . $id . '</li>';
						$foodbakery_post .= '<li>' . __('Restaurant Title', 'foodbakery') . ' : <a' . $foodbakery_permalink . '">' . $foodbakery_title . '</a></li>';
						$foodbakery_post .= '</ul>';
						$foodbakery_post_data .= '<span>' . $foodbakery_post . '</span>';
						$restaurant_counter++;
					}
				} else {
					$foodbakery_post_data .= __('Restaurant not attached yet.', 'foodbakery');
				}
				$foodbakery_post_data .= '</div>';

				$output .= $foodbakery_post_data;
				
				$output .= '</div>';

				$html .= $output;
				break;

			default :
				break;
		}
		return $html;
	}

}
/**
 * End Function  how to Create Transations Fields
 */