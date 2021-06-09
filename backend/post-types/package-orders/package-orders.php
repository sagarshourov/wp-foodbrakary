<?php

// Membership Orders start
// Adding columns start

/**
 * Start Function  how to Create colume in transactions 
 */
if ( ! function_exists('package_orders_columns_add') ) {
	add_filter('manage_package-orders_posts_columns', 'package_orders_columns_add');

	function package_orders_columns_add($columns) {
		unset($columns['title']);
		unset($columns['date']);
		$columns['p_title'] = __('Membership Id', 'foodbakery');
		$columns['p_date'] = __('Date', 'foodbakery');
		$columns['users'] = __('Publisher', 'foodbakery');
		$columns['package'] = __('Membership Name', 'foodbakery');
		$columns['amount'] = __('Amount', 'foodbakery');
		return $columns;
	}

}

/**
 * Start Function  how to Show data in columns
 */
if ( ! function_exists('package_orders_columns') ) {
	add_action('manage_package-orders_posts_custom_column', 'package_orders_columns', 10, 2);

	function package_orders_columns($name) {
		global $post, $gateways, $foodbakery_plugin_options;
		$general_settings = new FOODBAKERY_PAYMENTS();
		$currency_sign = foodbakery_get_currency_sign();
		$currency_sign = get_post_meta($post->ID, 'foodbakery_currency', true);
		$currency_sign = ( isset($currency_sign) && $currency_sign != '' ) ? $currency_sign : '$';
		$transaction_user = get_post_meta($post->ID, 'foodbakery_transaction_user', true);
		$transaction_amount = get_post_meta($post->ID, 'foodbakery_transaction_amount', true);
		$transaction_fee = get_post_meta($post->ID, 'transaction_fee', true);
		$transaction_status = get_post_meta($post->ID, 'foodbakery_transaction_status', true);

		// return payment gateway name
		switch ( $name ) {
			case 'p_title':
				echo get_the_title($post->ID);
				break;
			case 'p_date':
				echo get_the_date();
				break;
			case 'users':
				echo esc_html($transaction_user) != '' ? get_the_title($transaction_user) : '';
				break;
			case 'package':
				$foodbakery_trans_type = get_post_meta(get_the_id(), "foodbakery_transaction_type", true);

				$foodbakery_trans_pkg = get_post_meta(get_the_id(), "foodbakery_transaction_package", true);
				$foodbakery_trans_pkg_title = get_the_title($foodbakery_trans_pkg);

				if ( $foodbakery_trans_pkg_title != '' ) {
					echo FOODBAKERY_FUNCTIONS()->special_chars($foodbakery_trans_pkg_title);
				} else {
					echo '-';
				}
				break;
			case 'amount':
				$foodbakery_trans_amount = get_post_meta(get_the_id(), "foodbakery_transaction_amount", true);
				if ( $foodbakery_trans_amount != '' ) {
					echo currency_symbol_possitions(FOODBAKERY_FUNCTIONS()->num_format($foodbakery_trans_amount), $currency_sign);
				} else {
					echo '-';
				}
				break;
		}
	}

}

/**
 * Start Function  how to Row in columns
 */
