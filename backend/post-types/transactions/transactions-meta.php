<?php

/**
 * Start Function  how to Create Transations Fields
 */
if (!function_exists('foodbakery_create_transactions_fields')) {

    function foodbakery_create_transactions_fields($key, $param=array()) {
	global $post, $foodbakery_html_fields, $foodbakery_form_fields, $foodbakery_plugin_options;
	$foodbakery_gateway_options = get_option('foodbakery_plugin_options');
	$foodbakery_currency_sign = foodbakery_get_currency_sign();
	$foodbakery_value = '';
	$foodbakery_value = $param['title'];
	$html = '';
	switch ($param['type']) {
	    case 'text' :
		// prepare
		$foodbakery_value = get_post_meta($post->ID, 'foodbakery_' . $key, true);

		if (isset($foodbakery_value) && $foodbakery_value != '') {
		    if ($key == 'transaction_expiry_date') {
			$foodbakery_value = date_i18n('d-m-Y', $foodbakery_value);
		    }/* else if ($key == 'transaction_amount') {
			$foodbakery_currency = get_post_meta($post->ID, 'foodbakery_currency', true);
			$foodbakery_value = $foodbakery_currency . $foodbakery_value;
		    } */else {
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
		if (isset($foodbakery_value) && $foodbakery_value != '') {
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
		if (isset($foodbakery_value) && $foodbakery_value != '') {
		    $foodbakery_value = $foodbakery_value;
		} else {
		    $foodbakery_value = '';
		}
		$foodbakery_classes = '';
		if (isset($param['classes']) && $param['classes'] != "") {
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

		if (isset($foodbakery_value) && $foodbakery_value != '') {
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

	    case 'summary' :
		// prepare
		$trans_first_name = get_post_meta($post->ID, 'foodbakery_trans_first_name', true);
		$trans_last_name = get_post_meta($post->ID, 'foodbakery_trans_last_name', true);
		$trans_email = get_post_meta($post->ID, 'foodbakery_trans_email', true);
		$trans_phone_number = get_post_meta($post->ID, 'foodbakery_trans_phone_number', true);
		$trans_address = get_post_meta($post->ID, 'foodbakery_trans_address', true);

		$output = '';

		if ($trans_first_name != '' || $trans_last_name != '' || $trans_email != '' || $trans_phone_number != '' || $trans_address != '') {

		    $foodbakery_opt_array = array(
			'name' => $param['title'],
			'hint_text' => '',
		    );
		    $output .= $foodbakery_html_fields->foodbakery_opening_field($foodbakery_opt_array);

		    $output .= '<ul class="trans-user-summary">';

		    if ($trans_first_name != '') {
			$output .= '<li>';
			$output .= '<label>' . esc_html__('First Name', 'foodbakery') . '</label><span>' . $trans_first_name . '</span>';
			$output .= '</li>';
		    }
		    if ($trans_last_name != '') {
			$output .= '<li>';
			$output .= '<label>' . esc_html__('Last Name', 'foodbakery') . '</label><span>' . $trans_last_name . '</span>';
			$output .= '</li>';
		    }
		    if ($trans_email != '') {
			$output .= '<li>';
			$output .= '<label>' . esc_html__('Email', 'foodbakery') . '</label><span>' . $trans_email . '</span>';
			$output .= '</li>';
		    }
		    if ($trans_phone_number != '') {
			$output .= '<li>';
			$output .= '<label>' . esc_html__('Phone Number', 'foodbakery') . '</label><span>' . $trans_phone_number . '</span>';
			$output .= '</li>';
		    }
		    if ($trans_address != '') {
			$output .= '<li>';
			$output .= '<label>' . esc_html__('Address', 'foodbakery') . '</label><span>' . $trans_address . '</span>';
			$output .= '</li>';
		    }

		    $output .= '<ul>';

		    $foodbakery_opt_array = array(
			'desc' => '',
		    );
		    $output .= $foodbakery_html_fields->foodbakery_closing_field($foodbakery_opt_array);
		}

		$html .= $output;
		break;
                
                
             case 'order_summary' :
		// prepare
		$trans_first_name = get_post_meta($post->ID, 'foodbakery_trans_first_name', true);
		$trans_last_name = get_post_meta($post->ID, 'foodbakery_trans_last_name', true);
		$trans_email = get_post_meta($post->ID, 'foodbakery_trans_email', true);
		$trans_phone_number = get_post_meta($post->ID, 'foodbakery_trans_phone_number', true);
		$trans_address = get_post_meta($post->ID, 'foodbakery_trans_address', true);
                
                
                $wooc_order_all_data    = get_post_meta($post->ID, 'foodbakery_wooc_order_data', true);

		$output = '';

		if ($trans_first_name != '' || $trans_last_name != '' || $trans_email != '' || $trans_phone_number != '' || $trans_address != '') {

		    $foodbakery_opt_array = array(
			'name' => $param['title'],
			'hint_text' => '',
		    );
		    $output .= $foodbakery_html_fields->foodbakery_opening_field($foodbakery_opt_array);

		    $output .= '<ul class="trans-user-summary">';
                    
                    
                    if( isset( $wooc_order_all_data ) && !empty( $wooc_order_all_data ) ){
                        foreach( $wooc_order_all_data as $wooc_order_data ){
                            $output .= '<li>';
                            $output .= '<label>' . esc_html__(strip_tags($wooc_order_data['label'])) . '</label><span>' . esc_html__(strip_tags($wooc_order_data['value'])) . '</span>';
                            $output .= '</li>';
                        }
                    }

		    $output .= '<ul>';

		    $foodbakery_opt_array = array(
			'desc' => '',
		    );
		    $output .= $foodbakery_html_fields->foodbakery_closing_field($foodbakery_opt_array);
		}

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