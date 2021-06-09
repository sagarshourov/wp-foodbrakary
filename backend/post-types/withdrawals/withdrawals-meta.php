<?php

/**
 * File Type: Memberships Post Type Metas
 */
if (!class_exists('withdrawals_post_type_meta')) {

    class withdrawals_post_type_meta {

	/**
	 * Start Contructer Function
	 */
	public function __construct() {
	    add_action('add_meta_boxes', array(&$this, 'withdrawals_add_meta_boxes_callback'));
	}

	/**
	 * Add meta boxes Callback Function
	 */
	public function withdrawals_add_meta_boxes_callback() {
	    add_meta_box('foodbakery_meta_withdrawals', esc_html__('Withdrawal Info', 'foodbakery'), array($this, 'foodbakery_meta_withdrawals'), 'withdrawals', 'normal', 'high');
	}

	public function foodbakery_meta_withdrawals() {
	    global $post, $withdrawals;

	    $foodbakery_publishers_list = array();
	    $currency_sign = get_post_meta($post->ID, 'foodbakery_currency', true);
	    $args = array('posts_per_page' => '-1', 'post_type' => 'publishers', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
	    $cust_query = get_posts($args);
	    if (is_array($cust_query) && sizeof($cust_query) > 0) {
		foreach ($cust_query as $package_post) {
		    if (isset($package_post->ID)) {
			$package_id = $package_post->ID;
			$package_title = $package_post->post_title;
			$foodbakery_publishers_list[$package_id] = $package_title;
		    }
		}
	    }

	    $withdrawals_meta = array();

	    $withdrawals_meta['withdrawal_id'] = array(
		'name' => 'withdrawal_id',
		'type' => 'hidden_label',
		'title' => esc_html__('Withdrawal Id', 'foodbakery'),
		'description' => '',
	    );

	    $withdrawals_meta['withdrawal_user'] = array(
		'name' => 'withdrawal_user',
		'type' => 'select',
		'classes' => 'chosen-select',
		'title' => esc_html__('User', 'foodbakery'),
		'options' => $foodbakery_publishers_list,
		'description' => '',
	    );

	    $withdrawals_meta['withdrawal_amount'] = array(
		'name' => 'withdrawal_amount',
		'type' => 'text',
		'title' => esc_html__('Amount', 'foodbakery') . ' (' . $currency_sign . ')',
		'description' => '',
		'active' => 'in-active',
	    );

	    $withdrawals_meta['withdrawal_detail'] = array(
		'name' => 'withdrawal_detail',
		'type' => 'textarea',
		'title' => esc_html__('Detail', 'foodbakery'),
		'description' => '',
	    );

	    $withdrawals_meta['withdrawal_status'] = array(
		'name' => 'withdrawal_status',
		'type' => 'select',
		'classes' => 'chosen-select',
		'title' => esc_html__('Status', 'foodbakery'),
		'options' => array(
		    'pending' => esc_html__('Pending', 'foodbakery'),
		    'approved' => esc_html__('Approved', 'foodbakery'),
		    'cancelled' => esc_html__('Cancelled', 'foodbakery'),
		),
		'description' => '',
	    );

	    $html = '
			<div class="page-wrap">
				<div class="option-sec" style="margin-bottom:0;">
					<div class="opt-conts">
						<div class="foodbakery-review-wrap">';
	    foreach ($withdrawals_meta as $key => $params) {
		$html .= $this->foodbakery_create_withdrawals_fields($key, $params);
	    }
	    $html .= '</div>
							</div>
						</div>
						<div class="clear"></div>
					</div>';
	    echo force_balance_tags($html);
	}

	public function foodbakery_create_withdrawals_fields($key, $param=array()) {
	    global $post, $foodbakery_html_fields, $foodbakery_form_fields, $foodbakery_plugin_options;
	    $foodbakery_currency_sign = foodbakery_get_currency_sign();
	    $foodbakery_value = $param['title'];
	    $html = '';
	    switch ($param['type']) {
		case 'text' :
		    // prepare
		    $foodbakery_value = get_post_meta($post->ID, $key, true);

		    if (isset($foodbakery_value) && $foodbakery_value != '') {
			if ($key == 'foodbakery_withdrawal_date') {
			    $foodbakery_value = date_i18n('d-m-Y', $foodbakery_value);
			} else {
			    $foodbakery_value = $foodbakery_value;
			}
		    } else {
			$foodbakery_value = isset($param['std']) ? $param['std'] : '';
		    }

		    if ($key == 'withdrawal_amount') {
			$foodbakery_value = $foodbakery_value;
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
		default :
		    break;
	    }
	    return $html;
	}

    }

    // Initialize Object
    $withdrawals_meta_object = new withdrawals_post_type_meta();
}