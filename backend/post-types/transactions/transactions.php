<?php

// Transactions start
// Adding columns start

/**
 * Start Function  how to Create colume in transactions 
 */
if (!function_exists('transactions_columns_add')) {
    add_filter('manage_foodbakery-trans_posts_columns', 'transactions_columns_add');

    function transactions_columns_add($columns) {
	unset($columns['title']);
	unset($columns['date']);
	$columns['p_title'] = esc_html__('Transaction Id', 'foodbakery');
	$columns['p_date'] = esc_html__('Date', 'foodbakery');
        $columns['restaurant'] = esc_html__('Restaurant', 'foodbakery');
	$columns['users'] = esc_html__('Buyer', 'foodbakery');
	$columns['order_type'] = esc_html__('Order Type', 'foodbakery');
	$columns['gateway'] = esc_html__('Payment Gateway', 'foodbakery');
	$columns['amount'] = esc_html__('Amount', 'foodbakery');
	$columns['comission'] = esc_html__('Commission - for sale', 'foodbakery');
	$columns['author_earnings'] = esc_html__('Author Earnings', 'foodbakery');
	return $columns;
    }

}

/**
 * Start Function  how to Show data in columns
 */
if (!function_exists('transactions_columns')) {
    add_action('manage_foodbakery-trans_posts_custom_column', 'transactions_columns', 10, 2);

    function transactions_columns($name) {
	global $post, $gateways, $foodbakery_plugin_options;
	$general_settings = new FOODBAKERY_PAYMENTS();
	$currency_sign = foodbakery_get_currency_sign();
	$currency_sign = get_post_meta($post->ID, 'foodbakery_currency', true);
	$currency_sign = ( isset($currency_sign) && $currency_sign != '' ) ? $currency_sign : '$';
	$transaction_user = get_post_meta($post->ID, 'foodbakery_transaction_user', true);
	$transaction_amount = get_post_meta($post->ID, 'foodbakery_transaction_amount', true);
	$transaction_fee = get_post_meta($post->ID, 'transaction_fee', true);
	$transaction_status = get_post_meta($post->ID, 'foodbakery_transaction_status', true);
        $transaction_order_id       = get_post_meta( $post->ID, 'foodbakery_transaction_order_id', true );
        $foodbakery_restaurant_id = get_post_meta($transaction_order_id, 'foodbakery_restaurant_id', true);
        $order_type = get_post_meta($post->ID, 'foodbakery_transaction_order_type', true);
        $foodbakery_restaurant_ids = get_post_meta($transaction_order_id, 'foodbakery_restaurant_ids', true);
        $order_restaurant_id    = $foodbakery_restaurant_id;
        $services_total_price = get_post_meta($transaction_order_id, 'services_total_price', true);
        $menu_order_fee = get_post_meta($transaction_order_id, 'menu_order_fee', true);


	// return payment gateway name
	switch ($name) {
	    case 'p_title':

		echo $post->ID; //get_the_title($post->ID);

		break;
            case 'restaurant':
                if( $order_type == 'package-order' ){
                    $order_restaurant_id   = $foodbakery_restaurant_ids[0];
                }
                if( isset( $order_restaurant_id ) && $order_restaurant_id != '' ){
                    echo get_the_title($order_restaurant_id);
                } else {
                    echo '-';
                }
                break;
	    case 'p_date':
		echo get_the_date();
		break;
	    case 'users':
		echo esc_html($transaction_user) != '' ? get_the_title($transaction_user) : '';
		break;
	    case 'order_type':
		if( $order_type == 'package-order' ){
                    echo esc_html( 'Membership Order', 'foodbakery' );
                } else {
                    echo esc_html( 'Order', 'foodbakery' );
                }
		break;
            case 'gateway':
		$foodbakery_trans_gate = get_post_meta(get_the_id(), "foodbakery_transaction_pay_method", true);
		if ($foodbakery_trans_gate != '') {
		    $foodbakery_trans_gate = isset($gateways[strtoupper($foodbakery_trans_gate)]) ? $gateways[strtoupper($foodbakery_trans_gate)] : $foodbakery_trans_gate;

		    $foodbakery_trans_gate = ( isset($foodbakery_trans_gate) && $foodbakery_trans_gate == 'FOODBAKERY_WOOCOMMERCE_GATEWAY' ) ? 'payment cancelled' : $foodbakery_trans_gate;
		    $foodbakery_trans_gate = isset($foodbakery_trans_gate) ? $foodbakery_trans_gate : esc_html__('Nill', 'foodbakery');
		    echo esc_html($foodbakery_trans_gate);
		} 
		$order_with = get_post_meta($post->ID, 'foodbakery_order_with', true);
		if (isset($order_with) && $order_with == 'woocommerce') {
		    $order_id = get_post_meta($post->ID, 'woocommerce_order_id', true);
		    if (isset($order_id) && $order_id != '') {
			echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . get_edit_post_link($order_id) . '">' . esc_html__('Order Detail', 'foodbakery') . '</a>';
		    }
		}
		break;
	    case 'amount':
		$order_with = get_post_meta($post->ID, 'foodbakery_order_with', true);
		if (isset($order_with) && $order_with == 'woocommerce') {
		    $currency_symbol = get_post_meta($post->ID, 'foodbakery_currency', true);
		    if (isset($currency_symbol) && $currency_symbol != '') {
			$currency_sign = $currency_symbol;
		    }
		}
		$foodbakery_trans_amount = get_post_meta(get_the_id(), "foodbakery_transaction_amount", true);
		echo get_post_meta(get_the_id(), "order_subtotal_price", true);
                $transaction_text   = '';
                $transac_type = get_post_meta(get_the_id(), 'foodbakery_transaction_order_charge_type', true);

                if ($transac_type == 'order-charges') {
                    $transaction_text       = ' ('.esc_html__('Commission - for sale','foodbakery').')';
                    $foodbakery_trans_amount = get_post_meta(get_the_id(), 'foodbakery_order_amount_charged', true);
                }
               // $foodbakery_trans_amount =   esc_html($currency_sign) .   restaurant_menu_price_calc('defined', $services_total_price, $menu_order_fee, true, false, false);
		if ($foodbakery_trans_amount != '') {
            //echo esc_attr($currency_sign) . FOODBAKERY_FUNCTIONS()->num_format($foodbakery_trans_amount).esc_html__( $transaction_text );
            $pay_mode = get_post_meta(get_the_id(), 'foodbakery_transaction_pay_method', true);
            if($pay_mode == 'cash'){
               echo restaurant_menu_price_calc('defined', $services_total_price, $menu_order_fee, true, false, false);
            }else{
                echo $foodbakery_trans_amount.esc_html__( $transaction_text);
            }

		} else {
		    echo '-';
		}
		break;
                
            case 'comission':
                $order_with = get_post_meta($post->ID, 'foodbakery_order_with', true);
		if (isset($order_with) && $order_with == 'woocommerce') {
		    $currency_symbol = get_post_meta($post->ID, 'foodbakery_currency', true);
		    if (isset($currency_symbol) && $currency_symbol != '') {
			$currency_sign = $currency_symbol;
		    }
		}
                $transaction_order_id       = get_post_meta( $post->ID, 'foodbakery_transaction_order_id', true );
                $order_amount_charged       = get_post_meta( $transaction_order_id, 'order_amount_charged', true );
		if( $order_amount_charged != '' ){
                    echo '<span style="color:red;">-'.currency_symbol_possitions($order_amount_charged, $currency_sign).'</span>';
                } else {
                    echo '-';
                }
		break;  
            
            case 'author_earnings':
                $order_with = get_post_meta($post->ID, 'foodbakery_order_with', true);
		if (isset($order_with) && $order_with == 'woocommerce') {
		    $currency_symbol = get_post_meta($post->ID, 'foodbakery_currency', true);
		    if (isset($currency_symbol) && $currency_symbol != '') {
			$currency_sign = $currency_symbol;
		    }
		}
		$transaction_order_id       = get_post_meta( $post->ID, 'foodbakery_transaction_order_id', true );
                $foodbakery_trans_amount = get_post_meta(get_the_id(), "foodbakery_transaction_amount", true);
                //$order_amount_credited       = get_post_meta( $transaction_order_id, 'order_amount_credited', true );
               // $foodbakery_trans_amount =   restaurant_menu_price_calc('defined', $services_total_price, $menu_order_fee, true, false, false);
                $order_amount_charged       = get_post_meta( $transaction_order_id, 'order_amount_charged', true );
                if(!is_numeric($services_total_price)){
                    $services_total_price = 0;
                }
                if(!is_numeric($order_amount_charged)){
                    $order_amount_charged = 0;
                }
                $order_amount_credited =  $services_total_price - $order_amount_charged;
		if( $order_amount_credited != '' ){
                    echo  currency_symbol_possitions(FOODBAKERY_FUNCTIONS()->num_format($order_amount_credited), $currency_sign);
                } else {
                    $order_amount_credited       = get_post_meta( $transaction_order_id, 'services_total_price', true );
                    echo  currency_symbol_possitions(FOODBAKERY_FUNCTIONS()->num_format($order_amount_credited), $currency_sign);
                }
                break;
	}
    }

}