if ( ! function_exists('remove_row_actions') ) {
	add_filter('post_row_actions', 'remove_row_actions', 10, 1);

	function remove_row_actions($actions) {
		if ( get_post_type() == 'package-orders' ) {
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
if ( ! class_exists('post_type_package_orders') ) {

	class post_type_package_orders {

		// The Constructor
		public function __construct() {
			add_action('init', array( &$this, 'transactions_init' ));
			add_action('admin_init', array( &$this, 'transactions_admin_init' ));
		}

		public function transactions_init() {
			// Initialize Post Type
			$this->transactions_register();
		}

		public function transactions_register() {
			$labels = array(
				'name' => __('Membership Orders', 'foodbakery'),
				'menu_name' => __('Membership Orders', 'foodbakery'),
				'add_new_item' => __('Add New Membership Order', 'foodbakery'),
				'edit_item' => __('Edit Membership Order', 'foodbakery'),
				'new_item' => __('New Membership Order Item', 'foodbakery'),
				'add_new' => __('Add New Membership Order', 'foodbakery'),
				'view_item' => __('View Membership Order Item', 'foodbakery'),
				'search_items' => __('Search', 'foodbakery'),
				'not_found' => __('Nothing found', 'foodbakery'),
				'not_found_in_trash' => __('Nothing found in Trash', 'foodbakery'),
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
				'supports' => array( '' )
			);
			register_post_type('package-orders', $args);
		}

		/**
		 * End Function  how create post type of transactions
		 */

		/**
		 * Start Function  how create add meta boxes of transactions
		 */
		public function transactions_admin_init() {
			// Add metaboxes
			add_action('add_meta_boxes', array( &$this, 'foodbakery_meta_transactions_add' ));
		}

		public function foodbakery_meta_transactions_add() {
			add_meta_box('foodbakery_meta_transactions', __('Membership Order Options', 'foodbakery'), array( &$this, 'foodbakery_meta_transactions' ), 'package-orders', 'normal', 'high');
		}

		public function foodbakery_meta_transactions($post) {
			global $foodbakery_html_fields, $foodbakery_form_fields, $foodbakery_plugin_options;
			
			$foodbakery_users_list = array( '' => __('Select Publisher', 'foodbakery') );
			$args = array( 'posts_per_page' => '-1', 'post_type' => 'publishers', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC' );
			$cust_query = get_posts($args);
			if ( is_array($cust_query) && sizeof($cust_query) > 0 ) {
				foreach ( $cust_query as $package_post ) {
					if ( isset($package_post->ID) ) {
						$package_id = $package_post->ID;
						$package_title = $package_post->post_title;
						$foodbakery_users_list[$package_id] = $package_title;
					}
				}
			}
			
			$foodbakery_packages_list = array( '' => __('Select Membership', 'foodbakery') );
			$args = array( 'posts_per_page' => '-1', 'post_type' => 'packages', 'orderby' => 'title', 'post_status' => 'publish', 'order' => 'ASC' );
			$cust_query = get_posts($args);
			if ( is_array($cust_query) && sizeof($cust_query) > 0 ) {
				foreach ( $cust_query as $package_post ) {
					if ( isset($package_post->ID) ) {
						$package_id = $package_post->ID;
						$package_title = $package_post->post_title;
						$foodbakery_packages_list[$package_id] = $package_title;
					}
				}
			}		

			$foodbakery_trans_type = get_post_meta(get_the_id(), "foodbakery_transaction_type", true);

			$transaction_meta = array();
			$transaction_meta['transaction_id'] = array(
				'name' => 'transaction_id',
				'type' => 'hidden_label',
				'title' => __('Membership Order Id', 'foodbakery'),
				'description' => '',
			);
			$transaction_meta['transaction_user'] = array(
				'name' => 'transaction_user',
				'type' => 'select',
				'classes' => 'chosen-select',
				'title' => __('Membership User', 'foodbakery'),
				'options' => $foodbakery_users_list,
				'description' => '',
			);

			$transaction_meta['transaction_package'] = array(
				'name' => 'transaction_package',
				'type' => 'select',
				'classes' => 'chosen-select-no-single',
				'title' => __('Membership', 'foodbakery'),
				'options' => $foodbakery_packages_list,
				'description' => '',
			);
			$transaction_meta['transaction_amount'] = array(
				'name' => 'transaction_amount',
				'type' => 'text',
				'title' => __('Amount', 'foodbakery'),
				'description' => '',
			);
			$transaction_meta['transaction_expiry_date'] = array(
				'name' => 'transaction_expiry_date',
				'type' => 'text',
				'title' => __('Membership Expiry Date', 'foodbakery'),
				'description' => '',
			);

			$transaction_meta['transaction_restaurant_expiry'] = array(
				'name' => 'transaction_restaurant_expiry',
				'type' => 'text',
				'title' => __('Restaurant Expiry', 'foodbakery'),
				'description' => '',
			);
			
			
			$transaction_meta['transaction_restaurant_tags_num'] = array(
				'name' => 'transaction_restaurant_tags_num',
				'type' => 'text',
				'title' => __('Number of Tags', 'foodbakery'),
				'description' => '',
			);
			
			$transaction_meta['transaction_restaurant_reviews'] = array(
				'name' => 'transaction_restaurant_reviews',
				'type' => 'checkbox',
				'title' => __('Reviews', 'foodbakery'),
				'description' => '',
			);
			
			$transaction_meta['transaction_restaurant_feature_list'] = array(
				'name' => 'transaction_restaurant_feature_list',
				'type' => 'checkbox',
				'title' => __('Featured Restaurant', 'foodbakery'),
				'description' => '',
			);
			
			$transaction_meta['transaction_restaurant_top_cat_list'] = array(
				'name' => 'transaction_restaurant_top_cat_list',
				'type' => 'checkbox',
				'title' => __('Top Categories Restaurant', 'foodbakery'),
				'description' => '',
			);
			
			$transaction_meta['transaction_restaurant_phone'] = array(
				'name' => 'transaction_restaurant_phone',
				'type' => 'checkbox',
				'title' => __('Phone Number', 'foodbakery'),
				'description' => '',
			);
			
			$transaction_meta['transaction_restaurant_website'] = array(
				'name' => 'transaction_restaurant_website',
				'type' => 'checkbox',
				'title' => __('Website Link', 'foodbakery'),
				'description' => '',
			);
			
			$transaction_meta['transaction_restaurant_social'] = array(
				'name' => 'transaction_restaurant_social',
				'type' => 'checkbox',
				'title' => __('Social Impressions Reach', 'foodbakery'),
				'description' => '',
			);
			
			$transaction_meta['transaction_restaurant_ror'] = array(
				'name' => 'transaction_restaurant_ror',
				'type' => 'checkbox',
				'title' => __('Respond to Reviews', 'foodbakery'),
				'description' => '',
			);
			
			$transaction_meta['transaction_status'] = array(
				'name' => 'transaction_status',
				'type' => 'select',
				'classes' => 'chosen-select-no-single',
				'title' => __('Status', 'foodbakery'),
				'options' => array('pending' => __('Pending', 'foodbakery'), 'approved' => __('Approved', 'foodbakery')),
				'description' => '',
			);
			
			$transaction_meta['transaction_restaurant_dynamic'] = array(
				'name' => 'transaction_restaurant_dynamic',
				'type' => 'trans_dynamic',
				'title' => __('Dynamic Fields', 'foodbakery'),
				'description' => '',
			);
			
			$transaction_meta['transaction_ex_features'] = array(
				'type' => 'extra_features',
				'title' => __('Restaurant', 'foodbakery'),
			);

			$html = '<div class="page-wrap">
						<div class="option-sec" style="margin-bottom:0;">
							<div class="opt-conts">
								<div class="foodbakery-review-wrap">
									<script type="text/javascript">
										jQuery(function(){
											jQuery("#foodbakery_transaction_expiry_date").datetimepicker({
												format:"d-m-Y",
												timepicker:false
											});
										});
									</script>';
			foreach ( $transaction_meta as $key => $params ) {
				$html .= foodbakery_create_package_orders_fields($key, $params);
			}

			$html .= '</div>
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
	return new post_type_package_orders();
}