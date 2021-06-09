<?php

/**
 * File Type: Reservations Post Type
 */
if ( ! class_exists('post_type_withdrawals') ) {

	class post_type_withdrawals {

		/**
		 * Start Contructer Function
		 */
		public function __construct() {
			add_action('init', array( &$this, 'foodbakery_withdrawals_register' ), 12);
			add_filter('manage_withdrawals_posts_columns', array( &$this, 'withdrawals_columns_add' ), 10, 1);
			add_action('manage_withdrawals_posts_custom_column', array( &$this, 'withdrawals_columns' ), 10, 2);
			
		}

		public function withdrawals_remove_row_actions($actions, $post) {
			if ( $post->post_type == 'withdrawals' ) {
				unset($actions['view']);
			}
			return $actions;
		}

		public function withdrawals_columns_add($columns) {
			unset($columns['title']);
			unset($columns['date']);
			$columns['p_title'] = esc_html__('Withdrawal Id', 'foodbakery');
			$columns['p_date'] = esc_html__('Date', 'foodbakery');
			$columns['users'] = esc_html__('User', 'foodbakery');
			$columns['amount'] = esc_html__('Amount', 'foodbakery');
			$columns['status'] = esc_html__('Status', 'foodbakery');
			return $columns;
		}

		public function withdrawals_columns($name) {
			global $post, $withdrawals, $foodbakery_plugin_options;
			$foodbakery_publisher = get_post_meta($post->ID, 'foodbakery_withdrawal_user', true);
                        $currency_sign = get_post_meta( $post->ID, 'foodbakery_currency', true );
                        $currency_sign  = ( isset( $currency_sign  ) && $currency_sign != '' )? $currency_sign : '$';
			switch ( $name ) {
				case 'p_title':
					echo '#' . $post->ID;
					break;
				case 'p_date':
					echo get_the_date();
					break;
				case 'users':
					echo get_the_title($foodbakery_publisher);
					break;
				case 'amount':
					$withdrawal_amount = get_post_meta($post->ID, 'withdrawal_amount', true);
					echo (foodbakery_get_currency($withdrawal_amount, true, '', '', false ));
					break;
				case 'status':
					echo get_post_meta($post->ID, 'foodbakery_withdrawal_status', true);
					break;
			}
		}

		/**
		 * Start Wp's Initilize action hook Function
		 */
		public function foodbakery_withdrawals_init() {
			// Initialize Post Type
			$this->foodbakery_withdrawals_register();
		}

		/**
		 * Start Function How to Register post type
		 */
		public function foodbakery_withdrawals_register() {
			$labels = array(
				'name' => _x('Withdrawals', 'post type general name', 'foodbakery'),
				'singular_name' => _x('Withdrawal', 'post type singular name', 'foodbakery'),
				'menu_name' => _x('Withdrawals', 'admin menu', 'foodbakery'),
				'name_admin_bar' => _x('Withdrawal', 'add new on admin bar', 'foodbakery'),
				'add_new' => _x('Add New', 'withdrawal', 'foodbakery'),
				'add_new_item' => esc_html__('Add New Withdrawal', 'foodbakery'),
				'new_item' => esc_html__('New Withdrawal', 'foodbakery'),
				'edit_item' => esc_html__('Edit Withdrawal', 'foodbakery'),
				'view_item' => esc_html__('View Withdrawal', 'foodbakery'),
				'all_items' => esc_html__('Withdrawals', 'foodbakery'),
				'search_items' => esc_html__('Search Withdrawals', 'foodbakery'),
				'parent_item_colon' => esc_html__('Parent Withdrawals:', 'foodbakery'),
				'not_found' => esc_html__('No withdrawals found.', 'foodbakery'),
				'not_found_in_trash' => esc_html__('No withdrawals found in Trash.', 'foodbakery')
			);

			$args = array(
				'labels' => $labels,
				'description' => esc_html__('Description', 'foodbakery'),
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'menu_position' => 29,
				'show_in_menu' => 'edit.php?post_type=orders_inquiries',
				'query_var' => false,
				'rewrite' => array( 'slug' => 'withdrawals' ),
				'capability_type' => 'post',
				'has_archive' => false,
				'hierarchical' => false,
				'exclude_from_search' => true,
				
				'supports' => array( 'title' ),
			);

			register_post_type('withdrawals', $args);
		}

		// End of class	
	}

	// Initialize Object
	$withdrawals_object = new post_type_withdrawals();
}

// End  analytic for order inquiries