/**
 * Start Function  how to Row in columns
 */
if (!function_exists('remove_row_actions')) {
    add_filter('post_row_actions', 'remove_row_actions', 10, 1);

    function remove_row_actions($actions) {
	if (get_post_type() == 'foodbakery-trans') {
	    unset($actions['view']);
	    unset($actions['trash']);
	    unset($actions['inline hide-if-no-js']);
	}
	return $actions;
    }

}


/**
 * Start Function  how create post type of transactions
 */
if (!class_exists('post_type_transactions')) {

    class post_type_transactions {

	// The Constructor
	public function __construct() {
	    add_action('init', array(&$this, 'transactions_init'));
	    add_action('admin_init', array(&$this, 'transactions_admin_init'));
	}

	public function transactions_init() {
	    // Initialize Post Type
	    $this->transactions_register();
	}

	public function transactions_register() {
	    $labels = array(
		'name' => esc_html__('Transactions', 'foodbakery'),
		'menu_name' => esc_html__('Transactions', 'foodbakery'),
		'add_new_item' => esc_html__('Add New Transaction', 'foodbakery'),
		'edit_item' => esc_html__('Edit Transaction', 'foodbakery'),
		'new_item' => esc_html__('New Transaction Item', 'foodbakery'),
		'add_new' => esc_html__('Add New Transaction', 'foodbakery'),
		'view_item' => esc_html__('View Transaction Item', 'foodbakery'),
		'search_items' => esc_html__('Search', 'foodbakery'),
		'not_found' => esc_html__('Nothing found', 'foodbakery'),
		'not_found_in_trash' => esc_html__('Nothing found in Trash', 'foodbakery'),
		'parent_item_colon' => ''
	    );
	    $args = array(
		'labels' => $labels,
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => true,
		'query_var' => false,
		'menu_icon' => 'dashicons-admin-post',
		'show_in_menu' => 'edit.php?post_type=orders_inquiries',
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('')
	    );
	    register_post_type('foodbakery-trans', $args);
	}

	/**
	 * End Function  how create post type of transactions
	 */

	/**
	 * Start Function  how create add meta boxes of transactions
	 */
	public function transactions_admin_init() {
	    // Add metaboxes
	    add_action('add_meta_boxes', array(&$this, 'foodbakery_meta_transactions_add'));
	}

	public function foodbakery_meta_transactions_add() {
	    add_meta_box('foodbakery_meta_transactions', esc_html__('Transaction Options', 'foodbakery'), array(&$this, 'foodbakery_meta_transactions'), 'foodbakery-trans', 'normal', 'high');
	}

	public function foodbakery_meta_transactions($post) {
	    global $gateways, $foodbakery_html_fields, $foodbakery_form_fields, $foodbakery_plugin_options;

	    $foodbakery_users_list = array('' => esc_html__('Select Publisher', 'foodbakery'));
	    $args = array('posts_per_page' => '-1', 'post_type' => 'publishers', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC');
	    $cust_query = get_posts($args);
	    if (is_array($cust_query) && sizeof($cust_query) > 0) {
		foreach ($cust_query as $package_post) {
		    if (isset($package_post->ID)) {
			$package_id = $package_post->ID;
			$package_title = $package_post->post_title;
			$foodbakery_users_list[$package_id] = $package_title;
		    }
		}
	    }

	    $object = new FOODBAKERY_PAYMENTS();
	    $payment_geteways = array();
	    $payment_geteways[''] = esc_html__('Select Payment Gateway', 'foodbakery');
	    $payment_geteways['cash'] = esc_html__('Cash on Delivery', 'foodbakery');
	    $foodbakery_gateway_options = get_option('foodbakery_plugin_options');

	    foreach ($gateways as $key => $value) {
		$status = $foodbakery_gateway_options[strtolower($key) . '_status'];
			if (isset($status) && $status == 'on') {
				$payment_geteways[$key] = $value;
			}
	    }

	    if (isset($foodbakery_gateway_options['foodbakery_use_woocommerce_gateway']) && $foodbakery_gateway_options['foodbakery_use_woocommerce_gateway'] == 'on') {
			if (class_exists('WooCommerce')) {
				unset($payment_geteways);
				$payment_geteways[''] = esc_html__('Select Payment Gateway', 'foodbakery');
				$gateways = WC()->payment_gateways->get_available_payment_gateways();
				foreach ($gateways as $key => $gateway_data) {
				$payment_geteways[$key] = $gateway_data->method_title;
				}
			}
	    }

	    $transaction_meta = array();
	    $transaction_meta['transaction_id'] = array(
		'name' => 'transaction_id',
		'type' => 'hidden_label',
		'title' => esc_html__('Transaction Id', 'foodbakery'),
		'description' => '',
	    );
	    $transaction_meta['transaction_order_id'] = array(
		'name' => 'transaction_order_id',
		'type' => 'hidden_label',
		'title' => esc_html__('Order Id', 'foodbakery'),
		'description' => '',
	    );
	    $transaction_meta['transaction_summary'] = array(
		'name' => 'transaction_summary',
		'type' => 'summary',
		'title' => esc_html__('Summary', 'foodbakery'),
		'description' => '',
	    );
            $transaction_meta['transaction_order_summary'] = array(
		'name' => 'transaction_order_summary',
		'type' => 'order_summary',
		'title' => esc_html__('Order Summary', 'foodbakery'),
		'description' => '',
	    );
	    $transaction_meta['transaction_order_type'] = array(
		'name' => 'transaction_order_type',
		'type' => 'select',
		'classes' => 'chosen-select',
		'title' => esc_html__('Order Type', 'foodbakery'),
		'options' => array('' => esc_html__('Select Type', 'foodbakery'), 'package-order' => esc_html__('Memberships Order', 'foodbakery'), 'reservation-order' => esc_html__('Reservation Order', 'foodbakery')),
		'description' => '',
	    );
	    $transaction_meta['transaction_user'] = array(
		'name' => 'transaction_user',
		'type' => 'select',
		'classes' => 'chosen-select',
		'title' => esc_html__('User', 'foodbakery'),
		'options' => $foodbakery_users_list,
		'description' => '',
	    );
	    $transaction_meta['transaction_amount'] = array(
		'name' => 'transaction_amount',
		'type' => 'text',
		'title' => esc_html__('Amount', 'foodbakery'),
		'description' => '',
	    );
	    $transaction_meta['transaction_pay_method'] = array(
		'name' => 'transaction_pay_method',
		'type' => 'select',
		'classes' => 'chosen-select-no-single',
		'title' => esc_html__('Payment Gateway', 'foodbakery'),
		'options' => $payment_geteways,
		'description' => '',
	    );
	    $transaction_meta['transaction_status'] = array(
		'name' => 'transaction_status',
		'type' => 'select',
		'classes' => 'chosen-select-no-single',
		'title' => esc_html__('Status', 'foodbakery'),
		'options' => array('pending' => esc_html__('Pending', 'foodbakery'), 'in-process' => esc_html__('In Process', 'foodbakery'), 'approved' => esc_html__('Approved', 'foodbakery'), 'cancelled' => esc_html__('Cancelled', 'foodbakery')),
		'description' => '',
	    );

	    $html = '
			<div class="page-wrap">
			<div class="option-sec" style="margin-bottom:0;">
			<div class="opt-conts">
			<div class="foodbakery-review-wrap">';

	    foreach ($transaction_meta as $key => $params) {
		$html .= foodbakery_create_transactions_fields($key, $params);
	    }

	    $html .= '
			</div>
			</div>
			</div>';
	    $foodbakery_opt_array = array(
		'std' => '1',
		'id' => 'transactions_form',
		'cust_name' => 'transactions_form',
		'cust_type' => 'hidden',
		'return' => true,
	    );
	    $html .= $foodbakery_form_fields->foodbakery_form_text_render($foodbakery_opt_array);
	    $html .= '
				<div class="clear"></div>
			</div>';
	    echo force_balance_tags($html);
	}

    }

    /**
     * End Function  how create add meta boxes of transactions
     */
    return new post_type_transactions();
}